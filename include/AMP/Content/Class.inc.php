<?php

require_once ('AMP/System/Data/Item.inc.php' );

class ContentClass extends AMPSystem_Data_Item {

    var $datatable = "class";
    var $name_field = "class";

    function ContentClass( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getSection() {
        return $this->getData( 'type' );
    }

    function &getContents() {
        if (isset($this->_contents)) return $this->_contents;

        $this->_contents = &new ArticleSet( $this->dbcon );
        $this->_contents->addCriteria( 'class='.$this->id );

        $criteria_set = new AMPContent_DisplayCriteria();
        $criteria_set->clean( $this->_contents );

        return $this->_contents;
    }

    function &getDisplay() {
        return new ContentClass_Display( $this );
    }

    function display() {
        $display = &$this->getDisplay();
        return $display->execute();
    }

    function &getHeaderRef() {
        if ($id = $this->getHeaderTextId() ) return new Article( $this->dbcon, $id );
        return false;
    }

    function getHeaderTextId() {
        if (!($id =  $this->getData( 'url' ))) return false;
        if ($id === 1) return false;
        return $id;
    }

    function getItemDate() {
        //interface
        return false;
    }

    function getTitle () {
        return $this->getName();
    }

    function getBlurb() {
        return $this->getData( 'description' );
    }
}
?>
