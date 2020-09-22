<?php

namespace MageSuite\Cache\Model\ResourceModel\CleanupLog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->timezone = $timezone;
    }

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\MageSuite\Cache\Model\CleanupLog::class, \MageSuite\Cache\Model\ResourceModel\CleanupLog::class);
    }

    public function _beforeLoad()
    {
        $this->join(
            ['stacktrace' => 'cache_cleanup_log_stacktrace'],
            'main_table.stacktrace_id=stacktrace.id',
            ['stacktrace', 'hash']
        );

        return parent::_beforeLoad();
    }

    public function _afterLoad()
    {
        foreach ($this->getItems() as $item) {
            $this->buildItem($item);
        }

        return parent::_afterLoad();
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'fulltext') {
            return parent::addFieldToFilter(
                'context',
                ['like' => '%' . $condition['fulltext'] . '%']
            );
        }

        return parent::addFieldToFilter($field, $condition);
    }

    protected function getExtra($context)
    {
        $output = '';

        if (isset($context['url'])) {
            $output .= 'URL: ' . $context['url'] . '<br>';
        }

        if (isset($context['cli']) && $context['cli']) {
            $output .= 'CLI: ' . $context['command'] . '<br>';
        }

        if (isset($context['admin_user'])) {
            $output .= 'Admin user: ' . $context['admin_user'] . '<br>';
        }

        if (isset($context['object_class'])) {
            $output .= 'Object class: ' . $context['object_class'] . '<br>';
        }

        if (isset($context['tag_resolving_strategy'])) {
            $output .= 'Resolving strategy: ' . $context['tag_resolving_strategy'] . '<br>';
        }

        return $output;
    }

    protected function getStackTrace($id, $stackTrace, $hash)
    {
        $htmlElementId = $hash . '_' . $id;

        $stackTrace = nl2br($stackTrace);

        return <<<HTML
                <a onclick="javascript: openStacktrace('$htmlElementId')">Show stacktrace</a>
                <div id="stacktrace-modal-{$htmlElementId}" style="display:none;">
                    {$stackTrace}
                </div>

HTML;
    }

    protected function getType($context)
    {
        if (isset($context['tag_registered_for_cleanup']) && $context['tag_registered_for_cleanup']) {
            return '<span class="grid-severity-minor"><span>TAG REGISTRATION</span></span>';
        }

        if (isset($context['varnish']) && $context['varnish']) {
            return '<span class="grid-severity-external"><span>VARNISH</span></span>';
        }

        return '<span class="grid-severity-critical"><span>REDIS</span></span>';
    }

    /**
     * @param array $data
     * @return string
     */
    public function getEntities(array $context): string
    {
        if (isset($context['tags'])) {
            return sprintf('Tags: %s', implode(' ', $context['tags']));
        }

        if (isset($context['cache_type'])) {
            return sprintf('Cache type: %s', $context['cache_type']);
        }

        if (isset($context['flush_magento'])) {
            return sprintf('Flush Magento');
        }

        if (isset($context['flush_storage'])) {
            return sprintf('Flush Storage');
        }

        return '';
    }

    protected function buildItem(\Magento\Framework\DataObject $item)
    {
        $context = json_decode($item->getContext(), true);

        $stackTrace = $this->getStackTrace(
            $item->getData('id'),
            $item->getData('stacktrace'),
            $item->getData('hash')
        );

        $item->setExecutedAt($this->timezone->date($item->getExecutedAt())->format('Y-m-d H:i:s'));
        $item->setType($this->getType($context));
        $item->setEntities($this->getEntities($context));
        $item->setExtra($this->getExtra($context));
        $item->setStackTrace($stackTrace);
    }
}
