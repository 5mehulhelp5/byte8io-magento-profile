<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Byte8\Profile\Api\Data\HistoryInterface;
use Byte8\Profile\Api\Data\HistorySearchResultsInterface;
use Byte8\Profile\Api\Data\HistorySearchResultsInterfaceFactory;
use Byte8\Profile\Api\HistoryRepositoryInterface;
use Byte8\Profile\Model\ResourceModel;

/**
 * @inheritDoc
 */
class HistoryRepository implements HistoryRepositoryInterface
{
    /**
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ResourceModel\History $resource
     * @param ResourceModel\History\CollectionFactory $collectionFactory
     * @param HistoryFactory $historyFactory
     * @param HistorySearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly ResourceModel\History $resource,
        private readonly ResourceModel\History\CollectionFactory $collectionFactory,
        private readonly HistoryFactory $historyFactory,
        private readonly HistorySearchResultsInterfaceFactory $searchResultsFactory
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var HistorySearchResultsInterface $searchResults */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @inheritDoc
     */
    public function get($entityId, ?string $field = null)
    {
        /** @var HistoryInterface|History $history */
        $history = $this->historyFactory->create();
        $this->resource->load($history, $entityId, $field);
        if (!$history->getId()) {
            throw new NoSuchEntityException(__('The history with ID "%1" doesn\'t exist.', $entityId));
        }

        return $history;
    }

    /**
     * @inheritDoc
     */
    public function save(HistoryInterface $history)
    {
        try {
            $this->resource->save($history);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $history;
    }

    /**
     * @inheritDoc
     */
    public function delete(HistoryInterface $history)
    {
        try {
            $this->resource->delete($history);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($entityId)
    {
        return $this->delete($this->get($entityId));
    }
}
