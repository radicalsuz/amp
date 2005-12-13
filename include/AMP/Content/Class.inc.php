<?php

require_once ('AMP/System/Data/Item.inc.php' );
require_once ('AMP/Content/Class/Display.inc.php' );
require_once ('AMP/Content/Display/Criteria.inc.php' );

define( 'AMP_CONTENT_CLASSLIST_DISPLAY_DEFAULT', 'ContentClass_Display');
define( 'AMP_CONTENT_CLASSLIST_DISPLAY_BLOG', 'ContentClass_Display_Blog');

class ContentClass extends AMPSystem_Data_Item {

    var $datatable = "class";
    var $name_field = "class";
    var $_contents_criteria = array();

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
        foreach ($this->_contents_criteria as $criteria ) {
            $this->_contents->addCriteria( $criteria );
        }


        $criteria_set = &new AMPContent_DisplayCriteria();
        $criteria_set->allowClass( $this->id );
        $criteria_set->clean( $this->_contents );

        return $this->_contents;
    }

    function addContentsCriteria( $criteria ) {
        if ( array_search( $criteria, $this->_contents_criteria ) !== FALSE ) return true;
        $this->_contents_criteria[] = $criteria;
    }

    function &getDisplay() {
        $classes = filterConstants( 'AMP_CONTENT_CLASS' );
        $display_def_constant= 'AMP_CONTENT_CLASSLIST_DISPLAY_' . array_search( $this->id , $classes );
        include_once( 'AMP/Content/Class/Display_Blog.inc.php');

        $display_class = AMP_CONTENT_CLASSLIST_DISPLAY_DEFAULT;
        if (defined( $display_def_constant )) $display_class = constant( $display_def_constant );

        if (!class_exists( $display_class )) $display_class = AMP_CONTENT_CLASSLIST_DISPLAY_DEFAULT;
        return new $display_class( $this );
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
