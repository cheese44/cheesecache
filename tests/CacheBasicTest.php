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
        $this->assertEquals(($renew ? $ct + 1 : 1), $cacheCounter);
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

    public function provideCacheByValues() {
      return array(
        array(
          array(1, 1, 1),
          'value1',
          array(2, 1, 1),
          'value2',
          array(3, 1, 1),
          'value3',
          array(
            1 => array(1 => array(1 => array(cheeseCacheApp\Cache::RESERVED_CACHE_KEY => array(cheeseCacheApp\Cache::LEAF_VALUE => 'value1')))),
            2 => array(1 => array(1 => array(cheeseCacheApp\Cache::RESERVED_CACHE_KEY => array(cheeseCacheApp\Cache::LEAF_VALUE => 'value2')))),
            3 => array(1 => array(1 => array(cheeseCacheApp\Cache::RESERVED_CACHE_KEY => array(cheeseCacheApp\Cache::LEAF_VALUE => 'value3'))))
          )
        ),
        array(
          array(1, 1, 1),
          'value1',
          array(1, 1),
          'value2',
          array(1, 1, 2),
          'value3',
          array(
            1 => array(
              1 => array(
                1 => array(cheeseCacheApp\Cache::RESERVED_CACHE_KEY => array(cheeseCacheApp\Cache::LEAF_VALUE => 'value1')),
                cheeseCacheApp\Cache::RESERVED_CACHE_KEY => array(cheeseCacheApp\Cache::LEAF_VALUE => 'value2'),
                2 => array(cheeseCacheApp\Cache::RESERVED_CACHE_KEY => array(cheeseCacheApp\Cache::LEAF_VALUE => 'value3'))
              )
            )
          )
        ),
        array(
          array(1, 1, 1),
          'value1',
          array(1, 1),
          'value2',
          array(1, 1, 2),
          'value3',
          array(
            1 => array(
              1 => array(
                1 => array(cheeseCacheApp\Cache::RESERVED_CACHE_KEY => array(cheeseCacheApp\Cache::LEAF_VALUE => 'value1')),
                cheeseCacheApp\Cache::RESERVED_CACHE_KEY => array(cheeseCacheApp\Cache::LEAF_VALUE => 'value2'),
                2 => array(cheeseCacheApp\Cache::RESERVED_CACHE_KEY => array(cheeseCacheApp\Cache::LEAF_VALUE => 'value3'))
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

    /**
     * @param array $params1
     * @param mixed $value1
     * @param array $params2
     * @param mixed $value2
     * @param array $params3
     * @param mixed $value3
     * @param array $expectedCacheStructure
     *
     * @dataProvider provideCacheByValues
     */
    public function testCacheByValues(
      $params1,
      $value1,
      $params2,
      $value2,
      $params3,
      $value3,
      $expectedCacheStructure
    ) {
      $this->cache->cache($params1, $value1);
      $this->cache->cache($params2, $value2);
      $this->cache->cache($params3, $value3);

      $cacheStructure = $this->getCacheStructure();

      $this->assertEquals($expectedCacheStructure, $cacheStructure);

      $returnedValue1 = $this->cache->geCacheValue($params1);
      $returnedValue2 = $this->cache->geCacheValue($params2);
      $returnedValue3 = $this->cache->geCacheValue($params3);

      $this->assertEquals($value1, $returnedValue1);
      $this->assertEquals($value2, $returnedValue2);
      $this->assertEquals($value3, $returnedValue3);
    }

  }