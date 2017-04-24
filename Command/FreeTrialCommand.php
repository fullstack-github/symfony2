<?php

namespace Dusk\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use \DateTime;

class FreeTrialCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('dusk:calculate-free-trail')->setDescription('Cron to calculate free trial days.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $freeDays = $this->getContainer()->getParameter('freeDays');

        $users = $em->getRepository('DuskUserBundle:User')->createQueryBuilder('u')
                ->where('u.isFreePeriod = :free')
                ->setParameter('free', 1)
                ->getQuery()
                ->execute();

        $expiredAt = new DateTime();

        foreach ($users as $user) {

            if ($user->hasRole('ROLE_ADMIN') or $user->hasRole('ROLE_USER')) {

                $startedAt = $user->getCreatedAt();
                $startedAt->modify("+" . $freeDays . " day");
                $interval = $startedAt->diff($expiredAt);

                if ($interval->d == 1) {
                    
                    // send email to admin
                    if ($user->hasRole('ROLE_ADMIN')) {
                        $message = \Swift_Message::newInstance()
                                ->setSubject('[DUSK]: Your free trial about to finish')
                                ->setFrom('contact@dusk.com')
                                ->setTo($user->getEmail())
                                ->setBody($this->renderView('DuskUserBundle:Email:freeTrail.txt.twig', array('username' => $user->getUsername())))
                                ->setContentType('text/html');
                        $this->get('mailer')->send($message);
                    }
                }

                if ($expiredAt >= $startedAt) {
                    $user->setIsFreePeriod(false);
                    $em->persist($user);
                    $em->flush();

                    $output->writeln("User '" . $user->getUsername() . "' has finished his free trial.");
                }
            }
        }
    }

}
