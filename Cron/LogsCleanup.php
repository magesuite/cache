<?php

namespace MageSuite\Cache\Cron;

class LogsCleanup
{
    /**
     * @var \MageSuite\Cache\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \MageSuite\Cache\Model\Command\CleanLogs
     */
    protected $cleanLogs;

    public function __construct(
        \MageSuite\Cache\Helper\Configuration $configuration,
        \MageSuite\Cache\Model\Command\CleanLogs $cleanLogs
    )
    {
        $this->configuration = $configuration;
        $this->cleanLogs = $cleanLogs;
    }

    public function execute()
    {
        if (!$this->configuration->isLoggingEnabled()) {
            return;
        }

        $this->cleanLogs->execute($this->configuration->getLoggingRetentionPeriod());
    }
}
