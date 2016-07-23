<?php

  namespace cheeseCache\tests;

  use cheeseCache\app as cheeseCacheApp;
  use cheeseCache\interfaces as cheeseInterfaces;
  use cheeseCache\exceptions as cheeseExceptions;

  /**
   * @author cheese44
   */
  class CacheComplexTest extends \PHPUnit_Framework_TestCase {

    /** @var  cheeseInterfaces\ICache $cache */
    private $cache;

    /**
     *
     */
    public function setUp() {
      parent::setUp();

      //clears the cache for every test run
      $this->cache = new cheeseCacheApp\Cache();
    }

    /**
     * @expectedException \cheeseCache\exceptions\InvalidCacheParameter
     */
    public function testCache() {
      $this->cache->cache(array(1, cheeseCacheApp\Cache::RESERVED_CACHE_KEY), 'value1');
    }

    /**
     * @expectedException \cheeseCache\exceptions\InvalidCacheParameter
     */
    public function testGetCacheValue() {
      $this->cache->geCacheValue(array(1, cheeseCacheApp\Cache::RESERVED_CACHE_KEY));
    }

    /**
     * @expectedException \cheeseCache\exceptions\InvalidCacheParameter
     */
    public function testIsCacheSet() {
      $this->cache->isCacheSet(array(1, cheeseCacheApp\Cache::RESERVED_CACHE_KEY));
    }

    /**
     * @expectedException \cheeseCache\exceptions\InvalidCacheParameter
     */
    public function testClearCache() {
      $this->cache->clearCache(array(1, cheeseCacheApp\Cache::RESERVED_CACHE_KEY));
    }

  }