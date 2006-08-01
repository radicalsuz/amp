<?php

require_once( 'AMP/System/Cache/File.php');

class AMP_System_Cache_Memcache extends AMP_System_Cache {

    var $_memcache_connection;
    var $_retrieved_items = array( );

    function AMP_System_Cache_Memcache( ){
        $this->__construct( );
    }

    function __construct( ){
        //ensure memcache is set
        $connected = $this->_init_connection( );
        if ( !$connected ) {
            return;
        }
        $this->_unique_site_key = AMP_SYSTEM_UNIQUE_ID;
        $this->_load_index( );

    }

    function _init_connection( ){
        if (!class_exists( 'Memcache' )) return false;
        $memcache_connection = &new Memcache;
        $result = $memcache_connection->connect( AMP_SYSTEM_MEMCACHE_SERVER, AMP_SYSTEM_MEMCACHE_PORT );
        if ( $result ) {
            $this->_memcache_connection = &$memcache_connection;
        } else {
            trigger_error( sprintf( AMP_TEXT_ERROR_CACHE_REQUEST_FAILED, 'Memcache', 'connect', 'Request: '.$_SERVER['REQUEST_URI'] ) );
        }
        return $result;
    }

    function has_connection( ){
        return isset( $this->_memcache_connection );
    }

    function &instance( ){
        static $cache = false;
        if ( !$cache) $cache = new AMP_System_Cache_Memcache;
        if ( !$cache->has_connection( )) {
            $cache = new AMP_System_Cache_File;
            if ( AMP_DISPLAYMODE_DEBUG_CACHE ) {
                trigger_error( sprintf( AMP_TEXT_ERROR_CACHE_CONNECTION_FAILED, get_class( $this )) );
            }
        }
        return $cache;
    }


    function add( &$item, $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;

        $result = $this->_memcache_connection->set( $authorized_key, $item, MEMCACHE_COMPRESSED );
        if ( $result ) {
            $this->_add_index_key( $authorized_key );
            if ( isset( $this->_items_retrieved[ $authorized_key ])) {
                $this->_items_retrieved[ $authorized_key ] = &$item;
            }
        } elseif ( AMP_DISPLAYMODE_DEBUG_CACHE ) {
            trigger_error( sprintf( AMP_TEXT_ERROR_CACHE_REQUEST_FAILED, get_class( $this ), __FUNCTION__, $key ) );
        }
        
        return $result;

    }

	function refresh( $key ) {
		$authorized_key = $this->authorize($key);
        if ( !$authorized_key ) return false;
        $this->_add_index_key( $authorized_key );
		if (isset($this->_items_retrieved[ $authorized_key ] )) {
			$this->_memcache_connection->set( $authorized_key, $this->_items_retrieved[ $authorized_key ], MEMCACHE_COMPRESSED );
		}
	}

    function contains( $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;

        if ( $authorized_key == $this->_index_key ) return true;
        if ( !isset( $this->_index[$authorized_key] )) return false; 
    
        return $this->_confirm_memcache_retrieve( $authorized_key );

    }

    function _confirm_memcache_retrieve( $authorized_key ){
        if ( isset( $this->_items_retrieved[ $authorized_key ])) return $authorized_key;
        $item = &$this->_memcache_connection->get( $authorized_key ) ;
        $this->_items_retrieved[ $authorized_key ] = &$item;
        if ( !$item ) return false; 
        return $authorized_key;
    }

    function &retrieve( $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;
        if ( !$this->contains( $authorized_key )) {
            if ( AMP_DISPLAYMODE_DEBUG_CACHE ) {
                trigger_error( sprintf( AMP_TEXT_ERROR_CACHE_REQUEST_FAILED, get_class( $this ), __FUNCTION__, $key ));
            }
            return false;
        }

        $result = $this->_confirm_memcache_retrieve( $authorized_key );
        if ( !$result ) return false;

        return $this->_items_retrieved[ $authorized_key ];
    }

    function delete( $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;

        $result = $this->_memcache_connection->delete( $authorized_key );
        if ( $result ) {
            $this->_remove_index_key( $authorized_key );
            unset( $this->_items_retrieved[ $authorized_key ]);
        }
        return $result;
    }
    
    function clear( $key_token = null ){
        $preserve_keys = array( );
        foreach( $this->_index as $authorized_key => $time_stored ){
            if ( isset( $key_token ) && ( strpos( $authorized_key, $key_token ) === FALSE )) {
                $preserve_keys[] = $authorized_key;
                continue;
            }
            $this->delete( $authorized_key );
        }

        if ( isset( $key_token ) && !empty( $preserve_keys )) {
            $this->_items_retrieved = array_combine_key( $preserve_keys, $this->_items_retrieved );
        } else {
            $this->_items_retrieved = array( );
        }
    }
}

?>
