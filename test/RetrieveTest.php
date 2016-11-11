<?php

namespace Omnipay\TNSPay;
use Omnipay\TNSPay\CreditCard as CreditCard;
use Omnipay\Tests\GatewayTestCase;
//use Omnipay\Common\CreditCard;
use Omnipay\TNSPay\Message\CardRequest;

class RetrieveTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setCurrency('MXN');
        $this->gateway->setMerchantId('TEST9351473526');
        $this->gateway->setPassword('30bbc6d3a1a3d189802bde419c856f69');

    }



    public function testIt()
    {

        $orderData = [
            'orderId' => 10001021454,
            'transactionId'=>203825632
        ];

            //Send purchase request
        $response = $this->gateway->retrieve($orderData)->send();

       // var_dump($response->getResponse());
        //error_log(print_r($response, true), 3, '/tmp/retrieve.log');
        $this->assertTrue($response->isSuccessful());
        $bodyResponse = $response->getData();
        error_log(print_r($bodyResponse, true), 3, '/tmp/retrieve.log');
        //error_log(print_r($response, true), 3, '/tmp/rr.log');
      //  error_log(print_r($response->getData(), true), 3, '/tmp/rr.log');
/*
        $this->assertEquals('VALID', $bodyResponse['status']);
        $this->assertEquals('BASIC', $bodyResponse['verificationStrategy']);
        $this->assertEquals('BASIC_VERIFICATION_SUCCESSFUL', $bodyResponse['response']['gatewayCode']);
*/
    }



    public function testFail()
    {

        $orderData = [
            'orderId' => 100010468721454,
            'transactionId'=> '798'
        ];

        //Send purchase request
        $response = $this->gateway->retrieve($orderData)->send();

        // var_dump($response->getResponse());
        //error_log(print_r($response, true), 3, '/tmp/retrieve.log');
        $this->assertTrue(!$response->isSuccessful());
        $bodyResponse = $response->getData();
        error_log(print_r($bodyResponse, true), 3, '/tmp/retrieve.log');
        //error_log(print_r($response, true), 3, '/tmp/rr.log');
        //  error_log(print_r($response->getData(), true), 3, '/tmp/rr.log');
        /*
                $this->assertEquals('VALID', $bodyResponse['status']);
                $this->assertEquals('BASIC', $bodyResponse['verificationStrategy']);
                $this->assertEquals('BASIC_VERIFICATION_SUCCESSFUL', $bodyResponse['response']['gatewayCode']);
        */
    }

}