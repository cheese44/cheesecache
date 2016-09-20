<?php

  namespace cheeseCache\exceptions;

  /**
   * @author cheese44
   */
  class CacheCollision extends \Exception {

    /**
     * InvalidCollisionMode constructor.
     *
     * @param string[]        $cacheParams
     * @param string[]        $callers
     * @param \Exception|null $previous
     */
    public function __construct($cacheParams, $callers, $code = 0, \Exception $previous = null) {
      $message = 'Collision detected at "'.implode(' -> ', $cacheParams).'": '.implode(', ', $callers);

      parent::__construct($message, $code, $previous);
    }
    
  }