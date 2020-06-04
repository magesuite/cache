<?php

namespace MageSuite\Cache\Logger;

class CleanupLogHandler extends \Magento\Framework\Logger\Handler\Base
{
    protected $loggerType = \Monolog\Logger::DEBUG;

    protected $fileName = '/var/log/cache_cleanup.log';
}
