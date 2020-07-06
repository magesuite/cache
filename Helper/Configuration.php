<?php

namespace MageSuite\Cache\Helper;

class Configuration
{
    const XML_PATH_CACHE_CLEANUP_DEBUGGER_CONFIGURATION = 'system/cache_cleanup_debugger';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $config = null;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {
        $this->scopeConfig = $scopeConfigInterface;
    }

    public function isLoggingEnabled()
    {
        return $this->getConfig()->getIsLoggingEnabled();
    }

    public function isBlockTagsPreviewEnabled()
    {
        return $this->getConfig()->getIsBlockTagsPreviewEnabled();
    }

    protected function getConfig()
    {
        if ($this->config === null) {
            $config = $this->scopeConfig->getValue(self::XML_PATH_CACHE_CLEANUP_DEBUGGER_CONFIGURATION);

            if(!is_array($config) || $config === null) {
                $config = [];
            }

            $this->config = new \Magento\Framework\DataObject($config);
        }

        return $this->config;
    }
}
