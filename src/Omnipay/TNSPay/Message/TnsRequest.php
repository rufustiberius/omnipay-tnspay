<?php

namespace Omnipay\TNSPay\Message;

use DOMDocument;
use SimpleXMLElement;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\TNSPay\CreditCard;

/**
 * TNSPay Purchase Request
 */
abstract class TnsRequest extends AbstractRequest
{

    /**
     * The TNSPay API Version is periodically updated.
     *
     * Documentation is available at https://secure.na.tnspayments.com/api/documentation/apiDocumentation/rest-json/index.html?locale=en_US
     */
    const TNSPAY_API_VERSION_NUMBER = 39;

    /**
     * TNSPay Api base url
     */
    const TNSPAY_BASE_URL = 'https://secure.na.tnspayments.com/';

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

    public function setOrderId($value)
    {
        return $this->setParameter('orderId', $value);
    }

    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    /**
     * @return PaymentPlanBag
     */
    public function getPaymentPlans()
    {
        return $this->getParameter('paymentPlans');
    }

    public function setPaymentPlans($value)
    {
        return $this->setParameter('paymentPlans', $value);
    }


    /**
     * Get the card.
     *
     * @return CreditCard
     */
    public function getCard()
    {
        return $this->getParameter('card');
    }

    /**
     * Sets the card.
     *
     * @param CreditCard $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setCard($value)
    {
        if ($value && !$value instanceof CreditCard) {
            $value = new CreditCard($value);
        }

        return $this->setParameter('card', $value);
    }

    /**
     * Get the data to be sent to TNSPay.
     *
     * @return array
     */
    public abstract function getData();


    /**
     * TNSPay requires an OrderID and a TransactionID
     *
     * At the moment, only a transaction ID is available from OmniPay, so this
     * driver will use the transaction id for both elements.
     *
     * @todo Determine the mechanism for live and test mode.
     */
    protected abstract function getEndpoint();


    /**
     * Send the data to TNSPay
     *
     * @param array $data
     * @return \Omnipay\Common\Message\ResponseInterface|Response
     */
    public abstract function sendData($data);


}
