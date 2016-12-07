# Filecached - PHP datastore #

A simple key-value datastore using filesystem instead of memory.

Useful for caching large datasets, and replacing memcache on low memory AWS instances.

Credit to https://github.com/hustcc for the original.

## Installation ##

Add the following line to your composer.json file:

    {
        "require": {
            "sharnw/filecached-php": "dev-master"
        }
    }

## Usage ##

Similar interface to memcache

* `set(k, v)` set key-value pair
* `get(k)` get key value, returns false if nothing present
* `delete(k)` delete key-value pair
* `flush()` wipe all cache data

### Demo ###
    
    require_once('src/cache.php');

    $cache = new Filecached();

    $example_data = [
        'time' => time(),
        'message' => 'something certainly happened today.',
    ];

    $cache->set('todays_news', $example_data);

    print_r($storeData = $cache->get('todays_news'));

    $example_data = [
        [
            'url' => 'https://libraries.io/github/hustcc/php-file-cache',
            'title' => 'Forked repo'
        ],
    ];

    $cache->set('todays_links', $example_data);

    print_r($storeData = $cache->get('todays_links'));

    $cache->flush('today_'); // flush all data in 'today/' namespace

    $cache->set('more data', ['blah']);

    $cache->flush(); // flush all data

## License ##
Released under the terms of the [MIT license](http://opensource.org/licenses/MIT).

## Links ##

* Forked lib - https://libraries.io/github/hustcc/php-file-cache
