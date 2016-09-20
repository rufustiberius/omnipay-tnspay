<?php

namespace Omnipay\TNSPay;

use Omnipay\TNSPay\Exception\InvalidPaypmentPlanException;
use Omnipay\TNSPay\Exception\EmptyPaymentPlanBagException;
use Omnipay\Common;



class PaymentPlanBag extends Common\ItemBag
{


    /**
     * Add a Payment Plan to the bag
     *
     * @see Item
     *
     * @param PaymentPlan|array $paymentPlan An existing item, or associative array of item parameters
     */
    public function add($item)
    {

        if ($item instanceof PaymentPlan) {
            $this->items['cardBrand'] = $item;
        } else {

            if(isset($item['cardBrand']) && isset($item['planId']))
                $this->items['cardBrand'] = new PaymentPlan($item['cardBrand'], $item['planId'] );
            else
                throw new InvalidPaypmentPlanException('Payment Plan must be defined by both cardBrand and planId');
        }


    }

    /**
     * @param Common\CreditCard $card
     * @return PaymentPlan
     * @throws EmptyPaymentPlanBagException
     */
    public function getPaymentPlanByCardBrand(Common\CreditCard $card)
    {

        if ($this->count() > 0) {

            if ($this->count() == 1) {
                return current($this->items);
            }

            if (in_array($card->getBrand() , array_keys($this->items))) {
                return $this->items[$card->getBrand()];
            }

            return isset($this->items['default']) ? $this->items['default'] : current($this->items);
        } else {
            throw new EmptyPaymentPlanBagException('Payment Plans Bag is Empty');
        }

    }

}