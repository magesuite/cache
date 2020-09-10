<?php

namespace MageSuite\Cache\Plugin\Indexer\CacheContextPlugin;

class LogEntityRegistration
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

    public function afterRegisterEntities(\Magento\Framework\Indexer\CacheContext $subject, $result, $cacheTag, $ids)
    {
        if (!$this->configuration->isLoggingEnabled()) {
            return $result;
        }

        $tags = [];

        foreach($ids as $id) {
            $tags[] = $cacheTag . '_' . $id;
        }

        $tags = array_unique($tags);

        $stackTrace = $this->getStackTrace();
        $data = $this->generateBasicCleanupLogData->execute($stackTrace);
        $data['tag_registered_for_cleanup'] = true;

        $batches = array_chunk($tags, self::BATCH_SIZE);

        foreach ($batches as $tagsBatch) {
            $data['tags'] = $tagsBatch;

            $this->cleanupLogRepository->save($data);
        }

        return $result;
    }

    public function afterRegisterTags(\Magento\Framework\Indexer\CacheContext $subject, $result, $cacheTags)
    {
        if (!$this->configuration->isLoggingEnabled()) {
            return $result;
        }

        if (!is_array($cacheTags)) {
            $cacheTags = [$cacheTags];
        }

        $cacheTags = array_unique($cacheTags);

        $stackTrace = $this->getStackTrace();
        $data = $this->generateBasicCleanupLogData->execute($stackTrace);
        $data['tag_registered_for_cleanup'] = true;

        $batches = array_chunk($cacheTags, self::BATCH_SIZE);

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

