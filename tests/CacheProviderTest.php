<?php

  namespace cheeseCache\tests;

  use cheeseCache\app as cheeseCacheApp;
  use cheeseCache\interfaces as cheeseInterfaces;
  use cheeseCache\exceptions as cheeseExceptions;

  /**
   * @author cheese44
   */
  class CacheProviderTest extends \PHPUnit_Framework_TestCase {

    public function testProvider() {
      $cache = cheeseCacheApp\CacheProvider::getCache();
      
      $cache->cache(array(1, 2), 'value1');
      
      $this->assertTrue($cache->isCacheSet(array(1, 2)));
      
      $cacheAfter = cheeseCacheApp\CacheProvider::getCache();
      
      $this->assertTrue($cacheAfter->isCacheSet(array(1, 2)));
      $this->assertFalse($cacheAfter->isCacheSet(array(1, 2, 3)));

      $cache->cache(array(1, 2, 3), 'value2');

      $this->assertTrue($cacheAfter->isCacheSet(array(1, 2, 3)));
      
      cheeseCacheApp\CacheProvider::destroyCache();
      
      $destroyedCache = cheeseCacheApp\CacheProvider::getCache();

      $this->assertFalse($destroyedCache->isCacheSet(array(1, 2)));
      $this->assertFalse($destroyedCache->isCacheSet(array(1, 2, 3)));
    }

  }