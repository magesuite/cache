<?php

namespace MageSuite\Cache\Model\Command;

class GenerateBasicCleanupLogData
{
    /**
     * @var \MageSuite\Cache\Model\StackTraceRepository
     */
    protected $stackTraceRepository;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    public function __construct(
        \MageSuite\Cache\Model\StackTraceRepository $stackTraceRepository,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->stackTraceRepository = $stackTraceRepository;
        $this->authSession = $authSession;
    }

    public function execute($stackTrace)
    {
        $stackTrace = $this->getStackTraceAsString($stackTrace);

        $stackTraceIdentifier = md5($stackTrace);
        $this->stackTraceRepository->save($stackTrace, $stackTraceIdentifier);

        $data = [
            'stack_trace_identifier' => $stackTraceIdentifier
        ];

        if (PHP_SAPI === 'cli') {
            $data['cli'] = true;
            $data['command'] = isset($_SERVER['argv']) ? implode(' ', $_SERVER['argv']) : '';
        }

        if ($this->authSession->isLoggedIn() && $this->authSession->getUser()) {
            $data['admin_user'] = $this->authSession->getUser()->getUserName();
        }

        return $data;
    }

    /**
     * getTraceAsString is not used because it returns arguments passed to methods. Arguments are dynamic and would
     * cause multiple stacktrace duplicates that differs only by method arguments.
     * @param $stackTrace
     * @return string
     */
    protected function getStackTraceAsString($stackTrace)
    {
        $output = '';

        $iterator = 1;

        foreach ($stackTrace as $traceItem) {
            $method = '';

            if (isset($traceItem['file'])) {
                $method .= $traceItem['file'];
            }

            if (isset($traceItem['line'])) {
                $method .= '(' . $traceItem['line'] . ')';
            }

            $method .= ': ';

            if (isset($traceItem['class'])) {
                $method .= $traceItem['class'];
            }

            if (isset($traceItem['type'])) {
                $method .= $traceItem['type'];
            }

            if (isset($traceItem['function'])) {
                $method .= $traceItem['function'].'()';
            }

            $output .= '#' . $iterator . ': ' . $method . PHP_EOL;

            $iterator++;
        }

        return $output;
    }
}
