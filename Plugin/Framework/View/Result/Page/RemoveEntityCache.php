<?php

declare(strict_types=1);

namespace MageSuite\Cache\Plugin\Framework\View\Result\Page;

class RemoveEntityCache
{
    public function aroundAddPageLayoutHandles(
        \Magento\Framework\View\Result\Page $subject,
        callable $proceed,
        array $parameters = [],
        $defaultHandle = null,
        $entitySpecific = true
    ): bool {
        if ($entitySpecific) {
            return true;
        }

        return $proceed($parameters, $defaultHandle, $entitySpecific);
    }
}
