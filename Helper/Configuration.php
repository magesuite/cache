<?php

namespace MageSuite\Cache\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CACHE_CLEANUP_DEBUGGER_CONFIGURATION = 'system/cache_cleanup_debugger';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $config = null;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {
        parent::__construct($context);

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
        if($this->config === null){
            $this->config = new \Magento\Framework\DataObject(
                $this->scopeConfig->getValue(self::XML_PATH_CACHE_CLEANUP_DEBUGGER_CONFIGURATION)
            );
        }

        return $this->config;
    }
}
