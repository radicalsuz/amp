<?php

require_once ( 'AMP/System/Data/Item.inc.php' );

class Article extends AMPSystem_Data_Item {

    var $datatable = "articles";
    var $name_field = "title";

    function Article( &$dbcon, $id = null ) {
        $this->init ($dbcon, $id);
    }

    function getParent() {
        return $this->getData( 'type' );
    }

    function getSection() {
        return $this->getParent();
    }
}
?>
