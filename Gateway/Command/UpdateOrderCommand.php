<?php
/************************************************************
 * *
 *  * Copyright © Kohaku1907. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    nguyenmtri11@gmail.com
 * *  @project   Payoo Payment
 */
namespace Kohaku1907\PayooPayment\Gateway\Command;

use Kohaku1907\PayooPayment\Gateway\Helper\TransactionReader;
use Kohaku1907\PayooPayment\Model\PaymentNotification;
use Magento\Payment\Gateway\Command;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Model\MethodInterface;

/**
 * Class UpdateOrderCommand
 *
 * @package Kohaku1907\PayooPayment\Gateway\Command
 */
class UpdateOrderCommand implements CommandInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Constructor
     *
     * @param ConfigInterface          $config
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        ConfigInterface $config,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->config          = $config;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param array $commandSubject
     * @return Command\ResultInterface|void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(array $commandSubject)
    {
        $paymentDO = SubjectReader::readPayment($commandSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        $order   = $payment->getOrder();
        ContextHelper::assertOrderPayment($payment);

        if ($order->getState() === Order::STATE_PENDING_PAYMENT) {
            switch ($this->config->getValue('payment_action')) {
                case MethodInterface::ACTION_AUTHORIZE_CAPTURE:
                    $payment->capture();
                    break;
            }
        }

        if (TransactionReader::isIpn($commandSubject)) {
            $response = SubjectReader::readResponse($commandSubject);
            /** @var PaymentNotification $invoice */
            $invoice = $response['invoice'];
            $message = __('IPN "%1"', $invoice->getState());
            $payment->prependMessage($message);
            $order->addCommentToStatusHistory($message);
        }

        $this->orderRepository->save($order);
    }
}
