<?php

  namespace cheeseCache\app;

  use cheeseCache\interfaces as cheeseInterfaces;
  use cheeseCache\exceptions as cheeseExceptions;

  /**
   * @author cheese44
   */
  class CacheProvider {

    private static $cache;
    
    public static function getCache() {
      if(self::$cache instanceof cheeseInterfaces\ICache):
        return self::$cache;
      endif;
      
      self::$cache = new Cache();
      
      return self::$cache;
    }
    
    public static function destroyCache() {
      self::$cache = null;
    }
    
  }