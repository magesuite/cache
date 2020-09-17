<?php

namespace MageSuite\Cache\Model\ResourceModel;

class CleanupLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $tablesExist = null;

    /**
     * @var CleanupLogStacktrace
     */
    protected $stacktraceResourceModel;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        CleanupLogStacktrace $stacktraceResourceModel,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);

        $this->stacktraceResourceModel = $stacktraceResourceModel;
    }

    protected function _construct()
    {
        $this->_init('cache_cleanup_log', 'id');
    }

    public function canSaveLog()
    {
        if ($this->tablesExist === null) {
            $this->tablesExist = $this->tableExist('cache_cleanup_log') &&
                $this->tableExist('cache_cleanup_log_stacktrace');
        }

        return $this->tablesExist;
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $context = $object->getContext();
        $stackTrace = $context['stack_trace'];

        $stackTraceId = $this->stacktraceResourceModel->getStackTraceId($stackTrace);

        unset($context['stack_trace']);

        $object->setContext(json_encode($context));
        $object->setStacktraceId($stackTraceId);

        return parent::_beforeSave($object);
    }

    protected function tableExist($tableName)
    {
        return $this->getConnection()->isTableExists($this->getConnection()->getTableName($tableName));
    }
}
