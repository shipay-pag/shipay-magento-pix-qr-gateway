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

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Shipay\PixQrGateway\Gateway\Config\Config;

class UrlResolver
{
    const ORDER_URI = '/order';
    const AUTH_URI = '/pdvauth';
    const WALLETS_URI = '/wallets';
    const CALLBACK_URI = '%srest/%s/V1/shipay/webhook';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlInterface
     */
    private $urlService;

    /**
     * @var Config
     */
    private $config;

    /**
     * UrlResolver constructor.
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlService
     * @param Config $config
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        UrlInterface $urlService,
        Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->urlService = $urlService;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getOrderUrl()
    {
        return sprintf('%s%s', $this->config->getGatewayUrl(), self::ORDER_URI);
    }

    /**
     * @return string
     */
    public function getAuthUrl()
    {
        return sprintf('%s%s', $this->config->getGatewayUrl(), self::AUTH_URI);
    }

    /**
     * @return string
     */
    public function getWalletsUrl()
    {
        return sprintf('%s%s', $this->config->getGatewayUrl(), self::WALLETS_URI);
    }

    /**
     * @return string
     */
    public function getTransactionUrl($transactionId)
    {
        return sprintf('%s%s/%s', $this->config->getGatewayUrl(), self::ORDER_URI, $transactionId);
    }

    /**
     * @param int $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCallbackUrl($storeId)
    {
        $storeCode = $this->storeManager
            ->getStore($storeId)
            ->getCode();

        return sprintf(self::CALLBACK_URI, $this->urlService->getBaseUrl(), $storeCode);
    }
}