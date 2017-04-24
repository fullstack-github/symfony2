<?php
namespace Dusk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;

/**
 * Pages
 * @ORM\Entity
 * @ORM\Table(name="paypal_order")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\OrderRepository")
 */
class Order
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /** @ORM\OneToOne(targetEntity="JMS\Payment\CoreBundle\Entity\PaymentInstruction") */
    protected $paymentInstruction;

    /** @ORM\Column(type="string", unique = true) */
    protected $orderNumber;

    /** @ORM\Column(type="decimal", precision = 2) */
    protected $amount;
    
    /**
     * @ORM\OneToOne(targetEntity="Room", mappedBy="order")
     */
    protected $order;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }
    
    public function __construct($amount, $orderNumber)
    {
        $this->amount = $amount;
        $this->orderNumber = $orderNumber;
    }

    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getPaymentInstruction()
    {
        return $this->paymentInstruction;
    }

    public function setPaymentInstruction(PaymentInstruction $instruction)
    {
        $this->paymentInstruction = $instruction;
    }


    /**
     * Set orderNumber
     *
     * @param string $orderNumber
     * @return Order
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
    
        return $this;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return Order
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    
        return $this;
    }

    /**
     * Set order
     *
     * @param \Dusk\UserBundle\Entity\Room $order
     * @return Order
     */
    public function setOrder(\Dusk\UserBundle\Entity\Room $order = null)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return \Dusk\UserBundle\Entity\Room 
     */
    public function getOrder()
    {
        return $this->order;
    }
}