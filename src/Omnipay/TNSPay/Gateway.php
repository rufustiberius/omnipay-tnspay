<?php

namespace Omnipay\TNSPay;

use Omnipay\TNSPay\Message\CompletePurchaseRequest;
use Omnipay\TNSPay\Message\PurchaseRequest;
use Omnipay\Common\AbstractGateway;

/**
 * TNSPay Gateway
 *
 * @link https://www.datacash.com/developersarea.php
 */
class Gateway extends AbstractGateway
{

    public function __construct()
    {
        parent::__construct();
        $this->httpClient->setSslVerification(false);
    }

    public function getName()
    {
        return 'TNSPay';
    }

    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'password' => '',
            'testMode' => false
        );
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\TNSPay\Message\PurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\TNSPay\Message\CompletePurchaseRequest', $parameters);
    }
}
