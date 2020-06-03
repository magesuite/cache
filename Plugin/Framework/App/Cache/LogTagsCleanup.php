<?php

namespace MageSuite\Cache\Plugin\Framework\App\Cache;

class LogTagsCleanup
{
    const BATCH_SIZE = 1000;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \MageSuite\Cache\Model\StackTraceRepository
     */
    protected $stackTraceRepository;

    /**
     * @var \MageSuite\Cache\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \MageSuite\Cache\Model\StackTraceRepository $stackTraceRepository,
        \MageSuite\Cache\Helper\Configuration $configuration,
        \Magento\Backend\Model\Auth\Session $authSession
    )
    {
        $this->logger = $logger;
        $this->stackTraceRepository = $stackTraceRepository;
        $this->configuration = $configuration;
        $this->authSession = $authSession;
    }

    public function afterClean(\Magento\Framework\App\Cache $subject, $result, $tags = [])
    {
        if(!$this->configuration->isLoggingEnabled()) {
            return $result;
        }

        if(!is_array($tags)) {
            $tags = [$tags];
        }

        $tags = array_unique($tags);

        $stackTrace = $this->getStackTrace();
        $stackTraceIdentifier = md5($stackTrace);
        $this->stackTraceRepository->save($stackTrace, $stackTraceIdentifier);

        $batches = array_chunk($tags, self::BATCH_SIZE);

        $data = [
            'stack_trace_identifier' => $stackTraceIdentifier
        ];

        if (PHP_SAPI === 'cli') {
            $data['cli'] = true;
            $data['command'] = isset($_SERVER['argv']) ? implode(' ', $_SERVER['argv']) : '';
        }

        if($this->authSession->isLoggedIn() && $this->authSession->getUser()) {
            $data['admin_user'] = $this->authSession->getUser()->getUserName();
        }

        foreach ($batches as $tagsBatch) {
            $data['tags'] = $tagsBatch;

            $this->logger->debug('cache_clear', $data);
        }

        return $result;
    }

    protected function getStackTrace()
    {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            return $e->getTraceAsString();
        }
    }
}
