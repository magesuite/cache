<?php

namespace MageSuite\Cache\Observer;

class LogFlushSystem implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \MageSuite\Cache\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \MageSuite\Cache\Model\Command\GenerateBasicCleanupLogData
     */
    protected $generateBasicCleanupLogData;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \MageSuite\Cache\Helper\Configuration $configuration,
        \MageSuite\Cache\Model\Command\GenerateBasicCleanupLogData $generateBasicCleanupLogData
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->generateBasicCleanupLogData = $generateBasicCleanupLogData;
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

        $data['flush_magento'] = true;

        $this->logger->info('cache_clear', $data);
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
