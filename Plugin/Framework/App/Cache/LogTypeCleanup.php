<?php

namespace MageSuite\Cache\Plugin\Framework\App\Cache;

class LogTypeCleanup
{
    /**
     * @var \MageSuite\Cache\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \MageSuite\Cache\Model\Command\GenerateBasicCleanupLogData\Proxy
     */
    protected $generateBasicCleanupLogData;

    /**
     * @var \MageSuite\Cache\Model\CleanupLogRepository
     */
    protected $cleanupLogRepository;

    public function __construct(
        \MageSuite\Cache\Model\CleanupLogRepository $cleanupLogRepository,
        \MageSuite\Cache\Helper\Configuration $configuration,
        \MageSuite\Cache\Model\Command\GenerateBasicCleanupLogData\Proxy $generateBasicCleanupLogData
    ) {
        $this->configuration = $configuration;
        $this->generateBasicCleanupLogData = $generateBasicCleanupLogData;
        $this->cleanupLogRepository = $cleanupLogRepository;
    }

    public function afterCleanType(\Magento\Framework\App\Cache\TypeListInterface $subject, $result, $typeCode)
    {
        if (!$this->configuration->isLoggingEnabled()) {
            return $result;
        }

        $stackTrace = $this->getStackTrace();

        $data = $this->generateBasicCleanupLogData->execute($stackTrace);
        $data['cache_type'] = $typeCode;
        $data['redis'] = true;

        $this->cleanupLogRepository->save($data);

        return $result;
    }

    protected function getStackTrace()
    {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            return $e->getTrace();
        }
    }
}
