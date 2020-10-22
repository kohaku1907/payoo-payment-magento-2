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

use Kohaku1907\PayooPayment\Gateway\Helper\Rate;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

/**
 * Class OrderDetailsDataBuilder
 *
 * @package Kohaku1907\PayooPayment\Gateway\Request
 */
class OrderDetailsDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Rate
     */
    private $helperRate;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * OrderDetailsDataBuilder constructor.
     *
     * @param ConfigInterface       $config
     * @param StoreManagerInterface $storeManager
     * @param Rate                  $helperRate
     * @param UrlInterface          $urlBuilder
     */
    public function __construct(
        ConfigInterface $config,
        StoreManagerInterface $storeManager,
        Rate $helperRate,
        UrlInterface $urlBuilder
    ) {
        $this->config       = $config;
        $this->helperRate   = $helperRate;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function build(array $buildSubject)
    {
        $orderXml = $this->buildOrderXml($buildSubject);
        $checksum = hash('sha512', $this->config->getValue('secret_key') . $orderXml);
        return [
            'data' => $orderXml,
            'checksum' => $checksum,
            'refer' => $this->urlBuilder->getUrl()
        ];

    }

    /**
     * @param array $buildSubject
     * @return string
     */
    protected function buildOrderXml(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDO->getPayment();
        $order   = $payment->getOrder();
        $billingAddress = $order->getBillingAddress();

        $order_ship_date = date('d/m/Y', strtotime('+1 day', time()));

        $validity_time = date('YmdHis', strtotime('+1 day', time()));
        $description = '';
        $items = $order->getAllVisibleItems();
        foreach ($items as $item) {
            $product = $item->getProduct();
            $description .= $product->getName() . ' X ' . $item->getQtyOrdered() . ' | ';
        }
        $description = rtrim($description, "| ");
        if(strlen($description) <= 50) {
            $description = str_pad($description, 51, "#", STR_PAD_RIGHT);
        }

        return '<shops><shop>' .
            '<session>' . $order->getIncrementId() . '</session>' .
            '<username>' . $this->config->getValue('username') . '</username>' .
            '<shop_id>' . $this->config->getValue('shop_id') . '</shop_id>' .
            '<shop_title>' . $this->config->getValue('shop_title') . '</shop_title>' .
            '<shop_domain>' . rtrim($this->urlBuilder->getBaseUrl(), '/') . '</shop_domain>' .
            '<shop_back_url>' . $this->urlBuilder->getUrl('payoo/payment/return') . '</shop_back_url>' .
            '<order_no>' . $order->getIncrementId() . '</order_no>' .
            '<order_cash_amount>' . (string) $this->helperRate->getVndAmount($order, round((float)SubjectReader::readAmount($buildSubject), 2)) . '</order_cash_amount>' .
            '<order_ship_date>' . $order_ship_date . '</order_ship_date>' .
            '<order_ship_days>' . 0 . '</order_ship_days>' .
            '<order_description>' . urlencode(preg_replace('/[^A-Za-z0-9\-]/', '', $description)) . '</order_description>' .
            '<validity_time>' .  $validity_time . '</validity_time>' .
            '<notify_url>' . $this->urlBuilder->getUrl('payoo/payment/ipn') . '</notify_url>' .
            '<customer>' .
            '<name>' . $billingAddress->getFirstname() . ' '. $billingAddress->getLastname() . '</name>' .
            '<phone>' . preg_replace('/\D+/', '', $billingAddress->getTelephone()) . '</phone>' .
            '<email>' . $billingAddress->getEmail() . '</email>' .
            '</customer>' .
            '</shop></shops>';
    }
}
