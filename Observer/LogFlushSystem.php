<?php

namespace MageSuite\Cache\Observer;

class LogFlushSystem implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageSuite\Cache\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \MageSuite\Cache\Model\Command\GenerateBasicCleanupLogData
     */
    protected $generateBasicCleanupLogData;

    /**
     * @var \MageSuite\Cache\Model\CleanupLogRepository
     */
    protected $cleanupLogRepository;

    public function __construct(
        \MageSuite\Cache\Model\CleanupLogRepository $cleanupLogRepository,
        \MageSuite\Cache\Helper\Configuration $configuration,
        \MageSuite\Cache\Model\Command\GenerateBasicCleanupLogData $generateBasicCleanupLogData
    ) {
        $this->configuration = $configuration;
        $this->generateBasicCleanupLogData = $generateBasicCleanupLogData;
        $this->cleanupLogRepository = $cleanupLogRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->configuration->isLoggingEnabled()) {
            return;
        }

        $stackTrace = $this->getStackTrace();
        $data = $this->generateBasicCleanupLogData->execute($stackTrace);

        $data['redis'] = true;
        $data['flush_magento'] = true;

        $this->cleanupLogRepository->save($data);
    }

    protected function getStackTrace()
    {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            return $e->getTrace();
        }
    }
}
