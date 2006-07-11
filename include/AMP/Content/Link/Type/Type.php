<?php

require_once( 'AMP/System/Data/Item.inc.php');

class Link_Type extends AMPSystem_Data_Item {

    var $datatable = "linktype";
    var $name_field = "name";
    var $_class_name = 'Link_Type';
    var $_tree;

    function Link_Type ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getParent( ){
        return $this->getData( 'parent' );
    }

    function getListOrder( ){
        $result = $this->getData( 'listorder' );
        if ( !$result ) return AMP_CONTENT_LISTORDER_MAX;
        return $result;
    }

    function getLinks( $source = null ){
        if ( !isset( $source ) ){
            require_once( 'AMP/Content/Link/Link.php');
            $source = &new AMP_Content_Link( AMP_Registry::getDbcon( ));
        }
        $source_set = $source->search( $source->makeCriteria( array( 'linkType' => $this->id ) ));
        
    }

    function getOrder( ){
        return $this->getData( 'listorder' );

    }
    function reorder( $new_order_value ){
        if ( $new_order_value == $this->getOrder( )) return false;
        $this->mergeData( array( 'listorder' => $new_order_value ));
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update');
        $this->notify( 'reorder');
        return $result;
    }

    function _sort_default( &$item_set ) {
        $this->sort( $item_set, 'listOrder' );
        /*
        $order = array_keys( AMPContent_Lookup::instance( 'linkTypeMap') );
        $item_set = array_combine_key( $order, $item_set );
        */
        
    }

}

?>
