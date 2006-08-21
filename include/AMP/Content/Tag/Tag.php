<?php
require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_Tag extends AMPSystem_Data_Item {
    var $datatable = "tags";
    var $name_field = "name";

    var $_generator;
    //var $_contents_criteria = array( );
    //var $_contents_class = 'ArticleSet';
    
    function AMPTag( &$dbcon, $id = null ){
        $this->init( $dbcon, $id );
    }

    function getBlurb( ){
        return $this->getData( 'description');
    }

    function getImageFilename( ){
        return $this->getData( 'image');
    }

    function &getImageRef() {
        if (! ($img_path = $this->getImageFileName())) return false;
        require_once( 'AMP/Content/Image.inc.php');
        $image = &new Content_Image( $img_path );
        return $image;
    }

    function getItemDate() {
        //interface
        return false;
    }
    function getTitle () {
        return $this->getName();
    }

/*
    function display() {
        $display = &$this->getDisplay();
        return $display->execute();
    }

    function &getDisplay() {
        require_once( 'AMP/Content/Tag/Display.inc.php');
        return new AMP_Content_Tag_Display( $this );
    }


    function &getContents() {
        if (isset($this->_contents)) return $this->_contents;

        $this->_contents = &new AMPTag_ContentSet( $this->dbcon, $this->id );
        $this->_contents->filter( "live" );

        return $this->_contents;
    }

    function addContentsCriteria( $criteria ) {
        if ( array_search( $criteria, $this->_contents_criteria ) !== FALSE ) return true;
        $this->_contents_criteria[] = $criteria;
    }
*/


}
?>
