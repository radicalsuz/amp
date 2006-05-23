<?php

class Blast_Config_Admin extends AMPSystem_Data_Item {
    var $datatable = 'phplist_admin';

    function Blast_Config_Admin( &$dbcon, $id = null ){
        $this->init( $dbcon, $id );
    }

    function getEmail( ){
        return $this->getData( 'email');
    }

    function getPassword( ){
        return $this->getData( 'password');
    }
}

?>
