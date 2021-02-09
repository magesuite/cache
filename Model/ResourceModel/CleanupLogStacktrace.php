<?php

namespace MageSuite\Cache\Model\ResourceModel;

class CleanupLogStacktrace extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $existingStackTracesIdsCache = [];

    protected function _construct()
    {
        $this->_init('cache_cleanup_log_stacktrace', 'id');
    }

    public function getStackTraceId($stackTrace)
    {
        $hash = hash('md5', $stackTrace);

        if (isset($this->existingStackTracesIdsCache[$hash]) && $this->existingStackTracesIdsCache[$hash] > 0) {
            return $this->existingStackTracesIdsCache[$hash];
        }

        $stacktraceId = $this->getExistingStacktraceId($hash);

        if ($stacktraceId == null) {
            $this->getConnection()->insert(
                $this->getMainTable(),
                [
                    'stacktrace' => $stackTrace,
                    'hash' => $hash
                ]
            );

            $stacktraceId = $this->getConnection()->lastInsertId();
        }

        $this->existingStackTracesIdsCache[$hash] = $stacktraceId;

        return $this->existingStackTracesIdsCache[$hash];
    }

    protected function getExistingStacktraceId(string $hash)
    {
        $select = $this->getConnection()->select();
        $select->from($this->getMainTable(), 'id');
        $select->where('hash = ?', $hash);

        $id = $this->getConnection()->fetchOne($select);

        return $id ?: null;
    }
}
