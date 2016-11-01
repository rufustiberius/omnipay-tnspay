<?php

namespace Omnipay\TNSPay\Message;

//use DOMDocument;
//use Guzzle\Common\Exception\InvalidArgumentException;
//use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Exception\BadResponseException;
use Omnipay\TNSPay\CreditCard;

//use SimpleXMLElement;

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
        $this->validate('amount', 'transactionId', 'orderId', 'device', 'shop', 'customerId', 'cardReference', 'items');
        //$this->getCard()->validate();

        $data = array(
            'apiOperation'  => 'PAY',
            'transaction' => array (
                'reference' => $this->getTransactionId(),
                'source' => 'INTERNET',
                'frequency' => 'SINGLE'
            ),
            'customer' => array ( 'email' => $this->getCard()->getEmail(),
                                  'firstName'=>$this->getCard()->getFirstName(),
                                    'lastName' => $this->getCard()->getLastName()
                ),
            'order'         => array(
                'reference' => $this->getTransactionId(),
                'amount'    => $this->getAmount(),
                'currency'  => $this->getCurrency(),
                'owningEntity' => substr($this->getShop(), 0, 40),
                'customerReference'=> $this->getCustomerId(),
                'productSKU' => $this->getMostExpensiveSku(),
            ),
            'billing' => array('address'=>$this->getAddress($this->getCard(), 'Billing') ),
            'shipping' => array ( 'address'=>$this->getAddress($this->getCard(), 'Shipping')),
            'sourceOfFunds' => $this->getSourceOfFunds($this->getCard(), $this->getCardReference()),
            'device' => array ( 'ipAddress' => $this->getDevice()->getIp(),
                                'browser' => substr($this->getDevice()->getBrowser(), 0, 255)
                            )
        );

        if($this->installments > 1 ) {
            $data['paymentPlan'] = $this->getInstallmentsData();
        }
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
    private function getSourceOfFunds($card, $cardReference=null)
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
     * @return string
     */
    protected function getMostExpensiveSku()
    {
        $sku=null;
        $items = $this->getItems()->all();

        usort($items, function ($a, $b) {
            return $b->getPrice() - $a->getPrice();
        });

        //API only accepts the first 15 characters of SKU
        $sku = substr($items[0]->getName(), 0, 15);
        return $sku;
    }

    /**
     * @param $card
     * @param $string Billing | Shipping
     * @return array
     */
    protected function getAddress($card, $type)
    {

        $address = array (
            'city' => call_user_func_array(array($card, "get{$type}City"), array()),
            'country' => call_user_func_array(array($card, "get{$type}Country"), array()), //$card->getBillingCountry(), // 3
            'postcodeZip' => substr( call_user_func_array(array($card, "get{$type}Postcode"), array()), 0, 10) , //10
            'stateProvince' => substr( call_user_func_array(array($card, "get{$type}State"), array())    , 0, 20), //20
            'street' => substr( call_user_func_array(array($card, "get{$type}Address1"), array()), 0, 100), //100
            'street2' => substr( call_user_func_array(array($card, "get{$type}Address2"), array()), 0, 100) //100
        );


        return array_filter($address);;
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
            '/order/' . $this->getOrderId() .
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
        $headers      = array( 'Content-Type' => 'application/json;charset=utf-8');

        try {
            $httpResponse = $this->httpClient->put($this->getEndpoint(), $headers, $json)
                ->setAuth('merchant.' . $this->getMerchantId(), $this->getPassword())
                ->send();

        }  catch (BadResponseException $e) {
            return $this->response = new Response($this, $e->getRequest()->getResponse()->getBody());
        }

        return $this->response = new Response($this, $httpResponse->getBody());
    }


    /**
     * Get the client Device.
     *
     * @return string
     */
    public function getDevice()
    {
        return $this->getParameter('device');
    }

    /**
     * Sets the client Device.
     *
     * @param string $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setDevice($value)
    {
        return $this->setParameter('device', $value);
    }

    /**
     * Get the client Device.
     *
     * @return string
     */
    public function getShop()
    {
        return $this->getParameter('shop');
    }

    /**
     * Sets the client Device.
     *
     * @param string $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setShop($value)
    {
        return $this->setParameter('shop', $value);
    }


    /**
     * Get the card reference.
     *
     * @return string
     */
    public function getCardReference()
    {
        return $this->getParameter('cardReference');
    }

    /**
     * Sets the card reference.
     *
     * @param string $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setCardReference($value)
    {
        return $this->setParameter('cardReference', $value);
    }

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getParameter('customerId');
    }

    /**
     * Sets the customer Id
     *
     * @param int $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setCustomerId($value)
    {
        return $this->setParameter('customerId', $value);
    }


}
