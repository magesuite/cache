<?php

declare(strict_types=1);

namespace MageSuite\Cache\Plugin\Framework\View\Result\Page;

class RemoveEntityCache
{
    protected \MageSuite\Cache\Helper\Configuration $configuration;

    public function __construct(\MageSuite\Cache\Helper\Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function aroundAddPageLayoutHandles(
        \Magento\Framework\View\Result\Page $subject,
        callable $proceed,
        array $parameters = [],
        $defaultHandle = null,
        $entitySpecific = true
    ): bool {
        if ($entitySpecific && !$this->configuration->isEntityLayoutHandleEnabled()) {
            return true;
        }

        return $proceed($parameters, $defaultHandle, $entitySpecific);
    }
}
