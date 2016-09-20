# cheeseCache

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://img.shields.io/travis/cheese44/cheesecache/master.svg?style=flat-square)](https://travis-ci.org/cheese44/cheeseCache)
[![Coverage Status](https://img.shields.io/codecov/c/github/cheese44/cheesecache.svg?style=flat-square)](https://codecov.io/github/cheese44/cheesecache)

A request scoped cache library for PHP with the goal to make caching consistent and maintainable.

Best used for caching function and query results that are called multiple times during a request and are unlikely to change

## Installation

Package is available on [Packagist](https://packagist.org/packages/cheese44/cheesecache), you can install it
using [Composer](http://getcomposer.org).

```bash
composer require cheese44/cheesecache
```

## Usage

```php

    use cheeseCache\app;
    
    class Test {
  
        /**
         * cheeseCache does all the work for you
         * 
         * you don't have to clutter your code with checking, setting and reading the cache yourself
         */
        public function cacheSum($a, $b) {
            $cache = cheeseCache\app\cacheProvider::getCache();
      
            $sum = $cache->cache(
                array($a, $b),
                function() use($a, $b) {
                    return $a + $b;
                });
        
            return $sum;
        }
    
        /**
         * of course cheeseCache still gives you the possibility to manually access these functionalities
         */
        public function cacheSum_Explicit($a, $b) {
            $cache = cheeseCache\app\cacheProvider::getCache();
      
            if($cache->isCacheSet(array($a, $b))):
                return $cache->geCacheValue(array($a, $b));
            endif;
      
            $sum = $a + $b;
      
            $cache->cache(
                array($a, $b),
                $sum
            );
      
            return $sum;
        }
  
    }
    
```
