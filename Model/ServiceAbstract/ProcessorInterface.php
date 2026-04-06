<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model\ServiceAbstract;

/**
 * Interface ProcessorInterface used to process
 * profile services.
 */
interface ProcessorInterface extends ServiceInterface
{
    /**
     * @return ServiceInterface
     */
    public function getContext();
}
