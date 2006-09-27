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

require_once( 'AMP/System/Cache/Config.inc.php');

class AMP_System_Cache {

    var $_index_key = 'AMP_cache_index';
    var $_index = array( );
    var $_unique_site_key;

    //{{{ constructor, &instance, __construct, __destroy
    function AMP_System_Cache( ){
        $this->__construct( );
    }

    /*
    function &instance( ){
        static $cache = false;
        if ( !$cache) $cache = new AMP_System_Cache;
        return $cache;
    }
    */

    function __construct( ){
        $this->_load_index( );
    }

    function __destroy( ){
        //wont work in php 4
        $this->_save_index( );
    }

    //}}}

    // {{{ public methods: add, retrieve, delete, contains, clear, url, refresh, header, age 
    function add( $item, $key ){

    }

    function contains( $key ){

    }

    function &retrieve( $key ){

    }

    function delete( $key ){

    }
    
    function clear( $key_token = null ){
        foreach( $this->_index as $authorized_key => $time_stored ){
            if ( isset( $key_token ) && ( strpos( $authorized_key, $key_token ) === FALSE )) continue;
            $this->delete( $authorized_key );
        }
    }

    function refresh( $key ) {
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;
        $this->_add_index_key( $authorized_key );
    }

    function header( $key ) {
        $javascript_token = str_replace( '%s', '', AMP_CACHE_KEY_JAVASCRIPT );
        $stylesheet_token = str_replace( '%s', '', AMP_CACHE_KEY_STYLESHEET );

        $dotpoint = strrpos( $key, '.');
        if ( !$dotpoint ) return false;

        if ( substr( $key, $dotpoint ) == $javascript_token ) {
            return 'Content-Type: text/javascript';
        }
        if ( substr( $key, $dotpoint ) == $stylesheet_token ) {
            return 'Content-Type: text/css';
        }
        return false;
    }

    function age( $key ) {
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key || !isset( $this->_index[ $authorized_key ])) return false;
        return time( ) - $this->_index[$authorized_key];
    }

    function shutdown( ){
        return true;
    }

    function failover( ) {
        return false;
    }

    //}}}

    //{{{ authorize methods
    function authorize( $key ){
        if ( !( $key && is_string( $key ))) {
            trigger_error( sprintf( AMP_TEXT_ERROR_CACHE_INVALID_KEY , get_class( $key ) ));
            return false;
        }
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
        if ( strpos( $key, ( '__'.$this->_unique_site_key.'__') ) !== FALSE ) return $key;
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
        $this->_index_key = $this->localize( $this->_index_key );
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
