<?php

namespace MageSuite\Cache\Model;

class CleanupLogRepository
{
    /**
     * @var ResourceModel\CleanupLog
     */
    protected $resourceModel;

    /**
     * @var CleanupLogFactory
     */
    protected $cleanupLogEntryFactory;

    public function __construct(
        CleanupLogFactory $cleanupLogEntryFactory,
        \MageSuite\Cache\Model\ResourceModel\CleanupLog $resourceModel
    )
    {
        $this->resourceModel = $resourceModel;
        $this->cleanupLogEntryFactory = $cleanupLogEntryFactory;
    }

    public function save($data)
    {
        /** @var CleanupLog $cleanupLogEntry */
        $cleanupLogEntry = $this->cleanupLogEntryFactory->create();
        $cleanupLogEntry->setContext($data);

        $this->resourceModel->save($cleanupLogEntry);
    }
}
