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

use Magento\Payment\Gateway\CommandInterface;

/**
 * Class CompleteCommand
 *
 * @package Kohaku1907\PayooPayment\Gateway\Command
 */
class CompleteIpnCommand implements CommandInterface
{
    /**
     * @var UpdateIpnDetailsCommand
     */
    private $updateIpnDetailsCommand;

    /**
     * @var UpdateOrderCommand
     */
    private $updateOrderCommand;

    /**
     * @param UpdateIpnDetailsCommand $updateIpnDetailsCommand
     * @param UpdateOrderCommand   $updateOrderCommand
     */
    public function __construct(
        UpdateIpnDetailsCommand $updateIpnDetailsCommand,
        UpdateOrderCommand $updateOrderCommand
    ) {
        $this->updateIpnDetailsCommand = $updateIpnDetailsCommand;
        $this->updateOrderCommand   = $updateOrderCommand;
    }

    /**
     * @param array $commandSubject
     * @return \Magento\Payment\Gateway\Command\ResultInterface|void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Payment\Gateway\Command\CommandException
     */
    public function execute(array $commandSubject)
    {
        $this->updateIpnDetailsCommand->execute($commandSubject);
        $this->updateOrderCommand->execute($commandSubject);
    }
}
