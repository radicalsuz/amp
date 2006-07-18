<?php

require_once( 'AMP/System/Cache/Cache.php');
require_once( 'AMP/System/File/File.php');

/**
 * AMP_System_Cache_File 
 * 
 * @uses AMP_System_Cache
 * @package AMP_System
 * @version 3.5.9
 * @copyright 2006 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMP_System_Cache_File extends AMP_System_Cache {

    var $_path_cache = AMP_SYSTEM_CACHE_PATH;

    function AMP_System_Cache_File( ){
        $this->__construct( );
    }

    function __construct( ){
        if ( !file_exists( $this->_path_cache )){
            if ( !mkdir( $this->_path_cache )){
                trigger_error( sprintf( AMP_TEXT_ERROR_CACHE_PATH_NOT_FOUND, $this->_path_cache ));
            }
        }
        $this->_load_index( );

    }

    function &instance( ){
        static $cache = false;
        if ( !$cache) $cache = new AMP_System_Cache_File;
        if ( !$cache->has_connection( )) {
            $cache = false;
        }
        return $cache;
    }

    function add( $item, $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;
        $serialized_item = $this->to_string( $item );
        $entry_ref = & fopen( $this->_path( $authorized_key ), 'wb' );
        if ( !$entry_ref ) return false;

        $result = fwrite( $entry_ref, $serialized_item ) ;
        fclose( $entry_ref );

        $this->_add_index_key( $authorized_key );
        
        return $result;

    }

    function contains( $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;
        if ( $authorized_key == $this->_index_key ) {
            return file_exists( $this->_path( $authorized_key ));
        }

        if ( isset( $this->_index[$authorized_key]) 
             && file_exists( $this->_path( $authorized_key ))){
            return $authorized_key;
        }
        return false;

    }

    function &retrieve( $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;
        if ( !$this->contains( $authorized_key )) return false;
        $serialized_item = file_get_contents( $this->_path( $authorized_key ));
        if ( !$serialized_item )  return false;
        return $this->from_string( $serialized_item );
    }

    function delete( $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;
        if ( !$this->contains( $authorized_key )) return false;

        $this->_remove_index_key( $authorized_key );
        $entry_ref = & new AMP_System_File( $this->_path( $authorized_key ));
        return $entry_ref->delete( );
    }
    
    function clear( $key_token = null ){
        foreach( $this->_index as $authorized_key => $time_stored ){
            if ( isset( $key_token ) && ( strpos( $authorized_key, $key_token ) === FALSE )) continue;
            $this->delete( $authorized_key );
        }
        $file_set = AMPfile_list( $this->_path_cache );

        foreach( $file_set as $file_name ){
            if ( isset( $key_token ) && ( strpos( $file_name, $key_token ) === FALSE )) continue;
            $entry_ref = & new AMP_System_File( $this->_path( $file_name ));

            if ( $entry_ref->getTime( )+ AMP_SYSTEM_CACHE_TIMEOUT < time( )){
                $entry_ref->delete( );
            }
        }
    }

    function _path( $authorized_key ){
        $result = $this->_path_cache . DIRECTORY_SEPARATOR. $authorized_key ;
        return $result;
    }

    function has_connection( ){
        return $this->_save_index( );
    }

    function authorize( $key ){
        $authorized_key = false;
        $standard_key = PARENT::authorize( $key );

        if ( $standard_key ) {
            //check for the slash char
            $authorized_key = str_replace( DIRECTORY_SEPARATOR, '__DS__', $standard_key );
        }
        return $authorized_key;
    }


}

?>
