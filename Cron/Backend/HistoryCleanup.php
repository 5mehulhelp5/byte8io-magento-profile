<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Cron\Backend;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Byte8\Profile\Api\Data\HistoryInterface;

/**
 * Class HistoryCleanup used to
 * clean-up profile history.
 */
class HistoryCleanup
{
    private const HISTORY_LIFETIME = 1209600;
    private const SECONDS_IN_DAY = 86400;
    private const XML_PATH_HISTORY_LIFETIME = 'byte8_profile/profile_config/history_lifetime';

    /**
     * @param DateTime $dateTime
     * @param ResourceConnection $resource
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly DateTime $dateTime,
        private readonly ResourceConnection $resource,
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $connection = $this->resource->getConnection();
        $connection->delete(
            $connection->getTableName(HistoryInterface::DB_TABLE_NAME),
            [
                HistoryInterface::CREATED_AT . ' < ?' => $connection->formatDate(
                    $this->dateTime->gmtTimestamp() - $this->getHistoryLifetime()
                )
            ]
        );
    }

    /**
     * @return int
     */
    private function getHistoryLifetime(): int
    {
        if ($historyLifetime = $this->scopeConfig->getValue(self::XML_PATH_HISTORY_LIFETIME)) {
            $historyLifetime = $historyLifetime * self::SECONDS_IN_DAY;
        } else {
            $historyLifetime = self::HISTORY_LIFETIME;
        }

        return $historyLifetime;
    }
}
