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

    /**
     * @var \Magento\Framework\App\Cache\Tag\Strategy\Factory
     */
    protected $tagResolvingStrategy;

    public function __construct(
        \MageSuite\Cache\Model\CleanupLogRepository $cleanupLogRepository,
        \MageSuite\Cache\Helper\Configuration $configuration,
        \MageSuite\Cache\Model\Command\GenerateBasicCleanupLogData $generateBasicCleanupLogData,
        \Magento\Framework\App\Cache\Tag\Resolver $tagResolver,
        \Magento\Framework\App\Cache\Tag\Strategy\Factory $tagResolvingStrategy
    ) {
        $this->configuration = $configuration;
        $this->generateBasicCleanupLogData = $generateBasicCleanupLogData;
        $this->tagResolver = $tagResolver;
        $this->cleanupLogRepository = $cleanupLogRepository;
        $this->tagResolvingStrategy = $tagResolvingStrategy;
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
        $tagResolvingStrategy = $this->tagResolvingStrategy->getStrategy($object);
        $tags = $this->tagResolver->getTags($object);

        if(empty($tags)) {
            return;
        }

        $stackTrace = $this->getStackTrace();
        $data = $this->generateBasicCleanupLogData->execute($stackTrace);
        $data['varnish'] = true;
        $data['object_class'] = get_class($object);
        $data['tag_resolving_strategy'] = get_class($tagResolvingStrategy);

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
