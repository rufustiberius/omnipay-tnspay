<?php

namespace Omnipay\TNSPay\Message;

use DOMDocument;
use Omnipay\Common\Exception\InvalidCreditCardException;
use SimpleXMLElement;
use Omnipay\Common\Message\AbstractRequest;

/**
 * TNSPay Card Request (Tokenization based om system-generated tokens)
 */
class CardRequest extends TnsRequest
{

    /**
     * @var string
     */
    protected $verificationStrategy = 'BASIC';
    /**
     * Get the data to be sent to TNSPay.
     *
     * @return array
     */
    public function getData()
    {
        $this->validate('card');
        $this->getCard()->validate();

        $data = array(
            'sourceOfFunds' => array(
                'type'     => 'CARD',
                'provided' => array(
                    'card' => array(
                        'number'       => $this->getCard()->getNumber(),
                        'expiry'       => array(
                            'month' => $this->getCard()->getExpiryDate('m'),
                            'year'  => $this->getCard()->getExpiryDate('y'),
                        ),
                        'securityCode' => $this->getCard()->getCVV(),
                    ),
                ),
            ),
            'verificationStrategy' => $this->getVerificationStrategy()
        );

        return $data;
    }


    /**
     * @return string
     */
    public function getEndpoint()
    {
        return
            parent::TNSPAY_BASE_URL.'api/rest/version/' . parent::TNSPAY_API_VERSION_NUMBER .
            '/merchant/' . $this->getMerchantId() .
            '/token';
    }

    /**
     * Send the data to TNS for Create or Update Token (with system-generated token)
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
            $httpResponse = $this->httpClient->post($this->getEndpoint(), $headers, $json)
                ->setAuth('merchant.' . $this->getMerchantId(), $this->getPassword())
                ->send();

        } catch (BadResponseException $e) {

            return $this->response = new Response($this, $e->getRequest()->getResponse()->getBody());
        } catch (InvalidCreditCardException $cardException) {
            return $this->response = new Response($this, $cardException->getMessage());
        }


        return $this->response = new Response($this, $httpResponse->getBody());
    }


    /**
     * @param $verificationStrategy
     */
    public function setVerificationStrategy( $verificationStrategy)
    {
        $this->verificationStrategy = $verificationStrategy;
    }

    /**
     * @return string
     */
    public function getVerificationStrategy()
    {
        return $this->verificationStrategy;
    }

}
