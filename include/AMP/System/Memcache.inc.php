<?php

if (!defined( 'AMP_SITE_MEMCACHE_TIMEOUT')) define ( 'AMP_SITE_MEMCACHE_TIMEOUT', 60 );

class AMPSystem_Memcache {

    var $memcache_connection;
    var $_memcache_server = 'localhost';
    var $_memcache_port = 11211;

    function AMPSystem_Memcache() {
        $this->init();
    }

    function init() {
        $this->memcache_connection = &new Memcache;
        $this->memcache_connection->pconnect( $this->_memcache_server, $this->_memcache_port );
    }

    function &instance() {
        static $memcache = false;
        if (!$memcache) $memcache = new AMPSystem_Memcache();
        if (!isset($memcache)) return false;
        return $memcache;
    }

    function &getPageItem( $item_key )  {
        $cachekey = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . $item_key;
        return $this->memcache_connection->get( $cachekey );
    }

    function setPageItem( $item_key, $item_value ) {
        $cachekey = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . $item_key;
        return $this->memcache_connection->set( $cachekey, $item_value , MEMCACHE_COMPRESSED, AMP_SITE_MEMCACHE_TIMEOUT  );
    }

    function setSiteItem( $item_key, $item_value ) {
        $cachekey = $_SERVER['SERVER_NAME'] . $item_key;
        return $this->memcache_connection->set( $cachekey, $item_value , MEMCACHE_COMPRESSED, AMP_SITE_MEMCACHE_TIMEOUT  );
    }

    function &getSiteItem( $item_key )  {
        $cachekey = $_SERVER['SERVER_NAME'] . $item_key;
        return $this->memcache_connection->get( $cachekey );
    }

}
?>

