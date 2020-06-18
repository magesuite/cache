<?php

namespace MageSuite\Cache\Block;

class CacheUrl extends \Magento\Framework\View\Element\AbstractBlock implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var \MageSuite\Cache\Helper\Url
     */
    protected $urlHelper;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \MageSuite\Cache\Helper\Url $urlHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->urlHelper = $urlHelper;
    }

    public function getIdentities()
    {
        $server = $this->_request->getServer();

        $scheme = $server->get('REQUEST_SCHEME');
        $host = $server->get('HTTP_HOST');
        $requestUri = $server->get('ORIGINAL_URI') ?? $this->_request->getRequestUri();

        $url = $scheme.'://'.$host.$requestUri;
        $url = $this->urlHelper->normalize($url);

        return [md5($url)];
    }
}
