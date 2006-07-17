<?php

if (!defined( 'MEMCACHE_KEY_INDEX')) define ( 'MEMCACHE_KEY_INDEX', 'keyIndex' );

/**
 * AMPSystem_Memcache 
 * This caching library is deprecated as of 3.5.9
 * use AMP/System/Cache instead
 *
 * @package 
 * @version 3.5.9
 * @copyright 2006 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMPSystem_Memcache {

    var $memcache_connection;
    var $_memcache_server = 'localhost';
    var $_memcache_port = 11211;
    var $_key_index;

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
        if( $this->memcache_connection->set( $cachekey, $item_value , MEMCACHE_COMPRESSED, AMP_SITE_MEMCACHE_TIMEOUT  )) {
            $this->addSiteKey($cachekey);
            return true;
        }
        return false;
    }

    function setSiteItem( $item_key, $item_value ) {
        $cachekey = $_SERVER['SERVER_NAME'] . $item_key;
        if ( $this->memcache_connection->set( $cachekey, $item_value , MEMCACHE_COMPRESSED, AMP_SYSTEM_CACHE_TIMEOUT )) {
            if ( $item_key != MEMCACHE_KEY_INDEX ) $this->addSiteKey( $cachekey );
            return true;
        }
        return false;
    }

    function &getSiteItem( $item_key )  {
        $cachekey = $_SERVER['SERVER_NAME'] . $item_key;
        return $this->memcache_connection->get( $cachekey );
    }

    function deleteSiteItem( $item_key ) {
        $cachekey = $_SERVER['SERVER_NAME'] . $item_key;
        return $this->memcache_connection->delete( $item_key );
    }

	function addSiteKey( $cachekey ) {
		$key_index = &$this->getSiteKeys();
        if ( array_search( $cachekey, $key_index ) !== FALSE ) return true;
		$key_index[] = $cachekey;
		return $this->setSiteItem( MEMCACHE_KEY_INDEX, $key_index );
	}

	function &getSiteKeys() {
        if (isset( $this->_key_index)) return $this->_key_index;
		if(!$key_index = &$this->getSiteItem( MEMCACHE_KEY_INDEX )) {
            $key_index = array();
			return $key_index;
		}
        $this->_key_index = &$key_index;
		return $this->_key_index;
	}

	function flushSite() {

        $keys_index = &$this->getSiteKeys();
		foreach($keys_index as $key) {
			$this->memcache_connection->delete( $key );
		}

		$this->deleteSiteItem( MEMCACHE_KEY_INDEX );
        $this->_key_index = null;
        
	}

}
?>
