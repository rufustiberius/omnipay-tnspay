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
    protected $ip;
    protected $browser;

    public function __construct($ip, $browser='')
    {
        $this->ip=$ip;
        $this->browser=$browser;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function getBrowser()
    {
        return $this->browser;
    }
}