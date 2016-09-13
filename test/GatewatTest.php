<?php

namespace Omnipay\TNSPay;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\Common\CreditCard;

class PurchaseTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('TEST9351473526B');
        $this->gateway->setCurrency('MXN');
        $this->gateway->setPassword('7de59bb7bbf3c86c0ca98d98ece63f27');
    }


    /**
     * Purchase using a card directly
     */
    public function testPurchaseWOToken()
    {
        $card =[
            'number' => '4012000033330026',
            'expiryMonth' => '5',
            'expiryYear' => '2017',
            'cvv' => '123',
            'firstName' => 'Bobby',
            'lastName' => 'Tables',
        ];

        $response = $this->gateway->purchase(
            array('amount' => '450.00',
                'transactionId' => '2020899',
                'card' => $card))
            ->send();
       // error_log(print_r($response, true), 3, '/tmp/purchases.log');
        $this->assertTrue($response->isSuccessful());
        //print_r($response);
    }


    /**
     * Purchase using a tokenized card
     */
    public function testPurchaseWToken()
    {
        $cardData = [
            'number' => '4012000033330026',
            'expiryMonth' => '5',
            'expiryYear' => '2017',
            'cvv' => '123',
            'firstName' => 'Osom',
            'lastName' => 'Tester',
        ];

        //Send purchase request
        $tokenizationResponse = $this->gateway->createCard( [ 'card' => $cardData ])->send();

        $this->assertTrue($tokenizationResponse->isSuccessful());
        $bodyResponse = $tokenizationResponse->getData();
        error_log(print_r($bodyResponse, true), 3, '/tmp/tokens.log');


        $response = $this->gateway->purchase(
            array('amount' => '1009.00',
                    'cardReference' => $bodyResponse['token'],
                'transactionId' => time(),
                'clientIp' => '189.206.5.138',
                'card' => $cardData))
            ->send();
         error_log(print_r($response, true), 3, '/tmp/token_purchases.log');
        $this->assertTrue($response->isSuccessful());
        //print_r($response);

    }


    /**
     * Purchase using a tokenized card
     * Use a payment Plan
     */
    public function testPurchaseWTokenPaymentPlan()
    {

        //print_r($response);
    }


    /*public function testFetchTransaction()
    {
        $request = $this->gateway->fetchTransaction(array('transactionReference' => 'abc123'));
        $this->assertInstanceOf('\Omnipay\PayPal\Message\FetchTransactionRequest', $request);
        $this->assertSame('abc123', $request->getTransactionReference());
    }*/

    /*public function testAuthorize()
{
    $this->setMockHttpResponse('ProPurchaseSuccess.txt');
    $response = $this->gateway->authorize($this->options)->send();
    $this->assertTrue($response->isSuccessful());
    $this->assertEquals('96U93778BD657313D', $response->getTransactionReference());
    $this->assertNull($response->getMessage());
} */


    /*public function testPurchase()
    {
        $this->setMockHttpResponse('ProPurchaseSuccess.txt');
        $response = $this->gateway->purchase($this->options)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('96U93778BD657313D', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }*/

}