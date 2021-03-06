<?php

namespace Omnipay\TNSPay;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\Common\CreditCard;
use Faker;

class PurchaseTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('TEST9351473526');
        $this->gateway->setCurrency('MXN');
        $this->gateway->setPassword('30bbc6d3a1a3d189802bde419c856f69');
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

        error_log(print_r("Here we go\n", true), 3, '/tmp/purchase_request.log');
        $response = $this->gateway->purchase(
            array(
                'amount' => '450.00',
                'transactionId' => '2880'.time(),
                'orderId' => '1000000'.time(),
                'card' => $card,
                'shop' => 'www.osom.com',
                'device' => new Device('10.10.0.0','Safari OSX Webkit 1113')
                ))->send();
       // error_log(print_r($response, true), 3, '/tmp/purchases.log');
        $this->assertTrue($response->isSuccessful());
        //print_r($response);
    }


    /**
     * Purchase using a tokenized card
     */
    public function testPurchaseWToken()
    {
        $ppBag = new PaymentPlanBag();
        $ppBag->add(array('cardBrand'=>'default', 'planId' =>'PLANN'));

        $faker = Faker\Factory::create();
        $cardData = [
            'number' => '345678901234564',
            'expiryMonth' => '5',
            'expiryYear' => '2017',
            'cvv' => '1234',
            'firstName' => $faker->firstName(),
            'lastName' => $faker->lastName(),
            'email' => $faker->email(),
            'billingAddress1' => 'Av. Horacio 340 PH',
            'billingAddress2' => 'POLANCO V SECCIÓN',
    'billingCity' => 'MIGUEL HIDALGO',
    'billingPostcode' => '11560',
            'billingFirstName' => $faker->firstName(),
            'billingLastName' => $faker->lastName(),
    'billingState' => 'DISTRITO FEDERAL',
    'billingCountry' => 'MEX',
    'billingPhone' => $faker->phoneNumber(),
    'shippingAddress1' => 'Av. Horacio 340 PH',
    'shippingAddress2' => 'POLANCO V SECCIÓN',
    'shippingCity' => 'MIGUEL HIDALGO',
    'shippingPostcode' => '11560',
    'shippingState' => 'DISTRITO FEDERAL',
    'shippingPhone' => $faker->phoneNumber(),
    'shippingCountry' => 'MEX',
            'shippingFirstName' => $faker->firstName(),
            'shippingLastName' => $faker->lastName(),
        ];

        //Send purchase request

        $tokenizationResponse = $this->gateway->createCard( [ 'card' => $cardData ])->send();
        $this->gateway->setPaymentPlans($ppBag);
        $this->assertTrue($tokenizationResponse->isSuccessful());
        $bodyResponse = $tokenizationResponse->getData();


        $response = $this->gateway->purchase(
            array(
                'amount' => $faker->randomFloat(2, 99, 6000),
                'cardReference' => $bodyResponse['token'],
                'transactionId' => '2880'.time(),
                'orderId' => '1000000'.time(),
                'card' => $cardData,
                'shop' => 'www.osom.com',
                'device' => new Device( $faker->ipv4, $faker->userAgent),
                'customerId' => 35488,
                'items' => array(
                    array (
                            'name' => 'AEO-2015',
                            'price' => $faker->randomFloat(2, 99, 1000),
                            'quantity' => 1
                    ),
                  /*  array ('name' => 'AEO-2087',
                            'price' => $faker->randomFloat(2, 99, 2000),
                            'quantity' => 1
                    ),
                    array ('name' => 'AEO-808',
                        'price' => $faker->randomFloat(2, 99, 1000),
                        'quantity' => 1
                    )*/
                )
            ))->setInstallments(3)->send();

        error_log(print_r($response->getData(), true), 3, '/tmp/token_purchases.log');
        error_log(print_r($response->getRequest()->getEndpoint(), true), 3, '/tmp/token_purchases.log');
        $this->assertTrue($response->isSuccessful());
        //print_r($response);

    }


    public function testPaymentPlanData()
    {
        $ppBag = new PaymentPlanBag();
        $ppBag->add(array('cardBrand'=>'default', 'planId' =>'PLANAMEX'));

        //$ppBag->add(array('cardBrand'=>'default', 'planId' =>'XXXX'));

        $this->gateway->setPaymentPlans($ppBag);

        $cardData = [
            'number' => '345678901234564',
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


        $response = $this->gateway->purchase(
            array(
                'amount' => '1009.00',
                'cardReference' => $bodyResponse['token'],
                'transactionId' => time(),
                'clientIp' => '189.206.5.138',
                'card' => $cardData,
                'items' => array(
                    array ('sku' => 'AEO-2015',
                        'price' => 1200
                    ),
                    array ('sku' => 'AEO-2087',
                        'price' => 189
                    )
                )
            ))->setInstallments(3)->send();

        error_log("\n ****" .date('Y-m-d H:i:s'). "  ****\n", 3, '/tmp/purchase_test.log');
        error_log(print_r($response->getData(), true), 3, '/tmp/purchase_test.log');

        $this->assertTrue($response->isSuccessful());

    }

    /**
     * Purchase using a tokenized card
     * Use a payment Plan
     */
    public function testPurchaseWTokenPaymentPlan()
    {

        //print_r($response);
    }



}