<?php

namespace Omnipay\TNSPay\Message;

use DOMDocument;
use Guzzle\Common\Exception\InvalidArgumentException;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Exception\BadResponseException;
use SimpleXMLElement;

/**
 * TNSPay Purchase Request
 */
class PurchaseRequest extends TnsRequest
{

    /**
     * @var int $installments
     */
    protected $installments=1;

    /**
     * Get the data to be sent to TNSPay.
     *
     * @return array
     */
    public function getData()
    {
        $this->validate('amount', 'transactionId', 'clientIp');
        //$this->getCard()->validate();
        $cardReference =$this->getCardReference();

        $data = array(
            'apiOperation'  => 'PAY',
            'transaction' => array (
                'acquirer' => array (
                    'transactionId' => $this->getTransactionId()
                ),
                'reference' => $this->getTransactionId(),
                'source' => 'INTERNET',
                'frequency' => 'SINGLE'
            ),
            'order'         => array(
                'reference' => $this->getTransactionId(),
                'amount'    => $this->getAmount(),
                'currency'  => $this->getCurrency(),
            ),
            'sourceOfFunds' => $this->getSourceOfFunds($this->getCard(), $cardReference),
            'device' => array ('ipAddress' => $this->getClientIp())
        );

        if($this->installments > 1 ) {
            $data['paymentPlan'] = $this->getInstallmentsData();
        }

        //error_log(print_r($data, true), 3, '/tmp/purchase_request.log');
        //die;
        return $data;
    }

    public function getInstallmentsData()
    {
        $paymentPlans = $this->getPaymentPlans();
        $paymentPlanToUse = $paymentPlans->getPaymentPlanByCardBrand($this->getCard());
        return array ('numberOfPayments' => $this->installments,
                        'planId' => $paymentPlanToUse->getPlanId(), //PLANAMEX
                    );

    }

    /**
     * @param Omnipay\Common\CreditCard $card
     * @param string $cardReference
     * @return array
     */
    private function getSourceOfFunds($card, $cardReference)
    {
        $sourceOfFouds = array(
            'type'     => 'CARD',
            'provided' => array(
                'card' => array(
                    'number'       => $card->getNumber(),
                    'expiry'       => array(
                        'month' => $card->getExpiryDate('m'),
                        'year'  => $card->getExpiryDate('y'),
                    ),
                    'securityCode' => $card->getCVV(),
                    'nameOnCard'=> $card->getBillingName(),
                ),
            ),
        );

        if(!empty($cardReference)) {
            $sourceOfFouds['token'] = $cardReference;
            unset($sourceOfFouds['provided']['card']['number']);
            unset($sourceOfFouds['provided']['card']['expiry']);
            unset($sourceOfFouds['provided']['card']['securityCode']);
        }

        return $sourceOfFouds;
    }

    /**
     * @param int $installments
     */
    public function setInstallments($installments)
    {
        $this->installments = $installments;
        return $this;
    }


    /**
     * @param $installments
     * @return int
     */
    protected function getInstallments($installments)
    {
        return $installments;
    }

    /**
     * TNSPay requires an OrderID and a TransactionID
     *
     * At the moment, only a transaction ID is available from OmniPay, so this
     * driver will use the transaction id for both elements.
     *
     * @todo Determine the mechanism for live and test mode.
     */
    public function getEndpoint()
    {
        return
            'https://secure.na.tnspayments.com/api/rest/version/' . self::TNSPAY_API_VERSION_NUMBER .
            '/merchant/' . $this->getMerchantId() .
            '/order/' . $this->getTransactionId() .
            '/transaction/' . $this->getTransactionId();
    }

    /**
     * Send the data to TNSPay
     *
     * @param array $data
     * @return \Omnipay\Common\Message\ResponseInterface|Response
     */
    public function sendData($data)
    {
        $json         = json_encode($data);
        $headers      = array(
            'Content-Type' => 'application/json;charset=utf-8',
        );

        try {
            $httpResponse = $this->httpClient->put($this->getEndpoint(), $headers, $json)
                ->setAuth('merchant.' . $this->getMerchantId(), $this->getPassword())
                ->send();

        }  catch (BadResponseException $e) {

            return $this->response = new Response($this, $e->getRequest()->getResponse()->getBody());
        }

        return $this->response = new Response($this, $httpResponse->getBody());
    }

}
