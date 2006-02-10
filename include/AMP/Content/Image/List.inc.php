<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/System/File/File.php');
require_once( 'AMP/Content/Page/Urls.inc.php');

class AMP_Content_Image_List extends AMPSystem_List {
    var $_path_files;
    #var $suppress = array( 'header'=>true , 'sortlinks' => true, 'editcolumn' =>true );
    var $suppress = array( 'header'=>true , 'solinks' => true, 'editcolumn' =>true );
    var $col_headers = array( 
        'Image' => '_makeThumb',
        'File Name' => 'name',
        'Date Uploaded' => 'itemDate',
        'Galleries' => '_makeGalleryList');

    var $_source_counter = 0;
    var $_source_keys;

    var $_thumb_attr;
    var $_pager_active = true;
    var $editlink = AMP_SYSTEM_URL_IMAGE_UPLOAD;

    function AMP_Content_Image_List( ){
        $this->_path_files = AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . AMP_CONTENT_URL_IMAGES . "original/";
        $fileSource = &new AMP_System_File( );
        $source = &$fileSource->search( $this->_path_files );
        $this->init( $source );
        $this->_initThumbAttrs( );
    }

    function _initThumbAttrs( ) {
        $reg = &AMP_Registry::instance();
        if ($thumb_attr = $reg->getEntry( AMP_REGISTRY_CONTENT_IMAGE_THUMB_ATTRIBUTES )) {
            $this->_thumb_attr = array_merge( $this->_thumb_attr, $thumb_attr );
        } else {
            $this->_thumb_attr['width'] = AMP_IMAGE_WIDTH_THUMB;
        }
    }

    function init( &$source ){
        $this->source = &$source;
        $this->_activatePager( );
        $this->_prepareData();
        $this->_source_keys = array_keys( $this->source );
    }
    function _HTML_sortLink( $fieldname ) {
        return '#';
    }

    function _prepareData( ){
        $this->_source_counter = 0;
        return !( empty( $this->source ));
    }

    function _getSourceRow( ){
        if ( !isset( $this->_source_keys[$this->_source_counter ])) return false;
        $row_data = array( );
        $row_data_source = &$this->source[ $this->_source_keys[ $this->_source_counter ]];
        foreach( $this->col_headers as $column ){
            $row_data[$column] = $this->_getSourceDataItem( $column, $row_data_source );
        }
        $row_data['id'] = $row_data['name'];
        #print join( " ## " ,  array_keys( $row_data )) . $this->_HTML_newline( );
        ++$this->_source_counter;
        return $row_data;
    }

    function _getSourceDataItem( $column, &$row_data_source ){
        if ( method_exists( $this, $column )) 
            return $this->$column( $row_data_source, $column );

        $get_method = 'get'.ucfirst( str_replace( ' ', '', $column ));
        if ( method_exists( $row_data_source, $get_method )) 
            return $row_data_source->$get_method();
        
        return false;
    }

    function _makeThumb( &$source, $column_name ){
        require_once( 'AMP/Content/Image.inc.php');
        $img = &new Content_Image( $source->getName( ));
        return $this->_HTML_link( $img->getURL( AMP_IMAGE_CLASS_ORIGINAL ), $this->_HTML_image($img->getURL( AMP_IMAGE_CLASS_THUMB ), $this->_thumb_attr ));
    }

    function _makeGalleryList( $source, $column_name ) {
        $galleries = &AMPContentLookup_GalleriesByImage::instance( $source->getName( ));
        if ( empty( $galleries )) return false;
        $gallerynames = array_combine_key( $galleries, AMPContent_Lookup::instance( 'galleries'));
        $output = "";
        foreach( $galleries as $galleryImage_id => $gallery_id ){
            if ( !isset( $gallerynames[ $gallery_id])) continue;
            $output .=  $this->_HTML_link( AMP_Url_AddVars( AMP_SYSTEM_URL_GALLERY_IMAGE, 'id='.$galleryImage_id), 
                                            AMP_trimText( $gallerynames[ $gallery_id ], 30))
                        . $this->_HTML_newline( );
        }
        return $output;

    }


}

?>
