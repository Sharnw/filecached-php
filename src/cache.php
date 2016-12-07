<?php

/**
*  
* A simple key-value datastore using files instead of memory.
*
* Useful for caching large datasets, and replacing memcache on low memory AWS instances.
* 
* @author hustcc
* @author sharnw
* @link https://github.com/Sharnw/php-file-cache
* @license MIT
*/
class Filecached {

    public $cache_path = 'cache/'; // default cache folder
    public $cache_time = 86400; // cache file expires after 1 day
    public $cache_extension = '.cache'; // default file extension
    
    public function __construct($cache_path = 'cache/', $cache_time = 86400, $cache_extension = '.cache') {
        $this->cache_path = $cache_path;
        $this->cache_time = $cache_time;
        $this->cache_extension = $cache_extension;
        if (!file_exists($this->cache_path)) {
            mkdir($this->cache_path, 0777);
        }
    }
    
    /*
    * Set a key/value pair
    */
    public function set($key, $value) {
        if (empty($value)) $this->delete($key);

        $filename = $this->_get_cache_file($key);
        file_put_contents($filename, serialize($value));
        //file_put_contents($filename, serialize($value), LOCK_EX);
    }
    
    /*
    * Remove a cache file by key
    */
    public function delete($key) {
        $filename = $this->_get_cache_file($key);
        if (file_exists($filename)) unlink($filename);
    }
    
    /*
    * Retrieve a cache file by key
    */
    public function get($key, $force_expire = false) {
        if ($this->_has_cache($key, $force_expire)) {
            $filename = $this->_get_cache_file($key);
            $value = file_get_contents($filename);
            if (empty($value)) {
                return false;
            }
            return unserialize($value);
        }
    }
    
    /*
    * Flush all cache files in a namespace, defaults to all
    */
    public function flush($prefix = '') {
        $fp = opendir($this->cache_path);
        while(!false == ($fn = readdir($fp))) {
            if($fn == '.' || $fn =='..' || ($prefix != '' and substr($fn, 0, strlen($prefix)) != $prefix)) {
                continue;
            }
            unlink($this->cache_path . $fn);
        }
    }

    /*
    * Remove all expired cache files
    */
    public function clean() {
        $fp = opendir($this->cache_path);
        while(!false == ($fn = readdir($fp))) {
            if($fn == '.' || $fn =='..') {
                continue;
            }
            if (filemtime($fn) + $this->cache_time >= time()) unlink($this->cache_path . $fn);
        }
    }
    
    /*
    * Check for a chache file by key
    *
    * @param Integer $force_expire Pass a new timestamp for file to expire at
    */
    private function _has_cache($key, $force_expire = false) {
        $expire_time = ($force_expire ? $force_expire : $this->cache_time);
        $filename = $this->_get_cache_file($key);
        if(file_exists($filename) && (filemtime($filename) + $expire_time >= time())) {
            return true;
        }
        return false;
    }
    
    /*
    * Return a safe filename by key
    */
    private function _safe_filename($key) {
        if ($key != null) {
            $parts = explode('_', $key);
            if (count($parts) > 1) { // allow for prefixing of filenames
                return $parts[0].'_'.md5($key);
            }
            return md5($key);
        }
        return 'invalid_cache_key';
    }
    
    /*
    * Retrieve the expected cache file name by key
    */
    private function _get_cache_file($key) {
        return $this->cache_path . $this->_safe_filename($key) . $this->cache_extension;
    }
}

?>