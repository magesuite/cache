<?php

namespace MageSuite\Cache\Console\Command;

class ClearCacheByTag extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \MageSuite\Cache\Service\CacheCleaner
     */
    protected $cacheCleaner;

    public function __construct(\MageSuite\Cache\Service\CacheCleaner $cacheCleaner)
    {
        parent::__construct();

        $this->cacheCleaner = $cacheCleaner;
    }

    protected function configure()
    {
        $this
            ->setName('cache:clean:tag')
            ->setDescription('Cleans cache by specified tag');

        $this->addArgument(
            'tag',
            \Symfony\Component\Console\Input\InputArgument::REQUIRED,
            'Tag'
        );
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $tag = $input->getArgument('tag');

        $this->cacheCleaner->cleanByTags([$tag]);

        $output->writeln(sprintf('Cache containing tag "%s" was cleared.', $tag));

        return 1;
    }
}
