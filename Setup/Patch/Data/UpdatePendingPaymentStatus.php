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

namespace Shipay\PixQrGateway\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdatePendingPaymentStatus implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * SpecialMaterialInitialData constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();

        // Update the pending_payment status to be visible on front store.
        $tableName = 'sales_order_status_state';
        $where = ['state = ?' => 'pending_payment'];
        $data = ['visible_on_front' => 1];
        $connection->update($tableName, $data, $where);

        // handles pending payment status
        $sql = $connection->select()
            ->from(
                [$tableName],
                ['status', 'state', 'visible_on_front']
            )
            ->where('status = ?', 'pending_payment')
            ->where('state = ?', 'processing');

        $result = $connection->fetchAll($sql);

        if (empty($result)) {
            $connection->insert(
                $tableName,
                [
                    'status' => 'pending_payment',
                    'state' => 'processing',
                    'is_default' => 0,
                    'visible_on_front' => 1
                ]
            );
        } else {
            $bind = ['visible_on_front' => 1];
            $where = ['status = ?' => 'pending_payment', 'state = ?' => 'processing'];
            $connection->update($tableName, $bind, $where);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
