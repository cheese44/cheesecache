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
     * @return array
     */
    public function getValidCollisionModes();

    /**
     * @param int $mode
     *
     * collision mode will only take effect when debugging is activated
     */
    public function setCollisionMode($mode = self::COLLISION_MODE_IGNORE);

    /**
     * @param bool $debug
     */
    public function setDebugging($debug = false);

    /**
     * @param int $memoryLimit
     * memory limit in MB that will be applied to the object.
     * the cache will try not to occupy more memory by deleting previously cached values.
     *
     * if the limit is set to 0 no limitation will be applied
     */
    public function setMemoryLimit($memoryLimit = 0);
  }