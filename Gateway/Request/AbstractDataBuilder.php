<?php
/************************************************************
 * *
 *  * Copyright © Kohaku1907. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    nguyenmtri11@gmail.com
 * *  @project   Payoo Payment
 */
namespace Kohaku1907\PayooPayment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class AbstractDataBuilder
 * @package Kohaku1907\PayooPayment\Gateway\Request
 */
abstract class AbstractDataBuilder implements BuilderInterface
{
    /**
     * Pay Url
     */
    const PAY_URL_TYPE = 'capturePayooPayment';

    /**@#+
     * Payoo AIO Url path
     *
     * @const
     */
    const PAY_URL_PATH = 'order/create';

    /**
     * Refund Url Path
     */
    const REFUND_TYPE = 'refundPayooPayment';

    /**
     * Transaction Type: Refund
     */
    const REFUND = 'refund';

    /**
     * Transaction Id
     */
    const TRANSACTION_ID = 'session';

    /**
     * Access Key
     */
    const ACCESS_KEY = 'accessKey';

    /**
     * Secret key
     */
    const SECRET_KEY = 'secretKey';

    /**
     * Partner code
     */
    const PARTNER_CODE = 'partnerCode';

    /**
     * Request Id
     */
    const REQUEST_ID = 'requestId';

    /**
     * Order Info
     */
    const ORDER_INFO = 'orderInfo';

    /**
     * Return Url
     */
    const RETURN_URL = 'returnUrl';

    /**
     * Notify Url
     */
    const NOTIFY_URL = 'notifyUrl';

    /**
     * Extra Data
     */
    const EXTRA_DATA = 'extraData';

    /**
     * Request Type
     */
    const REQUEST_TYPE = 'requestType';

    /**
     * Signature
     */
    const SIGNATURE = 'checksum';

    /**
     * Merchant Ref
     */
    const ORDER_ID = 'order_no';

    /**
     * Amount
     */
    const AMOUNT = 'amount';
}
