<?php

if (!defined( 'AMP_SITE_MEMCACHE_TIMEOUT')) define ( 'AMP_SITE_MEMCACHE_TIMEOUT', 180 );
if (!defined( 'AMP_SITE_KEY_INDEX')) define ( 'AMP_SITE_KEY_INDEX', 'AMP_SITE_KEY_INDEX' );

class AMPSystem_Memcache {

    var $memcache_connection;
    var $_memcache_server = 'localhost';
    var $_memcache_port = 11211;

    function AMPSystem_Memcache() {
        $this->init();
    }

    function init() {
        if (!class_exists( 'Memcache' )) return false;
        $this->memcache_connection = &new Memcache;
        $this->memcache_connection->pconnect( $this->_memcache_server, $this->_memcache_port );
    }

    function &instance() {
        static $memcache = false;
        if (!$memcache) $memcache = new AMPSystem_Memcache();
        if (!isset($memcache->memcache_connection)) return false;
        return $memcache;
    }

    function &getPageItem( $item_key )  {
        $cachekey = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . $item_key;
        return $this->memcache_connection->get( $cachekey );
    }

    function setPageItem( $item_key, $item_value ) {
        $cachekey = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . $item_key;
		$this->addSiteKey($_SERVER['SERVER_NAME'], $cachekey);
        return $this->memcache_connection->set( $cachekey, $item_value , MEMCACHE_COMPRESSED, AMP_SITE_MEMCACHE_TIMEOUT  );
    }

    function setSiteItem( $item_key, $item_value ) {
        $cachekey = $_SERVER['SERVER_NAME'] . $item_key;
		$this->addSiteKey($_SERVER['SERVER_NAME'], $cachekey);
        return $this->memcache_connection->set( $cachekey, $item_value , MEMCACHE_COMPRESSED, AMP_SITE_MEMCACHE_TIMEOUT  );
    }

    function &getSiteItem( $item_key )  {
        $cachekey = $_SERVER['SERVER_NAME'] . $item_key;
        return $this->memcache_connection->get( $cachekey );
    }

	function addSiteKey( $site_name, $key ) {
		$key_index = $this->getSiteKeys( $site_name );
		$key_index[$key] = time();
		$this->memcache_connection->set( AMP_SITE_KEY_INDEX.$site_name, $key_index );
	}

	function getSiteKeys( $site_name ) {
		$key_index = $this->memcache_connection->get( AMP_SITE_KEY_INDEX.$site_name );
		return array_keys( $key_index );
	}

	function flushSite( $site_name ) {
		foreach($this->getSiteKeys( $site_name ) as $key) {
			$this->memcache_connection->delete( $key );
		}
		$this->memcache_connection->delete( AMP_SITE_KEY_INDEX.$site_name );
	}

}
?>
