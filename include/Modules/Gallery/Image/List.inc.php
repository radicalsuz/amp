<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'Modules/Gallery/Image/Set.inc.php');

class GalleryImage_List extends AMPSystem_List {
    var $name = 'Photo Gallery Images';
    var $col_headers = array( 'Image' => 'img', 'ID' => 'id', 'Gallery' => 'galleryid', 'Section' => 'section', 'Status' => 'publish');
    var $extra_columns = array( 'Thumb' => 'thumb');
    var $_pager_active = true;
    var $editlink = 'gallery_image.php';
    
    function GalleryImage_List( &$dbcon, $gallery_id = null ){
        $source = &new GalleryImageSet( $dbcon, $gallery_id );
        $this->init( $source );
        $source->addSelectExpression( 'img AS thumb');
        $source->readData( );
        
        $this->addTranslation( 'thumb', '_getThumbDisplay');
        $this->addLookup( 'galleryid', AMPContent_Lookup::instance( 'galleries'));
        $this->addTranslation( 'section', '_getSectionName');
        #$this->addLookup( 'section', AMPContent_Lookup::instance( 'sections'));
        $reg = &AMP_Registry::instance();
        if ($thumb_attr = $reg->getEntry( AMP_REGISTRY_CONTENT_IMAGE_THUMB_ATTRIBUTES )) {
            $this->_thumb_attr = array_merge( $this->_thumb_attr, $thumb_attr );
        } else {
            $this->_thumb_attr['width'] = AMP_IMAGE_WIDTH_THUMB;
        }
    }

    function _getThumbDisplay( $value, $fieldname, $data ) {
        require_once( 'AMP/Content/Image.inc.php');
        $img = &new Content_Image( $data['img']);
        return $this->_HTML_link( AMP_Url_AddVars( $this->editlink, "id=".$data['id']), $this->_HTML_image($img->getURL( AMP_IMAGE_CLASS_THUMB ), $this->_thumb_attr ));
    }

    function _getSectionName( $value, $fieldname, $data ){
        static $names_lookup = false;
        if ( !$names_lookup ) $names_lookup = &AMPContent_Lookup::instance( 'sections');
        if (! ( $value && isset( $names_lookup[$value]) )) return false;
        return $names_lookup[$value];
        
    }


}

?>
