<?php

namespace MageSuite\Cache\Model;

class StackTraceRepository
{
    const FILE_PATH = 'log/stacktrace/%s.txt';

    protected $stackTracesCache = [];
    protected $stackTracesContentCache = [];

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    public function __construct(\Magento\Framework\Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function save($stackTrace, $identifier)
    {
        if (isset($this->stackTracesCache[$identifier]) && $this->stackTracesCache[$identifier]) {
            return;
        }

        $directoryWriter = $this->filesystem->getDirectoryWrite('var');

        $path = sprintf(self::FILE_PATH, $identifier);

        if ($directoryWriter->isExist($path)) {
            $this->stackTracesCache[$identifier] = true;
            return;
        }

        $directoryWriter->writeFile($path, $stackTrace);
        $this->stackTracesCache[$identifier] = true;
    }

    public function get($identifier)
    {
        if (!isset($this->stackTracesContentCache[$identifier])) {
            $this->stackTracesContentCache[$identifier] = file_get_contents(BP.'/var/log/stacktrace/'.$identifier.'.txt');
        }

        return $this->stackTracesContentCache[$identifier];
    }
}
