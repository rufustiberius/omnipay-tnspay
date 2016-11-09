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
            'paymentPlans' => '',
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

    public function getPaymentPlans()
    {
        return $this->getParameter('paymentPlans');
    }

    public function setPaymentPlans($value)
    {
        return $this->setParameter('paymentPlans', $value);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\TNSPay\Message\PurchaseRequest', $parameters);
    }

    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\TNSPay\Message\CardRequest', $parameters);
    }

    public function retrieve(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\TNSPay\Message\RetrieveRequest', $parameters);
    }

}
