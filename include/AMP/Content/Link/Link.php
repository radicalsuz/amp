<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_Link extends AMPSystem_Data_Item {

    var $datatable = "links";
    var $name_field = "linkname";
    var $_class_name = 'AMP_Content_Link';

    function AMP_Content_Link ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
        $this->_addAllowedKey( 'reltype');
    }

    function _afterRead( ){
        $reltype = &AMPContentLookup_SectionsByLink::instance( $this->id ) ;
        if ( !$reltype ) return;
        $this->mergeData( array( 'reltype' => array_keys( $reltype)) );
    }

    function _afterSave( ){
        $reltype = $this->getData( 'reltype');
        $this->_removeRelatedLinks( );
        $this->_saveRelatedLinks( $reltype );
    }

    function _removeRelatedLinks( ){
        $set = &$this->_getReltypeSource( );
        $set->deleteData( $this->_makeCriteriaReltypes( ));
    }

    function &_getReltypeSource( ){
        require_once( 'AMP/System/Data/Set.inc.php');
        $set = &new AMPSystem_Data_Set( $this->dbcon );
        $set->setSource( 'linksreltype');
        return $set;
    }

    function _makeCriteriaReltypes( ){
        return 'linkid=' . $this->id;
    }

    function _saveRelatedLinks( $link_values ){
        foreach( $link_values as $section_id ){
            $set = &$this->_getReltypeSource( );
            $set->insertData( array( 'linkid' => $this->id, 'typeid' => $section_id ));
        }
    }
    

    function getUrl( ){
        return $this->getData( 'url');
    }

    function getOrder( ){
        return $this->getData( 'linkorder');
    }

    function reorder( $new_order_value ){
        if ( $new_order_value == $this->getOrder( )) return false;
        $this->mergeData( array( 'linkorder' => $new_order_value ));
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update');
        $this->notify( 'reorder');
        return $result;

    }

    function &getImageRef( ){
        if (! ($img_path = $this->getImageFileName())) return false;
        require_once( 'AMP/Content/Image.inc.php');
        $image = &new Content_Image();
        return $image;
    }
    function getImageFileName() {
        return $this->getData( 'image' );
    }

    function getBlurb( ){
        $value = $this->getData( 'description');
        if ( '&nbsp;' == $value ) return false;
        return $value;
    }

    function getLinkType( ){
        return $this->getData( 'linktype');
    }

    function getLinkTypeName( ){
        if ( !( $result = $this->getLinkType( ))) return false;
        $typenames = &AMPContent_Lookup::instance( 'linkTypes');
        if ( isset( $typenames[$result])) return $typenames[ $result ];
        return false;
    }

    function _sort_default( &$item_set ){
        return $this->sort( $item_set, 'name');
#        $this->sort( $item_set, 'order');
#        return $this->sort( $item_set, 'linkTypeName');
    }

    function _makeCriteriaLinkType( $value ){
        if ( !is_numeric( $value )) return false;
        return 'type=' . $value ;
    }

    function getTitle( ){
        return $this->getName( );
    }

    
}


?>
