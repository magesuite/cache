<?php

namespace MageSuite\Cache\Model\Command;

class GenerateBasicCleanupLogData
{
    /**
     * @var \Magento\Backend\Model\Auth\Session\Proxy
     */
    protected $authSession;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    public function __construct(
        \Magento\Backend\Model\Auth\Session\Proxy $authSession,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\State $state
    ) {
        $this->authSession = $authSession;
        $this->url = $url;
        $this->state = $state;
    }

    public function execute($stackTrace)
    {

        $stackTrace = $this->getStackTraceAsString($stackTrace);

        $data = [
            'stack_trace' => $stackTrace
        ];

        if (PHP_SAPI === 'cli') {
            $data['cli'] = true;
            $data['command'] = isset($_SERVER['argv']) ? implode(' ', $_SERVER['argv']) : '';
        } else {
            $data['url'] = $this->url->getCurrentUrl();
        }

        $data['admin_user'] = $this->state->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_GLOBAL,
            [$this, 'getAdminNameFromSession']
        );

        return $data;
    }

    public function getAdminNameFromSession(){
        if ($this->authSession->isLoggedIn() && $this->authSession->getUser()) {
            return $this->authSession->getUser()->getUserName();
        }

        return false;
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
