<?php

namespace Omnipay\TNSPay\Exception;
use Omnipay\Common\Exception\OmnipayException;

/**
 * Invalid Credit Card Exception
 *
 * Thrown when a credit card is invalid or missing required fields.
 */
class EmptyPaymentPlanBagException extends \Exception implements OmnipayException
{
}
