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

namespace Shipay\PixQrGateway\Model;

use Shipay\PixQrGateway\Api\WalletsRepositoryInterface;
use Shipay\PixQrGateway\Gateway\Http\GetWallets;

class WalletsRepository implements WalletsRepositoryInterface
{
    /**
     * @var GetWallets
     */
    private $getWallets;

    /**
     * @var Wallet
     */
    private $wallet;

    /**
     * WalletsRepository constructor.
     * @param GetWallets $getWallets
     * @param Wallet $wallet
     */
    public function __construct(
        GetWallets $getWallets,
        Wallet $wallet
    ) {
        $this->getWallets = $getWallets;
        $this->wallet = $wallet;
    }

    /**
     * @inheritDoc
     */
    public function getWallets()
    {
        try {
            $wallets = $this->getWallets->placeRequest();
        } catch (\Exception $exception) {
            return [];
        }

        if (empty($wallets) || !isset($wallets[self::WALLETS])) {
            return [];
        }

        $wallets = $this->fillWallets(
            $this->filterEnabledWallets($wallets[self::WALLETS])
        );

        array_multisort($wallets);

        $result = [
            [
                'value' => '',
                'text' => ''
            ]
        ];

        $pixArrayIndex = $this->getPixArrayIndex($wallets);

        if ($pixArrayIndex !== null) {
            $result[1] = $wallets[$pixArrayIndex];
            unset($wallets[$pixArrayIndex]);
        }

        foreach ($wallets as $wallet) {
            $result[] = $wallet;
        }

        return $result;
    }

    /**
     * @param array $wallets
     * @return array
     */
    private function fillWallets($wallets)
    {
        return array_reduce($wallets, function ($accumulator, $item) {
            $accumulator[] = [
                'value' => $item['name'],
                'label' => $this->wallet->getWalletLabel($item['name'])
            ];
            return $accumulator;
        }, []);
    }

    /**
     * @param array $wallets
     * @return array
     */
    private function filterEnabledWallets($wallets)
    {
        return array_filter($wallets, function ($item) {
            return isset($item['active']) && $item['active'] === true;
        });
    }

    /**
     * @param array $wallets
     * @return null|int
     */
    private function getPixArrayIndex($wallets)
    {
        $index = null;

        foreach ($wallets as $key => $wallet) {
            if ($wallet['value'] === Wallet::PIX) {
                $index = $key;
                break;
            }
        }

        return $index;
    }
}