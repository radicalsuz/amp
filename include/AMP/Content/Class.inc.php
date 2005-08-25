<?php

require_once ('AMP/System/Data/Item.inc.php' );
require_once ('AMP/Content/Class/Display.inc.php' );
require_once ('AMP/Content/Display/Criteria.inc.php' );

class ContentClass extends AMPSystem_Data_Item {

    var $datatable = "class";
    var $name_field = "class";

    function ContentClass( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getSection() {
        if($section = $this->getData( 'type' )) return $section;
        return AMP_CONTENT_MAP_ROOT_SECTION;
    }

    function &getContents() {
        if (isset($this->_contents)) return $this->_contents;

        $this->_contents = &new ArticleSet( $this->dbcon );
        $this->_contents->addCriteria( 'class='.$this->id );
        $this->_contents->setSort( array( 'date DESC', 'id DESC' ) );

        $criteria_set = new AMPContent_DisplayCriteria();
        $criteria_set->allowClass( $this->id );
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
        if ($id == AMP_CONTENT_MAP_ROOT_SECTION ) return false;
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
