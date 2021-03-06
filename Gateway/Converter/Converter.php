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

use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\ConverterInterface;

class Converter
{
    /**
     * @var ConverterInterface
     */
    private $converter;

    /**
     * Converter constructor.
     * @param ConverterInterface $converter
     */
    public function __construct(ConverterInterface $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @param array $request
     * @return array|string
     * @throws ConverterException
     */
    public function convert(array $request)
    {
        return $this->converter->convert($request);
    }
}
