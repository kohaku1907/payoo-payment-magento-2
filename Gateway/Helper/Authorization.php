<?php
/************************************************************
 * *
 *  * Copyright © Kohaku1907. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    nguyenmtri11@gmail.com
 * *  @project   Payoo Payment
 */
namespace Kohaku1907\PayooPayment\Gateway\Helper;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Payment\Gateway\ConfigInterface;
use Kohaku1907\PayooPayment\Gateway\Request\AbstractDataBuilder;

/**
 * Class Authorization
 *
 * @package Kohaku1907\PayooPayment\Gateway\Helper
 */
class Authorization
{
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var string
     */
    protected $timestamp;

    /**
     * @var string
     */
    protected $params;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * Authorization constructor.
     * @param DateTime        $dateTime
     * @param Json            $serializer
     * @param ConfigInterface $config
     */
    public function __construct(
        DateTime $dateTime,
        Json $serializer,
        ConfigInterface $config
    ) {
        $this->dateTime   = $dateTime;
        $this->config     = $config;
        $this->serializer = $serializer;
    }

    /**
     * Set Parameter
     *
     * @param $params
     * @return $this
     */
    public function setParameter($params)
    {
        $this->params = $this->serializer->serialize($params);
        return $this;
    }

    /**
     * Signature
     *
     * @param $params
     * @return string
     */
    public function getSignature($params, $delimiter = '', $ltrim = false)
    {
        $str = '';
        foreach ($params as $param) {
            $str .= $delimiter . $param;
        }

        if($ltrim) {
            $str = ltrim($str, $delimiter);
        }
        return hash('sha512',$this->getSecretKey() . $str);
        //return hash('sha512',$this->getSecretKey() .$params['session'].'.'.$params['order_no'].'.'.$params['status']);
    }

    /**
     * @return array
     */
    public function getSignatureData()
    {
        return [
            AbstractDataBuilder::PARTNER_CODE,
            AbstractDataBuilder::ACCESS_KEY,
            AbstractDataBuilder::REQUEST_ID,
            AbstractDataBuilder::AMOUNT,
            AbstractDataBuilder::ORDER_ID,
            AbstractDataBuilder::TRANSACTION_ID,
            AbstractDataBuilder::ORDER_INFO,
            AbstractDataBuilder::RETURN_URL,
            AbstractDataBuilder::NOTIFY_URL,
            AbstractDataBuilder::EXTRA_DATA
        ];
    }

    /**
     * @return string
     */
    public function getParameter()
    {
        return $this->params;
    }

    /**
     * @return array
     */
    private function getPartnerInfo()
    {
        return [
            AbstractDataBuilder::PARTNER_CODE => $this->getPartnerCode(),
            AbstractDataBuilder::ACCESS_KEY => $this->getAccessKey()
        ];
    }

    /**
     * Get Header
     *
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($this->getParameter())
        ];
    }

    /**
     * @return string
     */
    private function getTimestamp()
    {
        if ($this->timestamp === null) {
            $this->timestamp = (string)($this->dateTime->gmtTimestamp() * 1000);
        }

        return $this->timestamp;
    }

    /**
     * @return mixed
     */
    private function getAccessKey()
    {
        return $this->config->getValue('access_key');
    }

    /**
     * @return mixed
     */
    private function getSecretKey()
    {
        return $this->config->getValue('secret_key');
    }

    /**
     * @return mixed
     */
    private function getPartnerCode()
    {
        return $this->config->getValue('username');
    }
}
