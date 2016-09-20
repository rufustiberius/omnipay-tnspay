<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 15/09/16
 * Time: 09:52 AM
 */

namespace Omnipay\TNSPay;


class PaymentPlan
{

    /**
     * You could define a payment plan by card brand
     * @var string $cardBrand
     */
    protected $cardBrand;

    /**
     * Use one Payment Plan defined in
     * https://secure.na.tnspayments.com/api/documentation/integrationGuidelines/supportedFeatures/pickAdditionalFunctionality/paymentPlans/index.html?locale=en_US
     * @var string $planId
     */
    protected $planId;

    /**
     * PaymentPlan used for payment with Installments
     *
     * @param string $cardBrand
     * @param string $planId
     */
    public function __construct( $cardBrand , $planId)
    {
        $this->cardBrand = $cardBrand;
        $this->planId = $planId;
    }

    /**
     * @param string $cardBrand
     */
    public function setCardBrand($cardBrand)
    {
        $this->planId = $cardBrand;
    }

    /**
     * @return string
     */
    public function getCardBrand()
    {
        return $this->cardBrand;
    }

    /**
     * @param string $planId
     */
    public function setPlanId($planId)
    {
        $this->planId = $planId;
    }

    /**
     * @return string
     */
    public function getPlanId()
    {
        return $this->planId;
    }

}