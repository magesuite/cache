<?php

namespace MageSuite\Cache\Model;

class CleanupLog extends \Magento\Framework\Model\AbstractModel implements \MageSuite\Cache\Api\Data\CleanupLogInterface
{
    protected function _construct()
    {
        $this->_init('MageSuite\Cache\Model\ResourceModel\CleanupLog');
    }
}
