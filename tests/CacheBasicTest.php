<?php

  namespace cheeseCache\tests;

  use cheeseCache\app as cheeseCacheApp;
  use cheeseCache\interfaces as cheeseInterfaces;
  use cheeseCache\exceptions as cheeseExceptions;

  /**
   * @author cheese44
   */
  class CacheBasicTest extends \PHPUnit_Framework_TestCase {

    /** @var  cheeseInterfaces\ICache $cache */
    private $cache;

    /**
     * @param array    $params
     * @param \Closure $callable
     * @param mixed    $expectedValue
     * @param array    $expectedCacheStructure
     * @param bool     $renew
     *
     * @return array
     */
    public function cacheMultipleTimes($params, $callable, $expectedValue, $expectedCacheStructure, $renew) {
      global $cacheCounter;

      $cacheCounter = 0;

      for($ct = 0; $ct < 5; ++$ct):
        $value = $this->cache->cache($params, $callable, $renew);

        $cacheStructure = $this->getCacheStructure();

        global $cacheCounter;

        $this->assertEquals($expectedValue, $value);
        $this->assertEquals(($renew ? $ct+1 : 1), $cacheCounter);
        $this->assertEquals($expectedCacheStructure, $cacheStructure);
      endfor;
    }

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

    /**
     * @return array
     */
    public function provideCacheByCallable() {
      return array(
        array(
          array('test', 1, 2, 'hello'),
          function () {
            global $cacheCounter;

            ++$cacheCounter;

            return 'first cached value';
          },
          true,
          array(
            'test' => array(
              1 => array(
                2 => array(
                  'hello' => array(
                    cheeseCacheApp\Cache::RESERVED_CACHE_KEY => array(
                      cheeseCacheApp\Cache::LEAF_VALUE  => 'first cached value',
                      cheeseCacheApp\Cache::LEAF_CALLER => 'cheeseCache\tests\CacheBasicTest->cacheMultipleTimes'
                    )
                  )
                )
              )
            )
          ),
          'first cached value'
        ),
        array(
          array('test', 'test', 2, 2),
          function () {
            global $cacheCounter;

            ++$cacheCounter;

            return 'second cached value';
          },
          false,
          array(
            'test' => array(
              'test' => array(
                2 => array(
                  2 => array(
                    cheeseCacheApp\Cache::RESERVED_CACHE_KEY => array(
                      cheeseCacheApp\Cache::LEAF_VALUE => 'second cached value'
                    )
                  )
                )
              )
            )
          ),
          'second cached value'
        )
      );
    }

    /**
     * @return array
     */
    public function provideCacheByValue() {
      return array(
        array(
          array('test', 1, 2, 'hello'),
          'first cached value',
          true,
          array(
            'test' => array(
              1 => array(
                2 => array(
                  'hello' => array(
                    cheeseCacheApp\Cache::RESERVED_CACHE_KEY => array(
                      cheeseCacheApp\Cache::LEAF_VALUE  => 'first cached value',
                      cheeseCacheApp\Cache::LEAF_CALLER => 'cheeseCache\tests\CacheBasicTest->testCacheByValue'
                    )
                  )
                )
              )
            )
          )
        ),
        array(
          array('test', 'test', 2, 2),
          'second cached value',
          false,
          array(
            'test' => array(
              'test' => array(
                2 => array(
                  2 => array(
                    cheeseCacheApp\Cache::RESERVED_CACHE_KEY => array(
                      cheeseCacheApp\Cache::LEAF_VALUE => 'second cached value'
                    )
                  )
                )
              )
            )
          )
        )
      );
    }

    /**
     *
     */
    public function setUp() {
      parent::setUp();

      //clears the cache for every test run
      $this->cache = new cheeseCacheApp\Cache();
    }

    /**
     * @param array    $params
     * @param \Closure $callable
     * @param bool     $debug
     * @param array    $expectedCacheStructure
     * @param mixed    $expectedValue
     *
     * @dataProvider provideCacheByCallable
     */
    public function testCacheByCallable($params, $callable, $debug, $expectedCacheStructure, $expectedValue) {
      $this->cache->setDebugging($debug);

      $this->cacheMultipleTimes($params, $callable, $expectedValue, $expectedCacheStructure, false);
      $this->cache->clearCache();
      
      $this->cacheMultipleTimes($params, $callable, $expectedValue, $expectedCacheStructure, true);
      $this->cache->clearCache();
      
      $this->cacheMultipleTimes($params, $callable, $expectedValue, $expectedCacheStructure, false);
      $this->cache->clearCache();
    }

    /**
     * @param array $params
     * @param mixed $value
     * @param bool  $debug
     * @param array $expectedCacheStructure
     *
     * @dataProvider provideCacheByValue
     */
    public function testCacheByValue($params, $value, $debug, $expectedCacheStructure) {
      $this->cache->setDebugging($debug);

      $this->cache->cache($params, $value);

      $cacheStructure = $this->getCacheStructure();

      $this->assertEquals($expectedCacheStructure, $cacheStructure);
    }

  }