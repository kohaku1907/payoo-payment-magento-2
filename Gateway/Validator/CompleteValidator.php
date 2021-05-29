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

use Kohaku1907\PayooPayment\Gateway\Helper\Rate;
use Kohaku1907\PayooPayment\Gateway\Request\AbstractDataBuilder;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Class CompleteValidator
 *
 * @package Kohaku1907\PayooPayment\Gateway\Validator
 */
class CompleteValidator extends AbstractResponseValidator
{

    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate(array $validationSubject)
    {
        $response      = SubjectReader::readResponse($validationSubject);
        $amount        = round(SubjectReader::readAmount($validationSubject), 2);
        $payment       = SubjectReader::readPayment($validationSubject);
        $amount        = $this->helperRate->getVndAmount($payment->getPayment()->getOrder(), $amount);
        $errorMessages = [];

        $validationResult = $this->validateTotalAmount($response, $amount)
            && $this->validateTransactionId($response)
            && $this->validateErrorCode($response)
            && $this->validateSignature($response, '.', true);

        if (!$validationResult) {
            $errorMessages = [__('Transaction has been declined. Please try again later.')];
        }

        return $this->createResult($validationResult, $errorMessages);
    }

    /**
     * Validate total amount.
     *
     * @param array               $response
     * @param array|number|string $amount
     * @return boolean
     */
    protected function validateTotalAmount(array $response, $amount)
    {
        return isset($response[self::TOTAL_AMOUNT])
            && (string)($response[self::TOTAL_AMOUNT]) === (string)$amount;
    }

    /**
     * @inheritDoc
     */
    protected function getSignatureArray($response)
    {
        return [
            self::TRANSACTION_ID,
            AbstractDataBuilder::ORDER_ID,
            self::STATUS,
        ];
    }
}
