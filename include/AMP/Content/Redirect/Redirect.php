<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_Redirect extends AMPSystem_Data_Item {

    var $datatable = "redirect";
    var $name_field = "old";

    function AMP_Content_Redirect ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getAlias( ){
        return $this->getData( 'old');
    }

    function getTarget( ){
        return $this->getData( 'new');
    }

    function getPublish( ){
        return $this->getData( 'publish');
    }
}

?>
