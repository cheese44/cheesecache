<?php

  namespace cheeseCache\tests;

  use cheeseCache\app as cheeseCacheApp;
  use cheeseCache\interfaces as cheeseInterfaces;
  use cheeseCache\exceptions as cheeseExceptions;

  /**
   * @author cheese44
   */
  class TestCacheBasic extends \PHPUnit_Framework_TestCase {

    /** @var  cheeseInterfaces\ICache $cache */
    private $cache;

    /**
     * @return array
     */
    public function getCacheStructure() {
      $reflectionCache = new \ReflectionClass($this->cache);

      $reflectionProperty = $reflectionCache->getProperty('cache');

      $reflectionProperty->setAccessible(true);

      $cacheStructure = $reflectionProperty->getValue($this->cache);

      return $cacheStructure;
    }

    public function setUp() {
      parent::setUp();

      //clears the cache for every test run
      $this->cache = new cheeseCacheApp\Cache();
    }
    
    public function testCacheByValue($params, $value, $expectedCacheStructure) {
      $this->cache->cache($params, $value);

      $cacheStructure = $this->getCacheStructure();
      
      $this->assertEquals($expectedCacheStructure, $cacheStructure);
    }
    
  }