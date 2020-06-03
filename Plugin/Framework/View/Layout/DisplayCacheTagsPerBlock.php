<?php

namespace MageSuite\Cache\Plugin\Framework\View\Layout;

class DisplayCacheTagsPerBlock
{
    const REQUEST_PARAM_NAME = 'show_block_identities';

    /**
     * @var \MageSuite\Cache\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    public function __construct(
        \MageSuite\Cache\Helper\Configuration $configuration,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->configuration = $configuration;
        $this->request = $request;
    }

    public function afterGetOutput(\Magento\Framework\View\Layout $subject, $result)
    {
        if(!$this->configuration->isBlockTagsPreviewEnabled()) {
            return $result;
        }

        if($this->request->getParam(self::REQUEST_PARAM_NAME, false) == false)  {
            return $result;
        }

        echo '<!-- CACHE_DEBUGGER_START '.PHP_EOL;

        foreach ($subject->getAllBlocks() as $block) {
            if ($block instanceof \Magento\Framework\DataObject\IdentityInterface) {
                $identities = array_unique($block->getIdentities());

                if(!empty($identities)) {
                    echo $block->getNameInLayout() . ': ' . implode(', ', $identities).PHP_EOL;
                    echo '--------------------------------------------'.PHP_EOL;
                }
            }
        }

        echo 'CACHE_DEBUGGER_END -->'.PHP_EOL;

        return $result;
    }
}
