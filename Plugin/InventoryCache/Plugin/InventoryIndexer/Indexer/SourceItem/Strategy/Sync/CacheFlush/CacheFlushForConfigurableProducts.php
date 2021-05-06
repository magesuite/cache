<?php

namespace MageSuite\Cache\Plugin\InventoryCache\Plugin\InventoryIndexer\Indexer\SourceItem\Strategy\Sync\CacheFlush;

class CacheFlushForConfigurableProducts
{
    const PRODUCT_TABLE_NAME = 'catalog_product_entity';
    const PRODUCT_CONFIGURABLE_RELATION_TABLE = 'catalog_product_super_link';

    /**
     * @var \Magento\InventoryCache\Model\FlushCacheByProductIds
     */
    protected $flushCacheByIds;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * @var string
     */
    protected $productEntityLinkField;

    public function __construct(
        \Magento\InventoryCache\Model\FlushCacheByProductIds $flushCacheByIds,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->flushCacheByIds = $flushCacheByIds;
        $this->metadataPool = $metadataPool;
        $this->resource = $resource;
    }

    public function afterAfterExecuteList(
        \Magento\InventoryCache\Plugin\InventoryIndexer\Indexer\SourceItem\Strategy\Sync\CacheFlush $subject,
        $subjectResult,
        \Magento\InventoryIndexer\Indexer\SourceItem\Strategy\Sync $sync,
        $result,
        array $sourceItemIds
    )
    {
        $configurableProductIds = $this->getConfigurableProductIdsBySourceItemIds($sourceItemIds);
        $this->flushCacheByIds->execute($configurableProductIds);
    }

    /**
     * Get parent product ids for simple products if any exist.
     *
     * @param array $sourceItemIds
     * @return array
     */
    public function getConfigurableProductIdsBySourceItemIds($sourceItemIds): array
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select()
            ->from(
                ['source_item' => $this->resource->getTableName(\Magento\Inventory\Model\ResourceModel\SourceItem::TABLE_NAME_SOURCE_ITEM)],
                []
            )->where(
                'source_item.' . \Magento\Inventory\Model\ResourceModel\SourceItem::ID_FIELD_NAME . ' IN (?)',
                $sourceItemIds
            )->join(
                ['product' => $this->resource->getTableName(self::PRODUCT_TABLE_NAME)],
                'source_item.' . \Magento\InventoryApi\Api\Data\SourceItemInterface::SKU . ' = product.sku',
                []
            )->joinLeft(
                ['configurable' => $this->resource->getTableName(self::PRODUCT_CONFIGURABLE_RELATION_TABLE)],
                'configurable.product_id = product.' . $this->getProductEntityLinkField(),
                ['configurable.parent_id']
            )->distinct();
        
        return $connection->fetchCol($select);
    }

    protected function getProductEntityLinkField()
    {
        if (!$this->productEntityLinkField) {
            $this->productEntityLinkField = $this->metadataPool
                ->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class)
                ->getLinkField();
        }
        return $this->productEntityLinkField;
    }
}