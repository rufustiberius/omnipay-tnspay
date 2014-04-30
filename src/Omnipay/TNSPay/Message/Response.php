<?php
/**
 * Created by PhpStorm.
 * User: RichardQ
 * Date: 29/04/14
 * Time: 16:20
 */

namespace Omnipay\TNSPay\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Class Response
 * @package Omnipay\TNSPay\Message
 */
class Response extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * A list of response.gatewayCodes and their descriptions.
     *
     * @var array
     */
    protected static $a_ResponseCodes = array(
        'APPROVED'                      => 'Transaction Approved',
        'UNSPECIFIED_FAILURE'           => 'Transaction could not be processed',
        'DECLINED'                      => 'Transaction declined by issuer',
        'TIMED_OUT'                     => 'Response timed out',
        'EXPIRED_CARD'                  => 'Transaction declined due to expired card',
        'INSUFFICIENT_FUNDS'            => 'Transaction declined due to insufficient funds',
        'ACQUIRER_SYSTEM_ERROR'         => 'Acquirer system error occurred processing the transaction',
        'SYSTEM_ERROR'                  => 'Internal system error occurred processing the transaction',
        'NOT_SUPPORTED'                 => 'Transaction type not supported',
        'DECLINED_DO_NOT_CONTACT'       => 'Transaction declined - do not contact issuer',
        'ABORTED'                       => 'Transaction aborted by payer',
        'BLOCKED'                       => 'Transaction blocked due to Risk or 3D Secure blocking rules',
        'CANCELLED'                     => 'Transaction cancelled by payer',
        'DEFERRED_TRANSACTION_RECEIVED' => 'Deferred transaction received and awaiting processing',
        'REFERRED'                      => 'Transaction declined - refer to issuer',
        'AUTHENTICATION_FAILED'         => '3D Secure authentication failed',
        'INVALID_CSC'                   => 'Invalid card security code',
        'LOCK_FAILURE'                  => 'Order locked - another transaction is in progress for this order',
        'SUBMITTED'                     => 'Transaction submitted - response has not yet been received',
        'NOT_ENROLLED_3D_SECURE'        => 'Card holder is not enrolled in 3D Secure',
        'PENDING'                       => 'Transaction is pending',
        'EXCEEDED_RETRY_LIMIT'          => 'Transaction retry limit exceeded',
        'DUPLICATE_BATCH'               => 'Transaction declined due to duplicate batch',
        'DECLINED_AVS'                  => 'Transaction declined due to address verification',
        'DECLINED_CSC'                  => 'Transaction declined due to card security code',
        'DECLINED_AVS_CSC'              => 'Transaction declined due to address verification and card security code',
        'DECLINED_PAYMENT_PLAN'         => 'Transaction declined due to payment plan',
        'APPROVED_PENDING_SETTLEMENT'   => 'Transaction Approved - pending batch settlement',
        'UNKNOWN'                       => 'Response unknown',
    );

    /**
     * Construct the response instance for the request.
     *
     * @param RequestInterface $request
     * @param                  $data
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data    = json_decode($data, True);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidResponseException;
        }
    }

    /**
     * Get the message from the gateway.
     *
     * @return null|string|void
     */
    public function getMessage()
    {
        $s_Message = self::$a_ResponseCodes['UNKNOWN'];
        if ($this->isSuccessful()) {
            if (isset($this->data['response']['gatewayCode'])) {
                if (isset(self::$a_ResponseCodes[$this->data['response']['gatewayCode']])) {
                    $s_Message = self::$a_ResponseCodes[$this->data['response']['gatewayCode']];
                } else {
                    $s_Message = self::$a_ResponseCodes . ' : ' . $this->data['response']['gatewayCode'];
                }
            }
        } elseif (isset($this->data['error']['explanation'])) {
            $s_Message = $this->data['error']['explanation'];
        }

        return $s_Message;
    }

    /**
     * Gets the redirect target url.
     */
    public function getRedirectUrl()
    {
        return '';
    }

    /**
     * Get the required redirect method (either GET or POST).
     */
    public function getRedirectMethod()
    {
    }

    /**
     * Gets the redirect form data array, if the redirect method is POST.
     */
    public function getRedirectData()
    {
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     * @todo Verify the valid response key. Probably 'response.gatewayCode' == 'APPROVED'.
     */
    public function isSuccessful()
    {
        return !array_key_exists('error', $this->data);
    }
}