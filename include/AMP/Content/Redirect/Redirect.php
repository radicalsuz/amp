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

    function setAlias( $alias_url ){
        return $this->mergeData( array( 'old' => $alias_url ));
    }

    function setTarget( $url ){
        return $this->mergeData( array( 'new' => $url ));
    }

    function setDefaults( ){
        return $this->mergeData( array( 'publish' => 1 ));
    }

    function makeCriteriaAlias( $alias ){
        return 'old =' .$this->dbcon->qstr( $alias );
    }
}

?>
