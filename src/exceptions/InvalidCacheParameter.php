<?php

  namespace cheeseCache\exceptions;

  /**
   * @author cheese44
   */
  class InvalidCacheParameter extends \Exception {

    /**
     * InvalidCollisionMode constructor.
     *
     * @param string|int      $invalidParameter
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($invalidParameter = "", $code = 0, \Exception $previous = null) {
      $message = sprintf('Invalid parameter given for cache: "%s"', $invalidParameter);
      
      parent::__construct($message, $code, $previous);
    }
    
  }