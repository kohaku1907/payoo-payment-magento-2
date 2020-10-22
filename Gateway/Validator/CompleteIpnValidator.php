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
use Kohaku1907\PayooPayment\Model\PaymentNotification;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Class CompleteValidator
 *
 * @package Kohaku1907\PayooPayment\Gateway\Validator
 */
class CompleteIpnValidator extends AbstractResponseValidator
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
            && $this->validateSignature($response);

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
        return isset($response['invoice'])
            && (string)($response['invoice']->getOrderCashAmount()) === (string)$amount;
    }

    protected function validateSignature(array $response)
    {
        /** @var PaymentNotification $invoice */
        $dataResponse = $response['invoice'];
        $checksum = $response['signature'];
        $keyFields = $response['keyFields'];
        $strData = $this->config->getValue('secret_key');

        unset($dataResponse->DigitalSignature);

        if(!empty($keyFields))
        {
            $arr_Keys = explode('|', $keyFields);
            for ($i = 0; $i < count($arr_Keys); $i++)
            {
                $strData .= '|' . $dataResponse->{$arr_Keys[$i]};
            }
        }
        if(strtoupper(hash('sha512',$strData))!= strtoupper($checksum))
        {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @inheritDoc
     */
    protected function getSignatureArray()
    {
        // TODO: Implement getSignatureArray() method.
    }
}
