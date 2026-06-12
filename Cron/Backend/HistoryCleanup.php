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
use Monolog\Logger as MonoLogger;
use Byte8\Core\Logger\LogProcessorInterface;
use Byte8\Core\Model\Trait\BatchPurgeTrait;
use Byte8\Profile\Api\Data\HistoryInterface;

/**
 * Class HistoryCleanup
 *
 * Removes old byte8_profile_history rows past the configured retention
 * window, deleting in bounded batches via BatchPurgeTrait.
 */
class HistoryCleanup
{
    use BatchPurgeTrait;

    public const XML_PATH_HISTORY_LIFETIME = 'byte8_profile/profile_config/history_lifetime';

    private const DEFAULT_RETENTION_DAYS = 14;
    private const MIN_RETENTION_DAYS = 1;
    private const BATCH_SIZE = 5000;
    private const MAX_BATCHES = 100;
    private const LOG_TAG = 'Profile History Cleanup';

    /**
     * @param DateTime $dateTime
     * @param ResourceConnection $resourceConnection
     * @param ScopeConfigInterface $scopeConfig
     * @param LogProcessorInterface $logger
     */
    public function __construct(
        private readonly DateTime $dateTime,
        private readonly ResourceConnection $resourceConnection,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly LogProcessorInterface $logger
    ) {
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $retentionDays = (int) ($this->scopeConfig->getValue(self::XML_PATH_HISTORY_LIFETIME)
            ?: self::DEFAULT_RETENTION_DAYS);
        if ($retentionDays < self::MIN_RETENTION_DAYS) {
            $retentionDays = self::MIN_RETENTION_DAYS;
        }

        $cutoff = $this->dateTime->gmtDate(null, strtotime(sprintf('-%d days', $retentionDays)));

        try {
            $stats = $this->purgeByAge(
                HistoryInterface::DB_TABLE_NAME,
                HistoryInterface::CREATED_AT,
                $cutoff,
                self::BATCH_SIZE,
                self::MAX_BATCHES
            );

            $this->logger->execute(
                self::LOG_TAG,
                [
                    'status' => 'completed',
                    'cutoff' => $cutoff,
                    'retention_days' => $retentionDays,
                    'batch_size' => self::BATCH_SIZE,
                    'max_batches' => self::MAX_BATCHES,
                    'batches_run' => $stats['batches_run'],
                    'rows_deleted' => $stats['rows_deleted'],
                    'duration_seconds' => $stats['duration_seconds'],
                    'exhausted' => $stats['exhausted']
                ],
                MonoLogger::INFO
            );
        } catch (\Exception $e) {
            $this->logger->execute(
                self::LOG_TAG . ' Error',
                [
                    'status' => 'failed',
                    'cutoff' => $cutoff,
                    'retention_days' => $retentionDays,
                    'error' => $e->getMessage()
                ],
                MonoLogger::ERROR
            );
        }
    }
}
