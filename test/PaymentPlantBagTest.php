<?php

namespace Omnipay\TNSPay;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\TNSPay\Exception\InvalidPaypmentPlanException;
use Omnipay\TNSPay\Exception\EmptyPaymentPlanBagException;
use Omnipay\Common\CreditCard;
use Omnipay\TNSPay\Message\CardRequest;

class PaymentPlanBagTest extends GatewayTestCase
{
    /**
     * @var PaymentPlanBag $ppBag
     */
    protected $ppBag;

    public function setUp()
    {
        parent::setUp();
        $this->ppBag = new PaymentPlanBag();
    }



    public function testAddByObject()
    {
        $pp = new PaymentPlan('default', 'BPOUIX');
        $this->ppBag->add($pp);
        $this->assertTrue(1 == $this->ppBag->count());
    }

    public function testAddByArray()
    {
        $pp = array('cardBrand'=>'default', 'planId' =>'BPOUIX');
        $this->ppBag->add($pp);
        $this->assertTrue(1 == $this->ppBag->count());
    }

    public function testAddByArrayExc()
    {

       try {
           $pp = array('planId' =>'BPOUIX');
           $this->ppBag->add($pp);
           $this->fail("Expected exception ");
        }catch(InvalidPaypmentPlanException $e){ //Not catching a generic Exception or the fail function is also catched
            $this->assertEquals("Payment Plan must be defined by both cardBrand and planId",$e->getMessage());
        }

    }

    public function testGetPlanExc()
    {
        $this->ppBag = new PaymentPlanBag();
        try {
            $card = new CreditCard();
            $this->ppBag->getPaymentPlanByCardBrand($card);

        }catch(EmptyPaymentPlanBagException $e){ //Not catching a generic Exception or the fail function is also catched
            $this->assertEquals("Payment Plans Bag is Empty",$e->getMessage());
        }

    }

    /**
     * If there is only ONE plan
     */
    public function testGetPlan()
    {
        $this->ppBag = new PaymentPlanBag();
        $pp = array('cardBrand'=>'default', 'planId' =>'BPOUIX');
        $this->ppBag->add($pp);

        $card = new CreditCard(
                array (
                    'number' => '4012000033330026',
                    'expiryMonth' => '5',
                    'expiryYear' => '2017',
                    'cvv' => '123',
                ));

        $returnedPP = $this->ppBag->getPaymentPlanByCardBrand($card);
        $this->assertTrue( $returnedPP instanceof PaymentPlan );
    }

    /**
     * If there is only ONE plan
     */
    public function testGetPlanAmex()
    {
        $this->ppBag = new PaymentPlanBag();
        $testPlanVal = 'AMEX_PLAN';
        $this->ppBag->add(array('cardBrand'=>'default', 'planId' =>'BPOUIX'));
        $this->ppBag->add(array('cardBrand'=>CreditCard::BRAND_AMEX, 'planId' =>$testPlanVal));
        $card = new CreditCard(
            array (
                'number' => '377399407489450',
                'expiryMonth' => '5',
                'expiryYear' => '2017',
                'cvv' => '123',
            ));

        $returnedPP = $this->ppBag->getPaymentPlanByCardBrand($card);
        $this->assertTrue( $returnedPP instanceof PaymentPlan );
        $this->assertEquals( $testPlanVal, $returnedPP->getPlanId() );
    }

    /**
     * If there is only ONE plan
     */
    public function testGetPlanDefault()
    {
        $this->ppBag = new PaymentPlanBag();
        $testPlanVal = 'BPOUIX';
        $this->ppBag->add(array('cardBrand'=>CreditCard::BRAND_AMEX, 'planId' =>'AMNEX_PLAN'));
        $this->ppBag->add(array('cardBrand'=>'default', 'planId' =>$testPlanVal));

        $card = new CreditCard(
            array (
                'number' => '4012000033330026',
                'expiryMonth' => '5',
                'expiryYear' => '2017',
                'cvv' => '123',
            ));

        $returnedPP = $this->ppBag->getPaymentPlanByCardBrand($card);
        $this->assertTrue( $returnedPP instanceof PaymentPlan );
        $this->assertEquals( $testPlanVal, $returnedPP->getPlanId() );
    }


    public function testAssignPaymentPlan()
    {
        $gateway = new Gateway();

        $gateway->setMerchantId('TEST9351473526B');
        $gateway->setCurrency('MXN');
        $gateway->setPassword('7de59bb7bbf3c86c0ca98d98ece63f27');
        $gateway->setPaymentPlans(new PaymentPlanBag());


        $this->assertTrue( $gateway->getPaymentPlans() instanceof PaymentPlanBag );
    }

}