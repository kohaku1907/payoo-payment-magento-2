<?php
/************************************************************
 * *
 *  * Copyright © Kohaku1907. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    nguyenmtri11@gmail.com
 * *  @project   Payoo Payment
 */
namespace Kohaku1907\PayooPayment\Gateway\Response;

use Kohaku1907\PayooPayment\Gateway\Helper\TransactionReader;
use Kohaku1907\PayooPayment\Model\PaymentNotification;
use Magento\Framework\Exception\LocalizedException as LocalizedExceptionAlias;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Kohaku1907\PayooPayment\Gateway\Validator\AbstractResponseValidator;

/**
 * Class TransactionCompleteHandler
 *
 * @package Kohaku1907\PayooPayment\Gateway\Response
 */
class TransactionCompleteHandler implements HandlerInterface
{
    /**
     * @var array
     */
    private $additionalInformationMapping = [
        'transaction_id' => AbstractResponseValidator::TRANSACTION_ID
    ];

    /**
     * @param array $handlingSubject
     * @param array $response
     * @throws LocalizedExceptionAlias
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = SubjectReader::readPayment($handlingSubject);
        /** @var Payment $orderPayment */
        $orderPayment = $paymentDO->getPayment();
        $orderPayment->setIsTransactionClosed(false);
        $orderPayment->setShouldCloseParentTransaction(true);
        if (TransactionReader::isIpn($handlingSubject)) {
            /** @var PaymentNotification $invoice */
            $invoice = $response['invoice'];
            $orderPayment->setTransactionId($invoice->getSession());
            foreach ($this->additionalInformationMapping as $informationKey => $responseKey) {
                if (property_exists ($invoice, $responseKey)) {
                    $orderPayment->setAdditionalInformation($informationKey, $invoice->{$responseKey});
                }
            }
        } else {
            $orderPayment->setTransactionId($response[AbstractResponseValidator::TRANSACTION_ID]);
            foreach ($this->additionalInformationMapping as $informationKey => $responseKey) {
                if (isset($response[$responseKey])) {
                    $orderPayment->setAdditionalInformation($informationKey, ucfirst($response[$responseKey]));
                }
            }
        }
    }
}
