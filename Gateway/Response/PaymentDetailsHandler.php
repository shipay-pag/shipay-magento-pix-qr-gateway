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

namespace Shipay\PixQrGateway\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;

class PaymentDetailsHandler implements HandlerInterface
{
    /**
     * @var array
     */
    private $responseFields = [
        ResponseFieldsInterface::DEEP_LINK,
        ResponseFieldsInterface::ORDER_ID,
        ResponseFieldsInterface::PIX_PSP,
        ResponseFieldsInterface::QR_CODE,
        ResponseFieldsInterface::QR_CODE_TEXT,
        ResponseFieldsInterface::STATUS,
        ResponseFieldsInterface::WALLET,
    ];

    /**
     * @param array $handlingSubject
     * @param array $response
     * @return void|null
     */
    public function handle(array $handlingSubject, array $response)
    {
        /** @var PaymentDataObjectInterface $paymentDataObject */
        $paymentDataObject = $handlingSubject['payment'];

        /** @var InfoInterface $payment */
        $payment = $paymentDataObject->getPayment();

        foreach ($this->responseFields as $field) {
            if (isset($response[$field])) {
                $payment->setAdditionalInformation($field, $response[$field]);
            }
        }
    }
}