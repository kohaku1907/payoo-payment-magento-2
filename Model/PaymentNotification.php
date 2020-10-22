<?php
/************************************************************
 * *
 *  * Copyright © Kohaku1907. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    nguyenmtri11@gmail.com
 * *  @project   Payoo Payment
 */
namespace Kohaku1907\PayooPayment\Model;

class PaymentNotification
{
    public $paymentMethod = "";
    public $state = "";
    public $session = "";
    public $businessUsername = "";
    public $shopID = 0;
    public $shopTitle = "";
    public $shopDomain = "";
    public $shopBackUrl = "";
    public $orderNo = "";
    public $orderCashAmount = 0;
    public $startShippingDate = ""; //Format: dd/mm/yyyy
    public $shippingDays = 0;
    public $orderDescription = "";
    public $notifyUrl = "";
    public $billingCode = "";
    public $paymentExpireDate = "";

    function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }
    function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
    function setState($state)
    {
        $this->state = $state;
    }
    function getState()
    {
        return $this->state;
    }

    function getSession()
    {
        return $this->session;
    }
    function getBusinessUsername()
    {
        return $this->businessUsername;
    }
    function getShopID()
    {
        return $this->shopID;
    }
    function getShopTitle()
    {
        return $this->shopTitle;
    }
    function getShopDomain()
    {
        return $this->shopDomain;
    }
    function getShopBackUrl()
    {
        return $this->shopBackUrl;
    }
    function getOrderNo()
    {
        return $this->orderNo;
    }
    function getOrderCashAmount()
    {
        return $this->orderCashAmount;
    }
    function getStartShippingDate()
    {
        return $this->startShippingDate;
    }
    function getShippingDays()
    {
        return $this->shippingDays;
    }
    function getOrderDescription()
    {
        return $this->orderDescription;
    }
    function getNotifyUrl()
    {
        return $this->notifyUrl;
    }
    function setSession($session)
    {
        $this->session = $session;
    }
    function setBusinessUsername($businessUsername)
    {
        $this->businessUsername = $businessUsername;
    }
    function setShopID($shopID)
    {
        $this->shopID = $shopID;
    }
    function setShopTitle($shopTitle)
    {
        $this->shopTitle = $shopTitle;
    }
    function setShopDomain($shopDomain)
    {
        $this->shopDomain = $shopDomain;
    }
    function setShopBackUrl($shopBackUrl)
    {
        $this->shopBackUrl = $shopBackUrl;
    }
    function setOrderNo($orderNo)
    {
        $this->orderNo = $orderNo;
    }
    function setOrderCashAmount($orderCashAmount)
    {
        $this->orderCashAmount = $orderCashAmount;
    }
    function setStartShippingDate($startShippingDate)
    {
        $this->startShippingDate = $startShippingDate;
    }
    function setShippingDays($shippingDays)
    {
        $this->shippingDays = $shippingDays;
    }
    function setOrderDescription($orderDescription)
    {
        $this->orderDescription = $orderDescription;
    }
    function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
    }
    //////////////// BillingCode ////////////////////
    function setBillingCode($billingCode)
    {
        $this->billingCode = $billingCode;
    }
    function getBillingCode()
    {
        return $this->billingCode;
    }
    function setPaymentExpireDate($paymentExpireDate)
    {
        $this->paymentExpireDate = $paymentExpireDate;
    }
    function getPaymentExpireDate()
    {
        return $this->paymentExpireDate;
    }
}
