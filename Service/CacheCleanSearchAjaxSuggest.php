<?php

namespace MageSuite\Cache\Service;

class CacheCleanSearchAjaxSuggest
{
    public const CACHE_TAG = 'search_ajax_suggest';

    public function __construct(
        protected \Magento\CacheInvalidate\Model\PurgeCache $purgeCache,
        protected \Psr\Log\LoggerInterface $logger
    ) {}

    public function clean(): void
    {
        try {
            $this->purgeCache->sendPurgeRequest(self::CACHE_TAG);
        } catch (\Exception $exception) {
            $this->logger->error(sprintf('Error during Varnish %s tag cache purge: %s', self::CACHE_TAG, $exception->getMessage()));
        }
    }
}
