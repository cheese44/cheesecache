<?php

  namespace cheeseCache\exceptions;

  /**
   * @author cheese44
   */
  class InvalidCollisionMode extends \Exception {

    /**
     * InvalidCollisionMode constructor.
     *
     * @param string|int      $givenMode
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($givenMode = "", $code = 0, \Exception $previous = null) {
      $message = sprintf('Invalid Collision mode given: "%s"', $givenMode);
      
      parent::__construct($message, $code, $previous);
    }
    
  }