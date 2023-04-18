<?php

namespace Ampersand\DisableStockReservation\Model\InventoryApi;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;

class GetInStockSourceItemsBySkusAndSortedSource
{
    /**
     * @var SourceItemRepositoryInterface
     */
    private $sourceItemRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param SourceItemRepositoryInterface $sourceItemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        SourceItemRepositoryInterface $sourceItemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->sourceItemRepository = $sourceItemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve source items for a defined set of SKUs and sorted source codes
     *
     * @param array $skus
     * @param array $sortedSourceCodes
     * @return SourceItemInterface[]
     */
    public function execute(array $skus, array $sortedSourceCodes): array
    {
        $skus = array_map('strval', $skus);
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceItemInterface::SKU, $skus, 'in')
            ->addFilter(SourceItemInterface::SOURCE_CODE, $sortedSourceCodes, 'in')
            //->addFilter(SourceItemInterface::STATUS, SourceItemInterface::STATUS_IN_STOCK)
            ->create();

        $items = $this->sourceItemRepository->getList($searchCriteria)->getItems();

        $itemsSorting = [];
        foreach ($items as $item) {
            $itemsSorting[] = array_search($item->getSourceCode(), $sortedSourceCodes, true);
        }

        array_multisort($itemsSorting, SORT_NUMERIC, SORT_ASC, $items);
        return $items;
    }
}