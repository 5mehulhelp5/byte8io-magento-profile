<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model\Config\Type;

use Byte8\Profile\Model\Config\ConfigModel;

/**
 * @inheritDoc
 */
class LogConfig extends ConfigModel implements LogConfigInterface
{
    /**
     * @inheritDoc
     */
    public function isActiveRequestLog(): bool
    {
        return (bool) $this->getConfig($this->getTypeId() . self::XML_PATH_IS_ACTIVE_REQUEST_LOG);
    }

    /**
     * @inheritDoc
     */
    public function isActiveResponseLog(): bool
    {
        return (bool) $this->getConfig($this->getTypeId() . self::XML_PATH_IS_ACTIVE_RESPONSE_LOG);
    }
}