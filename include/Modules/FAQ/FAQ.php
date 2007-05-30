<?php

require_once( 'AMP/System/Data/Item.inc.php');

class FAQ extends AMPSystem_Data_Item {

    var $datatable = "faq";
    var $name_field = "question";

    function FAQ ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getType( ){
        return $this->getData( 'typeid' );
    }

    function getURL( ) {
        if ( !( isset( $this->id ) && $this->id )) {
            return AMP_CONTENT_URL_FAQ;
        }
        return AMP_url_update( AMP_CONTENT_URL_FAQ, array( 'id' => $this->id ));
    }

    function get_url_edit( ) {
        if ( !( isset( $this->id ) && $this->id )) {
            return AMP_SYSTEM_URL_FAQ;
        }
        return AMP_url_update( AMP_SYSTEM_URL_FAQ, array( 'id' => $this->id ));

    }

    function getStatus( ) {
        return $this->getData( 'publish') ? AMP_PUBLISH_STATUS_LIVE : AMP_PUBLISH_STATUS_DRAFT;
    }
}

?>
