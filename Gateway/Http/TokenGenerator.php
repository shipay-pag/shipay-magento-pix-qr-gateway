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
use Magento\Framework\Phrase;
use Psr\Log\LoggerInterface;
use Shipay\PixQrGateway\Gateway\Config\Config;
use Shipay\PixQrGateway\Model\Cache\Type as ShipayTokenCache;
use Magento\Framework\Serialize\SerializerInterface;

class TokenGenerator
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlResolver
     */
    private $urlResolver;

    /**
     * @var ShipayTokenCache
     */
    private $cache;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * TokenGenerator constructor.
     * @param Config $config
     * @param UrlResolver $urlResolver
     * @param ShipayTokenCache $cache
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Config $config,
        UrlResolver $urlResolver,
        ShipayTokenCache $cache,
        LoggerInterface $logger,
        SerializerInterface $serializer
    ) {
        $this->config = $config;
        $this->urlResolver = $urlResolver;
        $this->cache = $cache;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function issueToken()
    {
        $token = $this->cache->load(ShipayTokenCache::TYPE_IDENTIFIER);

        if ($token) {
            // phpcs:disable
            return $this->serializer->unserialize($token);
            // phpcs:enable
        }

        $result = $this->generateNewToken();

        $newToken = $result[AuthenticationFieldsInterface::ACCESS_TOKEN];

        $timeToken = $result[AuthenticationFieldsInterface::TIME_TOKEN];

        // phpcs:disable
        $this->cache->save(
            $this->serializer->serialize($newToken),
            ShipayTokenCache::TYPE_IDENTIFIER,
            [],
            $timeToken
        );
        // phpcs:enable

        return $newToken;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    private function generateNewToken()
    {
        // phpcs:disable
        $channel = curl_init($this->urlResolver->getAuthUrl());

        curl_setopt($channel, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($channel, CURLOPT_POSTFIELDS, $this->getAuthPayload());
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($channel, CURLOPT_HTTPHEADER, $this->getHeaders());

        $response = curl_exec($channel);

        curl_close($channel);
        // phpcs:enable

        if ($response === false) {
            $this->throwExeption();
        }

        $response = $this->serializer->unserialize($response);

        if (!isset($response[AuthenticationFieldsInterface::ACCESS_TOKEN])) {
            $this->throwExeption();
        }

        return $response;
    }

    /**
     * @return string
     */
    private function getAuthPayload()
    {
        return $this->serializer->serialize([
            AuthenticationFieldsInterface::ACCESS_KEY => $this->config->getAccessKey(),
            AuthenticationFieldsInterface::SECRET_KEY => $this->config->getSecretKey(),
            AuthenticationFieldsInterface::CLIENT_ID => $this->config->getClientId()
        ]);
    }

    /**
     * @return array
     */
    private function getHeaders()
    {
        return [
            'Content-Type: application/json',
        ];
    }

    /**
     * @throws LocalizedException
     */
    private function throwExeption()
    {
        throw new LocalizedException(
            new Phrase(
                __('It was not possible to generate the token in the Shipay API.')
            )
        );
    }
}
