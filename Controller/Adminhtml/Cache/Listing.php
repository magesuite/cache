<?php

namespace MageSuite\Cache\Controller\Adminhtml\Cache;

class Listing extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Cache clear log'));

        return $resultPage;
    }
}
