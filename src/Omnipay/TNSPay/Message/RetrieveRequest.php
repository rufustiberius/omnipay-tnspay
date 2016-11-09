<?php

namespace Omnipay\TNSPay\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;
use SimpleXMLElement;
use Omnipay\Common\Message\AbstractRequest;

/**
 * TNSPay Card Request (Tokenization based om system-generated tokens)
 */
class RetrieveRequest extends TnsRequest
{


    /**
     * Get the data to be sent to TNSPay.
     *
     * @return array
     */
    public function getData()
    {
        $this->validate('orderId');
    }


    /**
     * @return string
     */
    public function getEndpoint()
    {
        //merchant/{merchantid}/order/{orderid}/transaction/{transactionid}
        //api/rest/version/39/merchant/{merchantId}/order/{orderid}
        return
            parent::TNSPAY_BASE_URL.'api/rest/version/' . parent::TNSPAY_API_VERSION_NUMBER .
            '/merchant/' . $this->getMerchantId() .
            '/order/'.$this->getOrderId();
    }

    /**
     * Send the data to TNS for Create or Update Token (with system-generated token)
     *
     * @param array $data
     * @return \Omnipay\Common\Message\ResponseInterface|Response
     */
    public function sendData($data)
    {

        $headers      = array(
            'Content-Type' => 'application/json;charset=utf-8',
        );
        try {
            $httpResponse = $this->httpClient->get($this->getEndpoint(), $headers)
                ->setAuth('merchant.' . $this->getMerchantId(), $this->getPassword())
                ->send();

        } catch (BadResponseException $e) {

            return $this->response = new Response($this, $e->getRequest()->getResponse()->getBody());
        } catch (InvalidCreditCardException $cardException) {
            return $this->response = new Response($this, $cardException->getMessage());
        }


        return $this->response = new Response($this, $httpResponse->getBody());
    }

}
