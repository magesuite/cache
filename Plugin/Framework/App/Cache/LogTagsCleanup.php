<?php

namespace MageSuite\Cache\Plugin\Framework\App\Cache;

class LogTagsCleanup
{
    const BATCH_SIZE = 1000;

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
        $data['redis'] = true;

        $batches = array_chunk($tags, self::BATCH_SIZE);

        if(empty($tags)) {
            $data['empty_tags'] = true;
            $this->cleanupLogRepository->save($data);
        }

        foreach ($batches as $tagsBatch) {
            $data['tags'] = $tagsBatch;

            $this->cleanupLogRepository->save($data);
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
