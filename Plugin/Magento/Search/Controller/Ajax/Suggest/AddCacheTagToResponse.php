<?php

declare(strict_types=1);

namespace MageSuite\Cache\Plugin\Magento\Search\Controller\Ajax\Suggest;

class AddCacheTagToResponse
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(
        \Magento\Search\Controller\Ajax\Suggest $subject,
        \Magento\Framework\Controller\ResultInterface $result
    ): \Magento\Framework\Controller\ResultInterface {

        $result->setHeader('X-Magento-Tags', \MageSuite\Cache\Service\CacheCleanSearchAjaxSuggest::CACHE_TAG);

        return $result;
    }
}
