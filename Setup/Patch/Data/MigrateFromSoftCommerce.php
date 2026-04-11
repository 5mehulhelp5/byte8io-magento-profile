<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Migrates DML data for Byte8_Profile from the legacy SoftCommerce vendor.
 *
 * Scope: Profile-domain modules only (Profile, ProfileHistory, ProfileQueue,
 * ProfileSchedule, ProfileConfig — all consolidated into Byte8_Profile).
 *
 * Plenty modules are handled by Byte8\PlentyCore\Setup\Patch\Data\MigrateFromSoftCommerce.
 * Profile notification is handled by Byte8\ProfileNotification\Setup\Patch\Data\MigrateFromSoftCommerce.
 *
 * Renamed/consolidated patch classes are recognized via getAliases() on each
 * patch — no patch_name rewrite is needed here.
 *
 * Table renames (DDL) are handled separately by the Schema Patch:
 * @see \Byte8\Profile\Setup\Patch\Schema\MigrateTablesFromSoftCommerce
 */
class MigrateFromSoftCommerce implements DataPatchInterface
{
    /**
     * Old module names that were consolidated into Byte8_Profile.
     */
    private const MODULE_RENAMES = [
        'SoftCommerce_Profile' => 'Byte8_Profile',
        'SoftCommerce_ProfileHistory' => 'Byte8_Profile',
        'SoftCommerce_ProfileQueue' => 'Byte8_Profile',
        'SoftCommerce_ProfileSchedule' => 'Byte8_Profile',
        'SoftCommerce_ProfileConfig' => 'Byte8_Profile',
    ];

    private const CONFIG_PATH_OLD_PREFIX = 'softcommerce_profile';
    private const CONFIG_PATH_NEW_PREFIX = 'byte8_profile';

    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * @inheritDoc
     */
    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->migrateSetupModule();
        $this->migrateConfigPaths();
        $this->migrateAclRules();

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    private function migrateSetupModule(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('setup_module');

        foreach (self::MODULE_RENAMES as $oldModule => $newModule) {
            $exists = $connection->fetchOne(
                $connection->select()
                    ->from($tableName, ['COUNT(*)'])
                    ->where('module = ?', $oldModule)
            );

            if (!$exists) {
                continue;
            }

            $newExists = $connection->fetchOne(
                $connection->select()
                    ->from($tableName, ['COUNT(*)'])
                    ->where('module = ?', $newModule)
            );

            if ($newExists) {
                $connection->delete($tableName, ['module = ?' => $oldModule]);
            } else {
                $connection->update(
                    $tableName,
                    ['module' => $newModule],
                    ['module = ?' => $oldModule]
                );
            }
        }
    }

    private function migrateConfigPaths(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('core_config_data');

        $connection->update(
            $tableName,
            ['path' => new \Zend_Db_Expr(sprintf(
                "REPLACE(path, '%s', '%s')",
                self::CONFIG_PATH_OLD_PREFIX,
                self::CONFIG_PATH_NEW_PREFIX
            ))],
            ['path LIKE ?' => self::CONFIG_PATH_OLD_PREFIX . '%']
        );
    }

    private function migrateAclRules(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('authorization_rule');

        if (!$connection->isTableExists($tableName)) {
            return;
        }

        $oldModules = array_keys(self::MODULE_RENAMES);
        foreach ($oldModules as $oldModule) {
            $newModule = self::MODULE_RENAMES[$oldModule];
            $connection->update(
                $tableName,
                ['resource_id' => new \Zend_Db_Expr(sprintf(
                    "REPLACE(resource_id, '%s', '%s')",
                    $oldModule,
                    $newModule
                ))],
                ['resource_id LIKE ?' => $oldModule . '::%']
            );
        }
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
