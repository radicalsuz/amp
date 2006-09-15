<?php
require_once( 'AMP/System/Data/Item.inc.php');

class AMP_System_User_Profile extends AMPSystem_Data_Item {

    var $datatable = 'userdata';

    function AMP_System_User_Profile( &$dbcon, $id =null ){
        $this->init( $dbcon, $id );
    }

    function getName( ) {
        $fname = $this->getData( 'First_Name');
        $lname = $this->getData( 'Last_Name');
        if ( $fname && $lname ) {
            return $lname . ', ' . $fname;
        }
        if ( $lname ) {
            return $lname;
        }
        if ( $fname ) {
            return $fname;
        }
    }

    function getModin( ) {
        return $this->getData( 'modin');
    }

    function getURL( ) {
        if( !$this->isLive( )) return false; 
        return AMP_SITE_URL . AMP_Url_AddVars( AMP_CONTENT_URL_FORM_DISPLAY, array( 'modin=' . $this->getModin( ), 'uid=' . $this->id ));
    }

    function get_url_edit( ) {
        return AMP_Url_AddVars( AMP_SYSTEM_URL_FORM_ENTRY, array( 'modin=' . $this->getModin( ), 'uid=' . $this->id ) );
    }

}

?>
