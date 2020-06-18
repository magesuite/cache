<?php

namespace MageSuite\Cache\Controller\Adminhtml\Cache;

class Clear extends \Magento\Backend\App\Action
{
    /**
     * @var \MageSuite\Cache\Service\CacheCleaner
     */
    protected $cacheCleaner;

    /**
     * @var \MageSuite\Cache\Helper\Url
     */
    protected $url;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageSuite\Cache\Service\CacheCleaner $cacheCleaner,
        \MageSuite\Cache\Helper\Url $url
    ) {
        parent::__construct($context);

        $this->cacheCleaner = $cacheCleaner;
        $this->url = $url;
    }

    public function execute()
    {
        $request = $this->getRequest();

        $params = $request->getParams();
        $tags = $this->getCacheTags($params);

        try {
            $this->cacheCleaner->cleanByTags($tags);

            $this->messageManager->addSuccess(__('Cache for specified tag/URL was successfully flushed'));
        } catch (\Exception $exception) {
            $this->messageManager->addError(__('There was an error when clearing cache: %1', $exception->getMessage()));
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    protected function getCacheTags(array $params)
    {
        $tags = [];

        if (isset($params['tag'])) {
            $tags += explode(',', $params['tag']);
        }

        if (isset($params['url'])) {
            $url = $params['url'];
            $url = $this->url->normalize($url);

            $tags[] = md5($url);
        }

        return $tags;
    }
}
