<?php

declare(strict_types=1);

/**
 * DISCLAIMER
 *
 * Do not edit this file if you want to update this module for future new versions.
 *
 * @category Shipay
 * @package Shipay_PixQrGateway
 * @copyright Copyright (c) 2021 Shipay
 * @author Shipay <ajuda@shipay.com.br>
 */

namespace Shipay\PixQrGateway\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Psr\Log\LoggerInterface;
use Shipay\PixQrGateway\Gateway\Enums\PaymentStatus;

class RefundReponseValidator extends AbstractValidator
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string[]
     */
    private $errorMessages = [
        'Order already cancelled',
        'Order already expired',
        'Order already refunded'
    ];

    /**
     * @var string[]
     */
    private $statuses = [
        PaymentStatus::PARTIAL_REFUNDED,
        PaymentStatus::REFUNDED,
    ];

    /**
     * GeneralReponseValidator constructor.
     * @param ResultInterfaceFactory $resultFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($resultFactory);
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $response = $validationSubject['response'];

        $isValid = true;
        $errorMessages = [];

        if (!isset($response['status']) || !in_array($response['status'], $this->statuses)) {
            $isValid = false;
        }

        if (isset($response['code']) &&
            $response['code'] === 400 &&
            isset($response['message']) &&
            in_array($response['message'], $this->errorMessages)
        ) {
            $isValid = true;
        }

        return $this->createResult($isValid, $errorMessages);
    }
}
