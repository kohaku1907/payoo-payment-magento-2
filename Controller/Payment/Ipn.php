<?php
/************************************************************
 * *
 *  * Copyright © Kohaku1907. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    nguyenmtri11@gmail.com
 * *  @project   Payoo Payment
 */
namespace Kohaku1907\PayooPayment\Controller\Payment;

use Kohaku1907\PayooPayment\Gateway\Helper\TransactionReader;
use Kohaku1907\PayooPayment\Model\PaymentNotification;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action as AppAction;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;

/**
 * Class Ipn
 *
 * @package Kohaku1907\PayooPayment\Controller\Payment
 */
class Ipn extends AppAction implements CsrfAwareActionInterface
{
    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var MethodInterface
     */
    private $method;

    /**
     * @var PaymentDataObjectFactory
     */
    private $paymentDataObjectFactory;
    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var OrderSender
     */
    private $orderSender;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Ipn constructor.
     *
     * @param Context                  $context
     * @param Session                  $checkoutSession
     * @param MethodInterface          $method
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderFactory             $orderFactory
     * @param CommandPoolInterface     $commandPool
     * @param OrderSender              $orderSender
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        MethodInterface $method,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        OrderRepositoryInterface $orderRepository,
        OrderFactory $orderFactory,
        CommandPoolInterface $commandPool,
        OrderSender $orderSender,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->commandPool              = $commandPool;
        $this->checkoutSession          = $checkoutSession;
        $this->orderRepository          = $orderRepository;
        $this->method                   = $method;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->orderFactory             = $orderFactory;
        $this->orderSender              = $orderSender;
        $this->_logger                  = $logger;
    }

    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }

        try {
            $response         = $this->getRequest()->getParam('NotifyData');
            $paymentData      = TransactionReader::readIpnResponse($response);
            /** @var PaymentNotification $invoice */
            $invoice = $paymentData['invoice'];

            $orderIncrementId = $invoice->getOrderNo();
            $this->_logger->debug('Order no:' . $orderIncrementId);

            $order            = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);
            $payment          = $order->getPayment();

            $this->_logger->debug('Payment method: '. $payment->getMethod());
            $this->_logger->debug('Method method: '. $this->method->getCode());
            ContextHelper::assertOrderPayment($payment);
            if ($payment->getMethod() === $this->method->getCode()) {
                $paymentDataObject = $this->paymentDataObjectFactory->create($payment);
                $this->commandPool->get('ipn')->execute(
                    [
                        'payment' => $paymentDataObject,
                        'response' => $paymentData,
                        'is_ipn' => true,
                        'amount' => $order->getTotalDue()
                    ]
                );
                $this->_logger->debug('Send Email here');
                $this->orderSender->send($order);
                echo 'NOTIFY_RECEIVED';
                return;
            }
            $this->_logger->debug('Fail to send email');
        } catch (\Exception $e) {
            $this->_objectManager->get('\Psr\Log\LoggerInterface')->critical($e->getMessage());
            $this->messageManager->addErrorMessage(__('Transaction has been declined. Please try again later.'));
            echo 'Verified is faillure.';
            return;
        }

        echo "<h3>I'm listening....</h3>";
    }

    /**
     * Create exception in case CSRF validation failed.
     * Return null if default exception will suffice.
     *
     * @param RequestInterface $request
     *
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * Perform custom request validation.
     * Return null if default validation is needed.
     *
     * @param RequestInterface $request
     *
     * @return boolean|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return  true;
    }
}
