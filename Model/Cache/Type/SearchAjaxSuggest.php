<?php

declare(strict_types=1);

namespace MageSuite\Cache\Model\Cache\Type;

class SearchAjaxSuggest extends \Magento\Framework\Cache\Frontend\Decorator\TagScope
{
    public const TYPE_IDENTIFIER = 'search_ajax_suggest';
    public const CACHE_TAG = 'search_ajax_suggest';

    public function __construct(
        \Magento\Framework\App\Cache\Type\FrontendPool $cacheFrontendPool,
        protected \Magento\Framework\Event\Manager $eventManager,
        protected \Magento\Framework\Indexer\CacheContext $cacheContext,
        protected \MageSuite\Cache\Service\CacheCleanSearchAjaxSuggest $cacheCleanSearchAjaxSuggest
    ) {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }

    public function clean($mode = \Zend_Cache::CLEANING_MODE_ALL, array $tags = []): bool
    {
        $this->cacheCleanSearchAjaxSuggest->clean();

        return true;
    }
}
