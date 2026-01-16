<?php

namespace MageSuite\Cache\Console\Command;

class ClearCacheSearchAjaxSuggest extends \Symfony\Component\Console\Command\Command
{
    public function __construct(
        protected \MageSuite\Cache\Service\CacheCleanSearchAjaxSuggest $clearSearchAjaxSuggestCache
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('cache:clean:search:ajax:suggest')
            ->setDescription(sprintf('Cleans cache by %s tag', \MageSuite\Cache\Service\CacheCleanSearchAjaxSuggest::CACHE_TAG));
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ): int {
        $this->clearSearchAjaxSuggestCache->clean();

        $output->writeln(sprintf('Cache for "%s" URL was cleared.', \MageSuite\Cache\Service\CacheCleanSearchAjaxSuggest::CACHE_TAG));

        return 1;
    }
}
