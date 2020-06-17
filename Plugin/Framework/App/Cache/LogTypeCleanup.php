<?php

namespace MageSuite\Cache\Plugin\Framework\App\Cache;

class LogTypeCleanup
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

    public function afterCleanType(\Magento\Framework\App\Cache\TypeListInterface $subject, $result, $typeCode)
    {
        if (!$this->configuration->isLoggingEnabled()) {
            return $result;
        }

        $stackTrace = $this->getStackTrace();

        $data = $this->generateBasicCleanupLogData->execute($stackTrace);
        $data['cache_type'] = $typeCode;

        $this->logger->info('cache_clear', $data);

        return $result;
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
