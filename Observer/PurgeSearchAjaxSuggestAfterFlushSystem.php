<?php

declare(strict_types=1);

namespace MageSuite\Cache\Observer;

class PurgeSearchAjaxSuggestAfterFlushSystem implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        protected \MageSuite\Cache\Service\CacheCleanSearchAjaxSuggest $cacheCleanSearchAjaxSuggest
    ) {}

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        $this->cacheCleanSearchAjaxSuggest->clean();
    }
}
