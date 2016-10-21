<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 20/10/16
 * Time: 12:06 PM
 */

namespace Omnipay\TNSPay;

class Device
{
    /**
     * @var string
     */
    protected $ip;

    /**
     * @var string
     */
    protected $browser;

    public function __construct($ip, $browser='')
    {
        $this->ip=$ip;
        $this->browser=$browser;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @param string $browser
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array('ip', 'browser');
    }
}