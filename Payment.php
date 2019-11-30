<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="payment",
 *	indexes={
 *		@ORM\Index(name="date", columns={"date"})
 *	})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PaymentRepository")
 */
class Payment
{
	const METHOD_CREDIT = 1;
	const METHOD_CASH   = 2;
	const METHOD_CHECK  = 3;
	const METHOD_PROMO  = 4;

	static public $methodDescriptions = array(
		self::METHOD_CREDIT => 'Credit Card',
		self::METHOD_CASH   => 'Cash',
		self::METHOD_CHECK  => 'Check',
		self::METHOD_PROMO  => 'Promo Code'
	);

	/**
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $user;

	/**
	 * @ORM\Column(name="amount", type="integer")
	 */
	private $amount;

  	/**
	 * @ORM\Column(type="datetime", nullable=true, name="date")
	 */
	private $date;

	/**
	 * @ORM\Column(name="method", type="integer", nullable=true)
	 */
	private $method;

	/**
	 * @ORM\Column(name="promo_code", type="string", length=255, nullable=true)
	 */
	private $promoCode;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Payment
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set method
     *
     * @param integer $method
     *
     * @return Payment
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return integer
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set promoCode
     *
     * @param string $promoCode
     *
     * @return Payment
     */
    public function setPromoCode($promoCode)
    {
        $this->promoCode = $promoCode;

        return $this;
    }

    /**
     * Get promoCode
     *
     * @return string
     */
    public function getPromoCode()
    {
        return $this->promoCode;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Payment
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
