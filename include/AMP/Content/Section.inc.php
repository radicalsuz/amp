<?php

require_once ( 'AMP/System/Data/Item.inc.php' );

class Section extends AMPSystem_Data_Item {

    var $datatable = "articletype";
    var $name_field = "type";

    function Section( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getParent() {
        return $this->getData( 'parent' );
    }

    function getSecured() {
        return $this->getData( 'secure' );
    }

    function getTemplate() {
        return $this->getData( 'templateid' );
    }

    function getStylesheet() {
        return $this->getData( 'css' );
    }

    function getRedirect() {
        if (!$this->getData('uselink')) return false;
        if (!( $target = $this->getData('linkurl'))) return false;
        return $target;
    }

}
?>
