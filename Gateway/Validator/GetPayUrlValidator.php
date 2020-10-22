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

use Kohaku1907\PayooPayment\Gateway\Request\AbstractDataBuilder;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class GetPayUrlValidator
 * @package Kohaku1907\PayooPayment\Gateway\Validator
 */
class GetPayUrlValidator extends AbstractResponseValidator
{
    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate(array $validationSubject)
    {
        $response         = SubjectReader::readResponse($validationSubject);
        $payment          = SubjectReader::readPayment($validationSubject);
        $orderId          = $payment->getOrder()->getOrderIncrementId();
        $errorMessages    = [];
        $validationResult = $this->validateErrorCode($response)
            && $this->validateOrderId($response['order'], $orderId);
           //; && $this->validateSignature($response);

        if (!$validationResult) {
            $errorMessages = [__('Something went wrong when get pay url.')];
        }

        return $this->createResult($validationResult, $errorMessages);
    }

    /**
     * @return array
     */
    protected function getSignatureArray()
    {
        return [
            AbstractDataBuilder::REQUEST_ID,
            AbstractDataBuilder::ORDER_ID,
            self::RESPONSE_MESSAGE,
            self::RESPONSE_LOCAL_MESSAGE,
            self::PAY_URL,
            self::ERROR_CODE,
            AbstractDataBuilder::REQUEST_TYPE
        ];
    }

    /**
     * Validate Order Id
     *
     * @param array   $response
     * @param $orderId
     * @return boolean
     */
    protected function validateOrderId(array $response, $orderId)
    {
        return isset($response[AbstractDataBuilder::ORDER_ID])
            && (string)($response[AbstractDataBuilder::ORDER_ID]) === (string)$orderId;
    }
}
