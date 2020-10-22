<?php
 /************************************************************
  * *
  *  * Copyright © Kohaku1907. All rights reserved.
  *  * See COPYING.txt for license details.
  *  *
  *  * @author    nguyenmtri11@gmail.com
  * *  @project   Payoo Payment
  */
use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Kohaku1907_PayooPayment',
    __DIR__
);
