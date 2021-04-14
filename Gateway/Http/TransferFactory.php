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

namespace Shipay\PixQrGateway\Gateway\Http;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Shipay\PixQrGateway\Gateway\Converter\Converter;

class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var UrlResolver
     */
    private $urlResolver;

    /**
     * TransferFactory constructor.
     * @param TransferBuilder $transferBuilder
     * @param Converter $converter
     * @param UrlResolver $urlResolver
     */
    public function __construct(
        TransferBuilder $transferBuilder,
        Converter $converter,
        UrlResolver $urlResolver
    ) {
        $this->transferBuilder = $transferBuilder;
        $this->converter = $converter;
        $this->urlResolver = $urlResolver;
    }

    /**
     * @param array $request
     * @return TransferInterface
     * @throws ConverterException
     * @throws LocalizedException
     */
    public function create(array $request)
    {
        $gatewayData = $this->resolveGatewayData($request);

        return $this->transferBuilder
            ->setUri($gatewayData['gateway_url'])
            ->setMethod($gatewayData['http_verb'])
            ->setBody($this->converter->convert($request))
            ->build();
    }

    /**
     * @param array $request
     * @return array
     */
    private function resolveGatewayData(array &$request)
    {
        $gatewayUrl = rtrim($this->urlResolver->getOrderUrl(), '/');

        if (array_key_exists('transaction_id_refund', $request) && !empty($request['transaction_id_refund'])) {
            $gatewayUrl = sprintf('%s/%s', $gatewayUrl, $request['transaction_id_refund']);
            unset($request['transaction_id_refund']);
            return [
                'gateway_url' => $gatewayUrl,
                'http_verb' => 'DELETE'
            ];
        }

        return [
            'gateway_url' => $gatewayUrl,
            'http_verb' => 'POST'
        ];
    }
}