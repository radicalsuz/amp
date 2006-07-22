<?php
require_once( 'AMP/System/Base.php');

class AMP_System_File {

    var $_path;
    var $_basename;
    var $_extension;

    var $_sort_property;
    var $_sort_direction = AMP_SORT_ASC;
    var $_sort_method = "";

    var $_observers = array( );
    var $id;
    var $_class_name = 'AMP_System_File';

    var $_mimetype;

    //mimetype cacheing is a performance optimization for large file lists
    var $_mimetype_cache;

    function AMP_System_File( $file_path = null ){
        if ( isset( $file_path ) && !is_object( $file_path )) $this->setFile( $file_path );
    }

    function setFile( $file_path ){
        $this->_path = $file_path;
        $this->_basename = basename( $file_path );
        $this->_extension = $this->findExtension( $file_path );
        $this->id = &$this->_basename;
    }

    function findExtension( $file_path ){
        if (!( $dotspot = strrpos( $file_path, "." ))) return false;
        return strtolower( substr( $file_path, $dotspot+1) );
        
    }

    function getTime( ){
        return filemtime( $this->getPath( ) );
    }

    function getItemDate( ){
        return date( 'M d, Y', $this->getTime( ));
    }

    function getName( ){
        return $this->_basename;
    }

    function getExtension( ){
        return $this->_extension;
    }

    function getPath( ){
        return $this->_path;
    }

    function set_mimetype( $mimetype_value = null ){
        if ( isset( $mimetype_value ) ) return $this->_mimetype = $mimetype_value;
        if ( !( function_exists( 'mime_content_type' ))) return false;

        $mime_filetype = false;
        if ( !( $mime_filetype = $this->lookup_mimetype( ))) {
            $mime_filetype = mime_content_type( $this->getPath() );
            $this->cache_mimetype( );
        }

        if ( !$mime_filetype ) return false;

        return $this->_mimetype = $mime_filetype ;
    }

    function get_mimetype( ){
        return $this->_mimetype;
    }

    function lookup_mimetype( $path ){
        $reg = &AMP_Registry::instance( );
        $mimetype_lookup = &$reg->getEntry( AMP_REGISTRY_MIMETYPE_CACHE );
        if ( !isset( $mimetype_lookup[ $this->getPath( ) ] )) return false;
        return $mimetype_lookup[ $this->getPath( )];
    }

    function _load_mimetype_cache( ){
        if ( isset( $this->_mimetype_cache )) return $this->_mimetype_cache;
        $reg = &AMP_Registry::instance( );
        $mimetype_lookup = &$reg->getEntry( AMP_REGISTRY_MIMETYPE_CACHE );
        if ( !$mimetype_lookup ) {
            $mimetype_lookup = &AMP_cache_get( AMP_REGISTRY_MIMETYPE_CACHE );
            if ( !$mimetype_lookup ){
                $blank_array= array( );
                $reg->setEntry( AMP_REGISTRY_MIMETYPE_CACHE, $blank_array );
                $this->_mimetype_cache = $blank_array;
            }
            
        }
        if ( $mimetype_lookup ) $this->_mimetype_cache = $mimetype_lookup;
        return $this->_mimetype_cache;

    }

    function cache_mimetype( ){
        $this->_mimetype_cache[ $this->getPath( )] = $this->_mimetype;
        $reg = &AMP_Registry::instance( );
        $reg->setEntry( AMP_REGISTRY_MIMETYPE_CACHE, $this->_mimetype_cache );
    }
    
    function _save_mimetype_cache( ){
        $mimetype_lookup = &$this->_load_mimetype_cache( );
        if ( $mimetype_lookup && !empty( $mimetype_lookup )) {
            AMP_cache_set( AMP_REGISTRY_MIMETYPE_CACHE, $mimetype_lookup );
        }
    }

    function search( $folder_path, $filename_pattern = null ){
        /** suggested patterns
         * send in patterns as *php or *namestuff* or namestuff*
         * wildcard * is replaced with regex ( [0-9a-zA-Z\.-_ ]+
         * filename extension: /([0-9a-zA-z\.-_ ]+\.php)/
         *
         * */
        if ( isset( $filename_pattern)) {
            $regex_pattern_1 = str_replace( '.', '\.', $filename_pattern);  
            $regex_pattern   =    '/'
                                . str_replace( '*' , '[0-9a-zA-Z\.-_ ]+' , $regex_pattern_1)  
                                . '/';
            #$regex_pattern = '/' . $filename_pattern . '/';


        } 

        $folder = &opendir( $folder_path );
        $result_set = array( );
        if ( substr( $folder_path, -1 ) !== DIRECTORY_SEPARATOR ) $folder_path .= DIRECTORY_SEPARATOR;
        #$result_text = system( 'ls '.$filename_pattern );
        $class_name = $this->_class_name;
        while( $file_name = readdir( $folder )){
            if (($file_name ==".") || ($file_name == "..")) continue; 
            if ( isset( $regex_pattern )){
                $name_matches = array( );
                preg_match( $regex_pattern, $file_name, $name_matches );
                if ( !count( $name_matches )) continue;
            }
            $result_set[ $file_name ] = &new $class_name( $folder_path . $file_name );
        }
        $this->sort( $result_set );
        $this->_save_mimetype_cache( );
        return $result_set;
    }

    function sort( &$file_set, $sort_property=null, $sort_direction = null ){
        $this->_sort_default( $file_set );
        if ( !isset( $sort_property)) return true;

        if ( !$this->setSortMethod( $sort_property )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_SORT_PROPERTY_FAILED, $sort_property, get_class( $this ) ));
            return false;
        }

        if ( isset( $sort_direction ))  $this->_sort_direction = $sort_direction;

        usort( $file_set, array( $this ,'_sort_compare'));
        return true;

    }

    function _sort_compare( $file1, $file2 ){
        if ( !( $sort_method = $this->_sort_accessor )) return 0;
        if ( $this->_sort_direction == AMP_SORT_DESC )
            return ( $file1->$sort_method( ) < $file2->$sort_method( ) ) ? 1 : -1; 
        return ( $file1->$sort_method( ) > $file2->$sort_method( ) ) ? 1 : -1; 
    }

    function setSortMethod( $sort_property ){
        $access_method = 'get' . ucfirst( $sort_property );
        if ( !method_exists( $this, $access_method )) return false;
        $this->_sort_accessor = $access_method;
        return true;
    }

    function _sort_default( &$file_set ){
        uksort( $file_set, "strnatcasecmp" );
    }

    function delete( ){
        $result = unlink( $this->getPath( ));
        $this->notify( 'delete');
        return $result;
    }

    function notify( $action ){
        foreach( $this->_observers as $observer ){
            $observer->update( $this, $action );
        }
    }

    function addObserver( &$observer, $observer_key = null ){
        if ( isset( $observer_key )){
            $this->_observers[$observer_key] = &$observer;
            return;
        }
        $this->_observers[] = &$observer;
    }


}

?>
