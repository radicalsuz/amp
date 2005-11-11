<?php

require_once( 'AMP/Content/Display/List.inc.php');

class AMPTag_ContentSet extends AMPSystem_Data_Set {

    var $datatable = "tags_contents";
    var $_content_item_types = array( 
        "article"   =>  'Article',
        "section"   =>  'Section',
        "image"     =>  'ContentImage',
        "tag"       =>  'AMPTag',
        "userdata"  =>  'ContentUser' );
    var $_content_item_paths = array( );

    function AMPTag_ContentSet( &$dbcon, $tag_id = null ) {
        $this->init( $dbcon );
        if ( isset( $tag_id )) $this->addCriteriaTag( $tag_id );
    }

    function addCriteriaTag( $tag_id ) {
        $this->addCriteria( "tag_id=".$tag_id);
    }

    function &getItem( ) {
        if ( !($data = $this->getData( ) )) return false;
        return $this->buildItem( $data );
    }
    function &buildItem( $data ) {
        if ( !( $itemclass = $this->_loadItemType( $data['content_type']))) return false;
        return new $itemclass( $this->dbcon, $data['content_foreign_key']);

    }
    function _loadItemType( $type ){
        if ( !isset( $this->_content_item_types[$type]) ) return false;
        if ( class_exists( $this->_content_item_types[ $type ])) return true;

        $load_filename = 'AMP/Content/'.ucfirst( $type ).'.inc.php';
        if ( isset( $this->_content_item_paths[ $type ] )) $load_filename = $this->_content_item_paths[ $type ];
        if ( !file_exists_incpath( $load_filename )) return false;

        include_once( $load_filename );
        return $this->_content_item_paths[$type];

    }

}

?>
