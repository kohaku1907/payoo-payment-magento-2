<?php
namespace Kohaku1907\PayooPayment\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Checkout\Model\Session;

/**
 * Class SendMailOnOrderSuccess
 * @package Kohaku1907\PayooPayment\Observer
 */
class SendMailOnOrderSuccess implements ObserverInterface
{
    /**
     * @var OrderFactory
     */
    protected $orderModel;

    /**
     * @var OrderSender
     */
    protected $orderSender;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @param \Magento\Sales\Model\OrderFactory $orderModel
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     * @param \Magento\Checkout\Model\Session $checkoutSession
     *
     * @codeCoverageIgnore
     */
    public function __construct(OrderFactory $orderModel, OrderSender $orderSender, Session $checkoutSession) {
        $this->orderModel = $orderModel;
        $this->orderSender = $orderSender;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $orderIds = $observer->getEvent()->getOrderIds();
        if(count($orderIds)) {
            $this->checkoutSession->setForceOrderMailSentOnSuccess(true);
            $order = $this->orderModel->create()->load($orderIds[0]);
            $this->orderSender->send($order, true);
        }
    }
}
