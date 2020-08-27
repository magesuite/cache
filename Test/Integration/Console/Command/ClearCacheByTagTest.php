<?php

namespace MageSuite\Cache\Test\Integration\Console\Command;

class ClearCacheByTagTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Symfony\Component\Console\Tester\CommandTester($this->command)
     */
    protected $tester;

    /**
     * @var \Magento\Catalog\Console\Command\ProductAttributesCleanUp
     */
    protected $command;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->command = $this->objectManager->create(\MageSuite\Cache\Console\Command\ClearCacheByTag::class);
        $this->tester = new \Symfony\Component\Console\Tester\CommandTester($this->command);
        $this->cache = $this->objectManager->create(\Magento\Framework\App\CacheInterface::class);
    }

    public function testItClearsAllCacheEntriesWithSpecifiedTag()
    {
        $this->cache->save('data_that_should_stay', 'stay_in_cache', ['stay_in_cache_tag']);

        $this->cache->save('data_that_should_be_removed_1', 'remove_from_cache_1', ['remove_from_cache_tag']);
        $this->cache->save('data_that_should_be_removed_2', 'remove_from_cache_2', ['remove_from_cache_tag']);

        $this->assertEquals('data_that_should_stay', $this->cache->load('stay_in_cache'));
        $this->assertEquals('data_that_should_be_removed_1', $this->cache->load('remove_from_cache_1'));
        $this->assertEquals('data_that_should_be_removed_2', $this->cache->load('remove_from_cache_2'));

        $this->tester->execute(['tag' => 'remove_from_cache_tag']);

        $this->assertEquals('data_that_should_stay', $this->cache->load('stay_in_cache'));
        $this->assertEquals(null, $this->cache->load('remove_from_cache_1'));
        $this->assertEquals(null, $this->cache->load('remove_from_cache_2'));

        $this->assertEquals('Cache containing tag "remove_from_cache_tag" was cleared.'.PHP_EOL, $this->tester->getDisplay());
    }
}
