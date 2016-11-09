<?php

namespace Omnipay\TNSPay;
use Omnipay\TNSPay\CreditCard as CreditCard;
use Omnipay\Tests\GatewayTestCase;
//use Omnipay\Common\CreditCard;
use Omnipay\TNSPay\Message\CardRequest;

class CardTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setCurrency('MXN');
        $this->gateway->setMerchantId('TEST9351473526B');
        $this->gateway->setPassword('7de59bb7bbf3c86c0ca98d98ece63f27');

    }



    public function testTokenization()
    {

        $cardData = [
            'number' => '4012000033330026',
            'expiryMonth' => '5',
            'expiryYear' => '2017',
            'cvv' => '123'
        ];

            //Send purchase request
            $response = $this->gateway->createCard( [ 'card' => $cardData ] )->send();

       // var_dump($response->getResponse());
        $this->assertTrue($response->isSuccessful());
        $bodyResponse = $response->getData();
        //error_log(print_r($response, true), 3, '/tmp/rr.log');
      //  error_log(print_r($response->getData(), true), 3, '/tmp/rr.log');

        $this->assertEquals('VALID', $bodyResponse['status']);
        $this->assertEquals('BASIC', $bodyResponse['verificationStrategy']);
        $this->assertEquals('BASIC_VERIFICATION_SUCCESSFUL', $bodyResponse['response']['gatewayCode']);

    }


    public function testAmexTokenization()
    {

        $cardData = [
            'number' => '345678901234564',
            'expiryMonth' => '5',
            'expiryYear' => '2017',
            'cvv' => '1234'
        ];

        //Send purchase request
        $response = $this->gateway->createCard( [ 'card' => $cardData ] )->send();

        // var_dump($response->getResponse());
        $this->assertTrue($response->isSuccessful());
        $bodyResponse = $response->getData();
        //error_log(print_r($response, true), 3, '/tmp/rr.log');
        //  error_log(print_r($response->getData(), true), 3, '/tmp/rr.log');

        $this->assertEquals('VALID', $bodyResponse['status']);
        $this->assertEquals('BASIC', $bodyResponse['verificationStrategy']);
        $this->assertEquals('BASIC_VERIFICATION_SUCCESSFUL', $bodyResponse['response']['gatewayCode']);

    }

    public function testInvalidCard()
    {

        $cardData = [
            'number' => '4012000033330026',
            'expiryMonth' => '5',
            'expiryYear' => '2017',
            'cvv' => '123'
        ];

        //Send purchase request
        $response = $this->gateway->createCard( [ 'card' => $cardData ] )->send();

        // var_dump($response->getResponse());
        $this->assertTrue($response->isSuccessful());
        $bodyResponse = $response->getData();
        //error_log(print_r($response, true), 3, '/tmp/rr.log');
        //  error_log(print_r($response->getData(), true), 3, '/tmp/rr.log');

        $this->assertEquals('VALID', $bodyResponse['status']);
        $this->assertEquals('BASIC', $bodyResponse['verificationStrategy']);
        $this->assertEquals('BASIC_VERIFICATION_SUCCESSFUL', $bodyResponse['response']['gatewayCode']);

    }

    public function testCardSerialization()
    {
        $card = new CreditCard(array (
            'number' => '4012000033330026',
            'expiryMonth' => '5',
            'expiryYear' => '2017',
            'cvv' => '123',
            'device' => new Device('127.0.0.1', 'adsdsdsadas')
        ));


        print_r(serialize($card));

    }

    public function testDeviceSerialization()
    {

        $d= new Device('127.0.0.1', 'adsdsdsadas sass');
        var_dump($d->toArray());
        print_r(serialize($d));

    }
}