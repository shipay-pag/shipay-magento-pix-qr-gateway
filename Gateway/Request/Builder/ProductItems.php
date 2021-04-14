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

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Shipay\PixQrGateway\Gateway\Request\RequestFieldsInterface;
use Shipay\PixQrGateway\Model\Math;

class ProductItems implements BuilderInterface
{
    /**
     * @var Math
     */
    private $math;

    /**
     * ProductItems constructor.
     * @param Math $math
     */
    public function __construct(Math $math)
    {
        $this->math = $math;
    }

    /**
     * @inheritDoc
     */
    public function build(array $buildSubject)
    {
        /** @var PaymentDataObjectInterface $paymentDataObject */
        $paymentDataObject = $buildSubject['payment'];

        /** @var OrderInterface $order */
        $order = $paymentDataObject->getPayment()->getOrder();

        $productItems = $this->getProductItems($order);
        $productItems[] = $this->getOtherItem($productItems, $buildSubject);

        $data = [
            RequestFieldsInterface::ITEMS => $productItems
        ];

        return $data;
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    private function getProductItems(OrderInterface $order)
    {
        return array_reduce($order->getItems(), function ($accumulator, $item) {
            /** @var OrderItemInterface $item */
            $accumulator[] = [
                RequestFieldsInterface::ITEM_TITLE => $item->getName(),
                RequestFieldsInterface::UNIT_PRICE => $item->getPrice(),
                RequestFieldsInterface::QUANTITY => $item->getQtyOrdered(),
            ];
            return $accumulator;
        }, []);
    }

    /**
     * @param array $productItems
     * @param array $buildSubject
     * @return array
     */
    private function getOtherItem($productItems, $buildSubject)
    {
        $totalProductsValue = $this->getTotalProductsValue($productItems);

        $diference = (float)$this->math
            ->subtract($buildSubject['amount'], $totalProductsValue);

        return [
            RequestFieldsInterface::ITEM_TITLE => __('Others'),
            RequestFieldsInterface::UNIT_PRICE => $diference,
            RequestFieldsInterface::QUANTITY => 1,
        ];
    }

    /**
     * @param $productItems
     * @return float
     */
    private function getTotalProductsValue($productItems)
    {
        return (float)array_reduce($productItems, function ($accumulator, $item) {
            if (!isset($item[RequestFieldsInterface::UNIT_PRICE]) ||
                !isset($item[RequestFieldsInterface::UNIT_PRICE])
            ) {
                return $accumulator;
            }

            $rowTotal = $this->math
                ->multiply(
                    $item[RequestFieldsInterface::UNIT_PRICE],
                    $item[RequestFieldsInterface::QUANTITY]
                );

            return $this->math
                ->add($accumulator, $rowTotal);
        }, 0);
    }
}