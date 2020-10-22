<?php
/************************************************************
 * *
 *  * Copyright © Kohaku1907. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    nguyenmtri11@gmail.com
 * *  @project   Payoo Payment
 */
namespace Kohaku1907\PayooPayment\Gateway\Validator;

use Kohaku1907\PayooPayment\Gateway\Helper\Authorization;
use Kohaku1907\PayooPayment\Gateway\Helper\Rate;
use Kohaku1907\PayooPayment\Gateway\Request\AbstractDataBuilder;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Class AbstractResponseValidator
 */
abstract class AbstractResponseValidator extends AbstractValidator
{

    /**
     * The amount that was authorised for this transaction
     */
    const TOTAL_AMOUNT = 'totalAmount';

    /**
     * The transaction type that this transaction was processed under
     * One of: Purchase, MOTO, Recurring
     */
    const TRANSACTION_TYPE = 'transactionType';

    /**
     * Pay Url
     */
    const PAY_URL = 'payment_url';

    /**
     * Transaction Id
     */
    const TRANSACTION_ID = 'session';

    /**
     * Error Code
     */
    const ERROR_CODE = 'errorcode';

    /**
     * Error Code Accept
     */
    const ERROR_CODE_ACCEPT = '0';

    /**
     * Result success
     */
    const RESULT_FAIL = 'fail';

    /**
     * Message
     */
    const RESPONSE_MESSAGE = 'message';

    /**
     * Local Response
     */
    const RESPONSE_LOCAL_MESSAGE = 'localMessage';

    /**
     * Order Type
     */
    const ORDER_TYPE = 'orderType';

    /**
     * Response Time
     */
    const RESPONSE_TIME = 'responseTime';

    /**
     * Pay type: qr or web
     */
    const PAY_TYPE = 'payType';


    /**
     * result
     */
    const RESULT = 'result';

    /**
     * status
     */
    const STATUS = 'status';

    /**
     * @var Rate
     */
    protected $helperRate;

    /**
     * @var Authorization
     */
    protected $authorization;

    /**
     * AbstractResponseValidator constructor.
     *
     * @param ResultInterfaceFactory $resultFactory
     * @param Authorization          $authorization
     * @param Rate                   $helperRate
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        Authorization $authorization,
        Rate $helperRate
    ) {
        parent::__construct($resultFactory);
        $this->helperRate    = $helperRate;
        $this->authorization = $authorization;
    }

    /**
     * @return array
     */
    abstract protected function getSignatureArray();

    /**
     * @param array $response
     * @return boolean
     */
    protected function validateErrorCode(array $response)
    {
        return !isset($response[self::ERROR_CODE]);
    }

    /**
     * @param array $response
     * @return boolean
     */
    protected function validateTransactionId(array $response)
    {
        return isset($response[self::TRANSACTION_ID])
            && $response[self::TRANSACTION_ID];
    }

    /**
     * Validate Signature
     *
     * @param array $response
     * @return boolean
     */
    protected function validateSignature(array $response)
    {
        $newParams = [];
        foreach ($this->getSignatureArray() as $param) {
            if (isset($response[$param])) {
                $newParams[$param] = $response[$param];
            }
        }
        $signature = $this->authorization->getSignature($newParams);
        if (!empty($response[AbstractDataBuilder::SIGNATURE])
            && $response[AbstractDataBuilder::SIGNATURE] === $signature) {
            return  true;
        }

        return false;
    }
}
