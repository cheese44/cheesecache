<?php

  namespace cheeseCache\interfaces;

  /**
   * @author cheese44
   */
  interface ICache {

    const COLLISION_MODE_ERROR = 2;
    const COLLISION_MODE_IGNORE = 1;
    const COLLISION_MODE_LOG = 4;

    /**
     * @param array          $cacheParams
     * @param callable|mixed $cacheable  //if callable, method must return value to be cached.
     * @param bool           $renewCache //cached value will be overwritten if true
     *
     * @return mixed
     */
    public function cache($cacheParams, $cacheable, $renewCache = false);

    /**
     * @param array $cacheParams
     */
    public function clearCache($cacheParams = array());

    /**
     * @param $cacheParams
     *
     * @return mixed
     */
    public function geCacheValue($cacheParams);

    /**
     * @param $cacheParams
     *
     * @return bool
     */
    public function isCacheSet($cacheParams);

    /**
     * @param bool $debug
     */
    public function setDebugging($debug = false);

  }