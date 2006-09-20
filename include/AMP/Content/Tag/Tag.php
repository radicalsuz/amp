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
        $empty_value = false;
        if (! ($img_path = $this->getImageFileName())) return $empty_value;
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

    function get_url_edit( ) {
        if ( !isset( $this->id )) return false;
        return AMP_Url_AddVars( AMP_SYSTEM_URL_TAG, array( 'id=' . $this->id ) );
    }

    function getURL( ) {
        if ( !isset( $this->id )) return false;
        return AMP_Url_AddVars( AMP_CONTENT_URL_TAG, array( 'id=' . $this->id ) );
    }

    function makeCriteriaTag( $tag_name ) {
        $key = AMP_Content_Tag::findByName( $tag_name );
        if ( !$key ) return 'FALSE';
        return 'id=' . $key;
    }

    function findByName( $raw_tag_name ) {
        static $simple_tag_lookup = array( );
        if ( empty( $simple_tag_lookup )) {

            //initialize an all-lowercase version of the tags set
            $tag_lookup = AMPSystem_Lookup::instance( 'tags');
            foreach( $tag_lookup as $tag_id => $tag_name ) {
                $simple_tag_lookup[$tag_id] = strtolower( $tag_name );
            }
        }
        
        $tag_name = strtolower( trim( $raw_tag_name ));
        return array_search( $tag_name, $simple_tag_lookup );
    }

    function create( $tag_name, $description = false ) {
        if ( !trim( $tag_name )) return false;
        $existing_id = AMP_Content_Tag::findByName( $tag_name );

        if ( $existing_id ) return $existing_id;
        return AMP_Content_Tag::_create( trim( $tag_name ));
    }

    function _create( $tag_name, $description = false ) {
        $tag = &new AMP_Content_Tag( AMP_Registry::getDbcon( ));
        $tag->setDefaults( );
        $tag->setData( array( 'name' => $tag_name , 'description' => $description ));
        $result = $tag->save( );
        if ( !$result ) return false;
        return $tag->id;

    }

    function setDefaults( ) {
        $this->mergeData( array( 
            'publish' => 1
            ));

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
