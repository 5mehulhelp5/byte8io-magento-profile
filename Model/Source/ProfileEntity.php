<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Byte8\Profile\Api\Data\ProfileInterface;
use Byte8\Profile\Model\GetProfileDataInterface;

/**
 * @inheritDoc
 */
class ProfileEntity implements OptionSourceInterface
{
    /**
     * @var array|null
     */
    private ?array $options = null;

    /**
     * @param GetProfileDataInterface $getProfileData
     */
    public function __construct(
        private readonly GetProfileDataInterface $getProfileData
    ) {
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): ?array
    {
        if (null === $this->options) {
            $this->options = [];
            foreach ($this->getProfileData->execute() as $item) {
                $this->options[] = [
                    'value' => $item[ProfileInterface::ENTITY_ID],
                    'label' => $item[ProfileInterface::NAME] ?? $item[ProfileInterface::ENTITY_ID],
                ];
            }
        }

        return $this->options;
    }
}
