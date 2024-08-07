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
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Payment as OrderPayment;

class Refund implements BuilderInterface
{
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

        $transactionId = $payment['refund_transaction_id'] ?? $payment['last_trans_id'];
        if (!$transactionId) {
            throw new LocalizedException(__('No authorization transaction to proceed refund.'));
        }

        return [
            'transaction_id_refund' => $transactionId,
            'amount' => $payment->getCreditmemo()->getGrandTotal()
        ];
    }
}
