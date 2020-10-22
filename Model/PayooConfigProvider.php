<?php
/************************************************************
 * *
 *  * Copyright © Kohaku1907. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    nguyenmtri11@gmail.com
 * *  @project   Payoo Payment
 */
namespace Kohaku1907\PayooPayment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;

/**
 * Class PayooConfigProvider
 *
 * @package Kohaku1907\PayooPayment\Model
 */
class PayooConfigProvider implements ConfigProviderInterface
{
    /**
     * Payoo Logo
     */
    const PAYOO_LOGO_SRC = 'https://www.payoo.vn/website/static/css/image/payoo-logo.png';

    /**
     * @var ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * PayooConfigProvider constructor.
     *
     * @param ResolverInterface $localeResolver
     * @param PaymentHelper     $paymentHelper
     * @param UrlInterface      $urlBuilder
     */
    public function __construct(
        ResolverInterface $localeResolver,
        PaymentHelper $paymentHelper,
        UrlInterface $urlBuilder
    ) {
        $this->localeResolver = $localeResolver;
        $this->paymentHelper  = $paymentHelper;
        $this->urlBuilder     = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'payooPayment' => [
                    'redirectUrl' => $this->urlBuilder->getUrl('payoo/payment/start'),
                    'logoSrc' => self::PAYOO_LOGO_SRC
                ]
            ]
        ];

        return $config;
    }
}
