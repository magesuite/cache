<?php

namespace MageSuite\Cache\Ui\Component\Listing\CleanupLog;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \MageSuite\Cache\Model\ResourceModel\CleanupLog\CollectionFactory $cleanupLogCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $cleanupLogCollectionFactory->create();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        return parent::getData();
    }
}
