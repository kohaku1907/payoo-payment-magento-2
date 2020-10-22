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

use Kohaku1907\PayooPayment\Gateway\Validator\AbstractResponseValidator;
use Magento\Payment\Gateway\Command\Result\ArrayResult;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Command\ResultInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Command\Result\ArrayResultFactory;

/**
 * Class GetPayUrlCommand
 *
 * @package Kohaku1907\PayooPayment\Gateway\Command
 */
class GetPayUrlCommand implements CommandInterface
{
    /**
     * @var BuilderInterface
     */
    private $requestBuilder;

    /**
     * @var TransferFactoryInterface
     */
    private $transferFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ArrayResultFactory
     */
    private $resultFactory;

    /**
     * Constructor
     *
     * @param BuilderInterface         $requestBuilder
     * @param TransferFactoryInterface $transferFactory
     * @param ClientInterface          $client
     * @param ArrayResultFactory       $resultFactory
     * @param ValidatorInterface       $validator
     */
    public function __construct(
        BuilderInterface $requestBuilder,
        TransferFactoryInterface $transferFactory,
        ClientInterface $client,
        ArrayResultFactory $resultFactory,
        ValidatorInterface $validator
    ) {
        $this->requestBuilder  = $requestBuilder;
        $this->transferFactory = $transferFactory;
        $this->client          = $client;
        $this->resultFactory   = $resultFactory;
        $this->validator       = $validator;
    }

    /**
     * @param array $commandSubject
     * @return ArrayResult|ResultInterface|null
     * @throws CommandException
     * @throws ClientException
     * @throws ConverterException
     */
    public function execute(array $commandSubject)
    {
        $transferO = $this->transferFactory->create($this->requestBuilder->build($commandSubject));
        $response  = $this->client->placeRequest($transferO);
        $result    = $this->validator->validate(array_merge($commandSubject, ['response' => $response]));

        if (!$result->isValid()) {
            throw new CommandException(__(implode("\n", $result->getFailsDescription())));
        }

        return $this->resultFactory->create(
            [
                'array' => [
                    AbstractResponseValidator::PAY_URL => $response['order'][AbstractResponseValidator::PAY_URL]
                ]
            ]
        );
    }
}
