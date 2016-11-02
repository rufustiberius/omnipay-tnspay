<?php

namespace Omnipay\TNSPay;

class CreditCard extends \Omnipay\Common\CreditCard
{

    /**
     * Get Card's Holder Name.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->getParameter('firstName');
    }

    /**
     * Set Card's Holder Name
     *
     * @param string $value Parameter value
     * @return CreditCard provides a fluent interface.
     */
    public function setFirstName($value)
    {
        return $this->setParameter('name', $value);
    }


    /**
     * Get Card's Holder Name.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->getParameter('lastName');
    }

    /**
     * Set Card's Holder Name
     *
     * @param string $value Parameter value
     * @return CreditCard provides a fluent interface.
     */
    public function setLastName($value)
    {
        return $this->setParameter('lastName', $value);
    }

    public function toArray()
    {
        return $this->parameters->all();
    }

}