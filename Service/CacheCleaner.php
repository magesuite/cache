<?php

namespace MageSuite\Cache\Service;

class CacheCleaner
{
    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * @var \Magento\Framework\Indexer\CacheContext
     */
    protected $cacheContext;
    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $eventManager;

    public function __construct(
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\Indexer\CacheContext $cacheContext
    )
    {
        $this->cache = $cache;
        $this->cacheContext = $cacheContext;
        $this->eventManager = $eventManager;
    }

    public function cleanByTags(array $tags) {
        $this->cache->clean($tags);
        $this->cacheContext->registerTags($tags);
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
    }
}