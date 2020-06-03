<?php

namespace MageSuite\Cache\Model\ResourceModel\CleanupLog;

class Collection extends \Magento\Framework\Data\Collection
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \MageSuite\Cache\Model\StackTraceRepository
     */
    protected $stackTraceRepository;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \MageSuite\Cache\Model\StackTraceRepository $stackTraceRepository
    )
    {
        $this->timezone = $timezone;
        $this->stackTraceRepository = $stackTraceRepository;

        parent::__construct($entityFactory);
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        if (isset($this->_orders['date']) && $this->_orders['date'] == 'DESC') {
            $program = 'tac ';
        } else {
            $program = 'cat ';
        }

        $logPath = BP . '/var/log/cache_cleanup.log';

        $grep = '';

        if (isset($this->_filters['search'])) {
            $grep = '|grep ' . escapeshellcmd($this->_filters['search']['fulltext']);
        }

        $count = '|wc -l';

        $this->_totalRecords = exec($program . $logPath . $grep . $count);

        $this->_isCollectionLoaded = true;

        if (empty($this->_items)) {
            $page = $this->getCurPage();
            $pageSize = $this->getPageSize();
            $from = (($page - 1) * $pageSize) + 1;
            $to = ($page * $pageSize);

            $pagination = '|sed -n "' . $from . ',' . $to . 'p"';

            $command = $program . $logPath . $grep . $pagination;
            exec($command, $logContents);

            $lineParser = new \Dubture\Monolog\Parser\LineLogParser();

            $iterator = 0;

            foreach ($logContents as $line) {
                $dataObject = new \Magento\Framework\DataObject();
                $data = $lineParser->parse($line);

                if (empty($data)) {
                    continue;
                }

                $data['date'] = $this->timezone->date($data['date'])->format('Y-m-d H:i:s');

                $id = md5($command) . '_' . ($from + $iterator);
                $dataObject->setId($id);
                $dataObject->setDate($data['date']);
                $dataObject->setTags(isset($data['context']['tags']) ? implode(' ', $data['context']['tags']) : '');
                $dataObject->setExtra($this->getExtra($data));
                $dataObject->setStackTrace($this->getStackTrace($data, $id));

                $this->_items[] = $dataObject;

                $iterator++;
            }

            return $this;
        }

        return $this;
    }

    protected function getExtra($data)
    {
        $output = '';

        if (isset($data['extra']['url'])) {
            $output .= 'URL: ' . $data['extra']['url'] . '<br>';
        }

        if (isset($data['context']['cli']) && $data['context']['cli']) {
            $output .= 'CLI: ' . $data['context']['command'] . '<br>';
        }

        if (isset($data['context']['admin_user'])) {
            $output .= 'Admin user: ' . $data['context']['admin_user'] . '<br>';
        }

        return $output;
    }

    public function addOrder($field, $order)
    {
        $this->_orders[$field] = $order;

        return $this;
    }

    public function addFieldToFilter($field, $condition)
    {
        $this->_filters[$field] = $condition;
    }

    protected function getStackTrace($data, $id)
    {
        $stackTraceIdentifier = $data['context']['stack_trace_identifier'];
        $htmlElementId = $stackTraceIdentifier.'_'.$id;
        $stackTrace = nl2br($this->stackTraceRepository->get($stackTraceIdentifier));

        return <<<HTML
                <a id="open-stacktrace-{$htmlElementId}">Show stacktrace</a>
                <div id="stacktrace-modal-{$htmlElementId}" style="display:none;">
                    {$stackTrace}
                </div>
                <script type="text/javascript">
                require(
                    [
                        'jquery',
                        'Magento_Ui/js/modal/modal'
                    ],
                    function (
                        $,
                        modal
                    ) {
                        var options = {
                            type: 'popup',
                            title: 'Stack trace',
                            buttons: [{
                                text: $.mage.__('Close'),
                                class: '',
                                click: function () {
                                    this.closeModal();
                                }
                            }]
                        };

                        modal(options, $('#stacktrace-modal-{$htmlElementId}'));

                        console.log('test');

                        $("#open-stacktrace-{$htmlElementId}").on('click', function () {
                            $("#stacktrace-modal-{$htmlElementId}").modal("openModal");
                        });

                    }
                );
                </script>
HTML;
    }
}
