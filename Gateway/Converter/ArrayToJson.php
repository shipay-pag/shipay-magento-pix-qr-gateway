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
 *
 * See LICENSE for license details.
 */

namespace Shipay\PixQrGateway\Gateway\Converter;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Payment\Gateway\Http\ConverterInterface;

class ArrayToJson implements ConverterInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * JsonToArray constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function convert($response)
    {
        return $this->serializer->serialize($response);
    }
}
