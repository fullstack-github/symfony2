<?php

namespace Dusk\UserBundle\Twig;

class HtmlExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('htmlentity', array($this, 'htmlentityFilter')),
        );
    }

    public function htmlentityFilter($html, $number=null)
    {
        $str = html_entity_decode($html);
        if($number and strlen($str) > $number) {
            echo substr($str, 0, $number).'...';
            return;
        }
         echo $str;
         return;
    }

    public function getName()
    {
        return 'dusk_html_entity';
    }
}