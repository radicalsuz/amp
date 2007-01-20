<?php
require_once ('AMP/Content/Page/Urls.inc.php');
define( 'AMP_TEXT_ERROR_PERMISSION_DENIED', 'You do not have permission to write this file: ' );

class AMPSystem_Upload {

    var $_file_name;
    var $_file_extension;

    var $_path_target;

    var $_fileRef;

    var $_autoRename = true;
    var $_target_folder = AMP_CONTENT_URL_DOCUMENTS;

    var $_errors;

    function AMPSystem_Upload( $filename = null ) {
        if (isset($filename)) $this->setFile( $filename );
    }

    function execute( $temp_name, $allow_existing_file=false) {
        if ( $this->_autoRename ) {
            $this->_cleanFilename( );
            $this->_path_target = $this->_findSafeFilename();
        }

        if(is_uploaded_file($temp_name)) {
            if (! move_uploaded_file( $temp_name, $this->_path_target )) return false;
        } elseif ($allow_existing_file) {
            if (! rename( $temp_name, $this->_path_target )) return false;
        } else return false;
        chmod( $this->_path_target, 0755 );

        AMP_s3_save( $this->_path_target );
        return true;
    }

    function _cleanFilename( ) {
        $this->_file_name = str_replace( array( '#', '&', '\'' ), '_', $this->_file_name );
    }


    function getTargetPath() {
        return $this->_path_target ;
    }

    function setFile( $filename ) {
        if ($dotspot = strrpos( $filename, "." )) {
            $this->_file_extension = strtolower( substr( $filename, $dotspot+1) );
        }
        $this->setTargetFileName( $filename );
    }

    function setTargetFileName( $name ) {
        $this->_file_name  = AMP_removeExtension( $name) ;
    }

    function setFolder( $folder_name ) {
        $base_folder_name = $folder_name;
        if ( substr( $folder_name, -1 ) == DIRECTORY_SEPARATOR ) {
            $base_folder_name = substr( $folder_name, strlen( $folder_name ) -1 );
        }
        if ( substr( $folder_name, 0, 1 ) == DIRECTORY_SEPARATOR ) {
            $base_folder_name = substr( $folder_name, strlen( $folder_name ), 1 );
        }
        $actual_path = AMP_LOCAL_PATH.DIRECTORY_SEPARATOR.$base_folder_name;
        if (!is_writable( $actual_path )) {
            $this->addError( AMP_TEXT_ERROR_PERMISSION_DENIED . $base_folder_name);
            return false;
        }

        $this->_target_folder = DIRECTORY_SEPARATOR . $base_folder_name . DIRECTORY_SEPARATOR;
        return true;
    }

    function _addExtension() {
        if (!(isset($this->_file_extension) && $this->_file_extension)) return false;
        return "." . $this->_file_extension;
    }


    function _findSafeFilename( $num = 0 ) {
        $testfile = $this->_file_name . $this->_addExtension();
        if ($num) $testfile = $this->_file_name ."_".$num. $this->_addExtension();

        $testpath =  AMP_LOCAL_PATH . $this->_target_folder . $testfile;

        if ( !file_exists($testpath) ) {
            return $testpath;
        }

        return $this->_findSafeFilename( ++$num );
    }

    function addError( $text, $name = null ) {
        if (isset($name )) return $this->_errors[ $name ] = $text;
        return $this->_errors[] = $text;
    }

    function getErrors() {
        return $this->_errors;
    }

}

?>
