<?php

namespace MageSuite\Cache\Observer;

class LogFullPageCacheCleanup implements \Magento\Framework\Event\ObserverInterface
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

    /**
     * @var \Magento\Framework\App\Cache\Tag\Resolver
     */
    protected $tagResolver;

    /**
     * @var \MageSuite\Cache\Model\CleanupLogRepository
     */
    protected $cleanupLogRepository;

    public function __construct(
        \MageSuite\Cache\Model\CleanupLogRepository $cleanupLogRepository,
        \MageSuite\Cache\Helper\Configuration $configuration,
        \MageSuite\Cache\Model\Command\GenerateBasicCleanupLogData $generateBasicCleanupLogData,
        \Magento\Framework\App\Cache\Tag\Resolver $tagResolver
    ) {
        $this->configuration = $configuration;
        $this->generateBasicCleanupLogData = $generateBasicCleanupLogData;
        $this->tagResolver = $tagResolver;
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

        $object = $observer->getEvent()->getObject();
        $tags = $this->tagResolver->getTags($object);

        if(empty($tags)) {
            return;
        }

        $stackTrace = $this->getStackTrace();
        $data = $this->generateBasicCleanupLogData->execute($stackTrace);
        $data['varnish'] = true;

        $batches = array_chunk($tags, \MageSuite\Cache\Plugin\Framework\App\Cache\LogTagsCleanup::BATCH_SIZE);

        foreach ($batches as $tagsBatch) {
            $data['tags'] = $tagsBatch;

            $this->cleanupLogRepository->save($data);
        }
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
