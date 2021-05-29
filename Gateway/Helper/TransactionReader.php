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

use Kohaku1907\PayooPayment\Gateway\Request\AbstractDataBuilder;
use Kohaku1907\PayooPayment\Gateway\Validator\AbstractResponseValidator;
use Kohaku1907\PayooPayment\Model\PaymentNotification;

/**
 * Class TransactionReader
 *
 * @package Kohaku1907\PayooPayment\Gateway\Helper
 */
class TransactionReader
{

    /**
     * Is IPN request
     */
    const IS_IPN = 'is_ipn';

    /**
     * Read Pay Url from transaction data
     *
     * @param array $transactionData
     * @return string
     */
    public static function readPayUrl(array $transactionData)
    {
        if (empty($transactionData[AbstractResponseValidator::PAY_URL])) {
            throw new \InvalidArgumentException('Pay Url should be provided');
        }

        return $transactionData[AbstractResponseValidator::PAY_URL];
    }

    /**
     * Read Order Id from transaction data
     *
     * @param array $transactionData
     * @return string
     */
    public static function readOrderId(array $transactionData)
    {
        if (empty($transactionData[AbstractDataBuilder::ORDER_ID])) {
            throw new \InvalidArgumentException('Order Id doesn\'t exit');
        }

        return $transactionData[AbstractDataBuilder::ORDER_ID];
    }

    /**
     * Check Is IPN from transaction data
     *
     * @param array $transactionData
     * @return string
     */
    public static function isIpn(array $transactionData)
    {
        if (!empty($transactionData[self::IS_IPN]) && $transactionData[self::IS_IPN]) {
            return true;
        }

        return false;
    }

    public static function readIpnResponse($xmlData) {
            $doc = new \DOMDocument();
            $doc->loadXML($xmlData);

            $notifyData = ($doc->getElementsByTagName("Data")->item(0)->nodeValue);
            $signature = ($doc->getElementsByTagName("Signature")->item(0)->nodeValue);
            $payooSessionID = $doc->getElementsByTagName("PayooSessionID")->item(0);
            $keyFields = $doc->getElementsByTagName("KeyFields")->item(0)->nodeValue;

        if(trim($notifyData) == "")
        {
            return false;
        }

        $doc = new \DOMDocument();
        $dataValue = base64_decode($notifyData);
        $doc->loadXML($dataValue);

        function readNodeValue($Doc, $TagName)
        {
            $nodeList = $Doc->getElementsByTagname($TagName);
            $tempNode = $nodeList->item(0);
            if($tempNode == null)
                return '';
            return $tempNode->nodeValue;
        }
        /** @var PaymentNotification $invoice */
        $invoice = new PaymentNotification();

        if(readNodeValue($doc, "BillingCode") == '')
        {
            $invoice->setSession(readNodeValue($doc, "session"));
            $invoice->setBusinessUsername(readNodeValue($doc, "username"));
            $invoice->setShopID(readNodeValue($doc, "shop_id"));
            $invoice->setShopTitle(readNodeValue($doc, "shop_title"));
            $invoice->setShopDomain(readNodeValue($doc, "shop_domain"));
            $invoice->setShopBackUrl(readNodeValue($doc, "shop_back_url"));
            $invoice->setOrderNo(readNodeValue($doc, "order_no"));
            $invoice->setOrderCashAmount(readNodeValue($doc, "order_cash_amount"));
            $invoice->setStartShippingDate(readNodeValue($doc, "order_ship_date"));
            $invoice->setShippingDays(readNodeValue($doc, "order_ship_days"));
            $invoice->setOrderDescription(urldecode((readNodeValue($doc, "order_description"))));
            $invoice->setNotifyUrl(readNodeValue($doc, "notify_url"));
            $invoice->setState(readNodeValue($doc, "State"));
            $invoice->setPaymentMethod(readNodeValue($doc, "PaymentMethod"));
            $invoice->setPaymentExpireDate(readNodeValue($doc, "validity_time"));
        }
        else
        {
            $invoice->setBillingCode(readNodeValue($doc, "BillingCode"));
            $invoice->setOrderNo(readNodeValue($doc, "OrderNo"));
            $invoice->setOrderCashAmount(readNodeValue($doc, "OrderCashAmount"));
            $invoice->setState(readNodeValue($doc, "State"));
            $invoice->setPaymentMethod(readNodeValue($doc, "PaymentMethod"));
            $invoice->setShopID(readNodeValue($doc, "ShopId"));
            $invoice->setPaymentExpireDate(readNodeValue($doc, "PaymentExpireDate"));
        }
        return [
            'invoice' => $invoice,
            'checksum' => $signature,
            'payooSessionID' => $payooSessionID,
            'keyFields' => $keyFields
        ];
    }

}
