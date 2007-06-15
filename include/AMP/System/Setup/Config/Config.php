<?php

require_once( 'AMP/System/File/Text.php');

class AMP_System_Config extends AMP_System_File_Text {

    function AMP_System_Config( $dbcon = null, $file_path = null ){
        $this->__construct( $file_path );
    }

    function getBody( ) {
        return $this->getSettings( );
    }

    function getSettings( ) {
        if ( isset( $this->_file_settings )) return $this->_file_settings;
        $settings = parse_ini_file( $this->getPath( ));
        $this->_file_settings = $settings;
        return $settings;
    }

    function getData( ) {
        $base_values = ( 'id' => $this->id, 'name' => $this->id );
        return array_merge( $base_values, $this->getSettings( ));
    }

}

?>
