<?php
require_once( 'AMP/System/Data/Config.inc.php');

class AMP_System_File {

    var $_path;
    var $_basename;
    var $_extension;

    var $_sort_property;
    var $_sort_direction = AMP_SORT_ASC;
    var $_sort_method = "";

    var $_observers = array( );
    var $id;

    function AMP_System_File( $file_path = null ){
        if ( isset( $file_path )) $this->setFile( $file_path );
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

    function search( $folder_path, $filename_pattern = null ){
        /** suggested patterns
         * send in patterns as *php or *namestuff* or namestuff*
         * wildcard * is replaced with regex ( [0-9a-zA-Z\.-_ ]+
         * filename extension: /([0-9a-zA-z\.-_ ]+\.php)/
         *
         * */
        if ( isset( $filename_pattern)) 
            $regex_pattern =    '/' . 
                                str_replace( 
                                        array( '*', '.'),
                                        array( '[0-9a-zA-Z\.-_ ]+', '\.'), 
                                        $filename_pattern)  
                                . '/';

        $folder = &opendir( $folder_path );
        $result_set = array( );
        if ( substr( $folder_path, -1 ) !== DIRECTORY_SEPARATOR ) $folder_path .= DIRECTORY_SEPARATOR;
        #$result_text = system( 'ls '.$filename_pattern );
        while( $file_name = readdir( $folder )){
            if (($file_name ==".") || ($file_name == "..")) continue; 
            if ( isset( $regex_pattern ) && !preg_match( $file_name, $regex_pattern, $name_matches )) continue;
            $result_set[ $file_name ] = &new AMP_System_File( $folder_path . $file_name );
        }
        $this->sort( $result_set );
        return $result_set;
    }

    function sort( &$file_set, $sort_property=null, $sort_direction = null ){
        $this->_sort_default( $file_set );
        if ( !isset( $sort_property)) return true;

        if ( !$this->setSortMethod( $sort_property )) {
            trigger_error( 'sort by '.$sort_property.' failed in '.get_class( $this ).": no access method found" );
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
