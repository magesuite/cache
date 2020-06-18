<?php

namespace MageSuite\Cache\Plugin\Framework\App\Cache;

class LogTagsCleanup
{
    const BATCH_SIZE = 1000;

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

    public function afterClean(\Magento\Framework\App\Cache $subject, $result, $tags = [])
    {
        if (!$this->configuration->isLoggingEnabled()) {
            return $result;
        }

        if (!is_array($tags)) {
            $tags = [$tags];
        }

        $tags = array_unique($tags);

        $stackTrace = $this->getStackTrace();
        $data = $this->generateBasicCleanupLogData->execute($stackTrace);

        $batches = array_chunk($tags, self::BATCH_SIZE);

        foreach ($batches as $tagsBatch) {
            $data['tags'] = $tagsBatch;

            $this->logger->info('cache_clear', $data);
        }

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
