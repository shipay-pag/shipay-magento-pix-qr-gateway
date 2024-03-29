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

namespace Shipay\PixQrGateway\Gateway\Request\Builder;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Payment as OrderPayment;
use Shipay\PixQrGateway\Gateway\Http\UrlResolver;
use Shipay\PixQrGateway\Gateway\Request\RequestFieldsInterface;
use Shipay\PixQrGateway\Observer\DataAssignObserver;

class Payment implements BuilderInterface
{
    /**
     * @var UrlResolver
     */
    private $urlResolver;

    /**
     * Payment constructor.
     * @param UrlResolver $urlResolver
     */
    public function __construct(UrlResolver $urlResolver)
    {
        $this->urlResolver = $urlResolver;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @throws LocalizedException
     */
    public function build(array $buildSubject)
    {
        /** @var PaymentDataObjectInterface $paymentDataObject */
        $paymentDataObject = $buildSubject['payment'];

        /** @var InfoInterface|OrderPayment $payment */
        $payment = $paymentDataObject->getPayment();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $paymentDataObject->getOrder();

        $data = [
            RequestFieldsInterface::ORDER_REF => $buildSubject['payment']->getPayment()->getOrder()->getIncrementId(),
            RequestFieldsInterface::WALLET => $payment->getAdditionalInformation(DataAssignObserver::WALLET),
            RequestFieldsInterface::TOTAL => $this->getTransactionValue($buildSubject),
            RequestFieldsInterface::CALLBACK_URL => $this->urlResolver->getCallbackUrl(
                $order->getStoreId()
            )
        ];

        return $data;
    }

    /**
     * @param array $buildSubject
     * @return string
     * @throws LocalizedException
     */
    private function getTransactionValue(array $buildSubject)
    {
        $amount = (float)$buildSubject['amount'] ?? 0;

        if ($amount <= 0) {
            throw new LocalizedException(
                new Phrase(
                    __('The amount to be paid must be greater than 0.')
                )
            );
        }

        return $amount;
    }
}
