<?php

/**
 * AMP_System_Cache 
 * 
 * @package AMP_System
 * @version 3.5.9
 * @copyright 2006 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMP_System_Cache {

    var $_index_key = 'AMP_cache_index';
    var $_index = array( );
    var $_unique_site_key;

    //{{{ constructor, &instance, __construct, __destroy
    function AMP_System_Cache( ){
        $this->__construct( );
    }

    function &instance( ){
        static $cache = false;
        if ( !$cache) $cache = new AMP_System_Cache;
        return $cache;
    }

    function __construct( ){
        $this->_load_index( );
    }

    function __destroy( ){
        //wont work in php 4
        $this->_save_index( );
    }

    //}}}

    // {{{ public methods: add, retrieve, delete, contains, clear, url, authorize, authorize_tainted
    function add( $item, $key ){

    }

    function contains( $key ){

    }

    function &retrieve( $key ){

    }

    function delete( $key ){

    }
    
    function clear( ){

    }

    //}}}

    //{{{ authorize methods
    function authorize( $key ){
        if ( !( $key && is_string( $key ))) return false;
        $local_key = $this->localize( $key );
        if ( strpos( $key, '%s' ) === FALSE ) return $local_key ;
        return sprintf( $local_key, AMP_SYSTEM_USER_ID );
    }
/*
    function authorize_tainted( $key ){
        if ( strpos( $key, '%s' ) === FALSE ) return false;
        return $this->authorize( $key );
    }
    */

    function localize( $key ) {
        if ( !isset( $this->_unique_site_key )) return $key;
        if ( strpos( $key, ( '__'.$this->_unique_site_key.'__') ) === FALSE ) return $key;
        return sprintf( '__%s__%s', $this->_unique_site_key, $key );
    }

    function identify( $key, $id = null ) {
        if ( !isset( $id )) return $key;
        if ( strrpos( $key, ( '__'. $id .'__') ) === 0 ) return $key;
        return $key . '__' . $id . '__'; 
    }

    function publicize( $key ){
        if ( !isset( $this->_unique_site_key )) return $key;
        $site_key_imprint = '__' . $this->_unique_site_key . '__';
        if ( strpos( $key, $site_key_imprint ) === FALSE ) return $key;
        return substr( $key, strlen( $site_key_imprint));

    }

    function url( $key  ){
        return AMP_Url_AddVars( AMP_SYSTEM_URL_CACHE_DATA, array( 'key' => 'key='. $this->publicize( $key ) ));
    }
    // }}}

    // {{{ private index management methods: _load_index, _save_index, _add_index_key, _remove_index_key 
    function _load_index( ){
        $result = $this->retrieve( $this->_index_key );
        if ( !$result ) {
            return $this->_index = array( );

        }

        $this->_index = $result;
        $this->_clear_expired( );
        $this->_save_index( );

    }

    function _clear_expired( ){
        foreach( $this->_index as $authorized_key => $time_stored ){
            if ( $time_stored + AMP_SYSTEM_CACHE_TIMEOUT < time( )){
                $this->delete( $authorized_key );
            }
        }

    }

    function _save_index( ){
        return $this->add( $this->_index, $this->_index_key );
    }

    function _add_index_key( $authorized_key ){
        if ( $authorized_key == $this->_index_key ) return true;
        $this->_index[ $authorized_key ] = time( );
        return $this->_save_index( );

    }

    function _remove_index_key( $authorized_key ){
        unset( $this->_index[ $authorized_key ]);
        $this->_save_index( );
    }

    function has_connection( ){
        return true;
    }
    // }}}

    // {{{ serialize wrappers: to_string, from_string
    function to_string( &$item ) {
        return serialize( $item );
    }

    function from_string( &$item ){
        return unserialize( $item );
    }
    // }}}

}

?>
