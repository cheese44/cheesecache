<?php

  namespace cheeseCache\app;

  use cheeseCache\interfaces as cheeseInterfaces;
  use cheeseCache\exceptions as cheeseExceptions;

  /**
   * @author cheese44
   */
  class Cache implements cheeseInterfaces\ICache {

    const LEAF_CALLER = 'caller';
    const LEAF_VALUE = 'value';
    /**
     * this key is a reserved value for $cacheParams
     * and is used for branching the cache paths from the actual cache values
     */
    const RESERVED_CACHE_KEY = 'reserved_cache_key';

    private $cache = array();
    private $debug = false;

    /**
     * @param array          $cacheParams
     * @param callable|mixed $cacheable  //if callable, method must return value to be cached.
     * @param bool           $renewCache //cached value will be overwritten if true
     *
     * @return mixed
     * @throws cheeseExceptions\InvalidCacheParameter
     */
    public function cache($cacheParams, $cacheable, $renewCache = false) {
      $cacheParams = (array)$cacheParams;
      $renewCache = (bool)$renewCache;

      $this->validateCacheParameters($cacheParams);

      if(!$renewCache && $this->isCacheSet($cacheParams)):
        $value = $this->geCacheValue($cacheParams);
      else:
        if(is_callable($cacheable)):
          $value = $cacheable();
        else:
          $value = $cacheable;
        endif;

        $this->setCacheValue($cacheParams, $value);
      endif;

      return $value;
    }

    /**
     * @param array $cacheParams
     * 
     * @throws cheeseExceptions\InvalidCacheParameter
     */
    public function clearCache($cacheParams = array()) {
      $cacheParams = (array)$cacheParams;

      $this->validateCacheParameters($cacheParams);

      if(empty($cacheParams)):
        $this->cache = array();
      else:
        $cache = &$this->cache;
        while(!empty($cacheParams)):
          $cacheParam = array_shift($cacheParams);

          $cache = &$cache[$cacheParam];
        endwhile;

        unset($cache[self::RESERVED_CACHE_KEY]);
      endif;
    }

    /**
     * @param $cacheParams
     *
     * @return mixed
     * @throws cheeseExceptions\InvalidCacheParameter
     */
    public function geCacheValue($cacheParams) {
      $this->validateCacheParameters($cacheParams);
      
      if($this->isCacheSet($cacheParams)):

        $cacheParams[] = self::RESERVED_CACHE_KEY;
        $cacheParams[] = self::LEAF_VALUE;

        $cache = $this->cache;
        while(count($cacheParams) != 0):
          $cacheParam = array_shift($cacheParams);

          $cache = $cache[$cacheParam];
        endwhile;
      else:
        $cache = null;
      endif;

      $cleanValue = $this->cleanValue($cache);

      return $cleanValue;
    }

    /**
     * @param $cacheParams
     *
     * @return bool
     * @throws cheeseExceptions\InvalidCacheParameter
     */
    public function isCacheSet($cacheParams) {
      $this->validateCacheParameters($cacheParams);
      
      $cache = &$this->cache;

      $cacheParams[] = self::RESERVED_CACHE_KEY;

      while(!empty($cacheParams)):
        $cacheParam = array_shift($cacheParams);

        if(!isset($cache[$cacheParam])):
          return false;
        else:
          $cache = &$cache[$cacheParam];
        endif;
      endwhile;

      return true;
    }

    /**
     * @param bool $debug
     */
    public function setDebugging($debug = false) {
      $debug = (bool)$debug;

      if($this->debug !== $debug):
        $this->cache = array();
      endif;

      $this->debug = (bool)$debug;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    private function cleanValue($value) {
      if(is_object($value)):
        $value = clone $value;
      endif;

      return $value;
    }

    /**
     * @param $cacheParams
     * @param $value
     */
    private function setCacheValue($cacheParams, $value) {
      $branch = &$this->cache;
      while(!empty($cacheParams)):
        $cacheParam = array_shift($cacheParams);

        if(!isset($branch[$cacheParam])):
          $branch[$cacheParam] = array();
        endif;

        $branch = &$branch[$cacheParam];
      endwhile;

      $value = $this->cleanValue($value);

      if($this->debug):
        $debug_backtrace = debug_backtrace();
        $leaf = array(
          self::LEAF_VALUE  => $value,
          self::LEAF_CALLER => sprintf(
            '%s%s%s',
            $debug_backtrace[2]['class'],
            $debug_backtrace[2]['type'],
            $debug_backtrace[2]['function']
          )
        );
      else:
        $leaf = array(
          self::LEAF_VALUE => $value
        );
      endif;

      $branch[self::RESERVED_CACHE_KEY] = $leaf;
    }

    /**
     * @param $cacheParams
     *
     * @throws cheeseExceptions\InvalidCacheParameter
     */
    private function validateCacheParameters($cacheParams) {
      if(in_array(self::RESERVED_CACHE_KEY, $cacheParams)):
        throw new cheeseExceptions\InvalidCacheParameter(self::RESERVED_CACHE_KEY);
      endif;
    }

  }