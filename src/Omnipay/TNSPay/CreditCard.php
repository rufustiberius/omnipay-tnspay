<?php

namespace Omnipay\TNSPay;

class CreditCard extends \Omnipay\Common\CreditCard
{

    public function toArray()
    {
        return $this->parameters->all();
    }

}