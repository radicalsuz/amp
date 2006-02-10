<?php

class AMP_System_File {

    var $_path;
    var $_basename;
    var $_extension;

    function AMP_System_File( $file_path = null ){
        if ( isset( $file_path )) $this->setFile( $file_path );
    }

    function setFile( $file_path ){
        $this->_path = $file_path;
        $this->_basename = basename( $file_path );
        $this->_extension = $this->findExtension( $file_path );
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
        $folder = opendir( $folder_path );
        $result_set = array( );
        if ( substr( $folder_path, -1 ) !== DIRECTORY_SEPARATOR ) $folder_path .= DIRECTORY_SEPARATOR;
        while( $file_name = readdir( $folder )){
            if (($file_name ==".") || ($file_name == "..")) continue; 
            $result_set[ $file_name ] = &new AMP_System_File( $folder_path . $file_name );

        }
        uksort( $result_set, "strnatcasecmp" );
        return $result_set;
    }

}

?>
