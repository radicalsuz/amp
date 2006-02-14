<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/System/File/File.php');
require_once( 'AMP/Content/Page/Urls.inc.php');
require_once( 'AMP/Content/Image/Observer.inc.php');

if ( !defined( 'AMP_TEXT_MODULE_NAME_GALLERY')) define( 'AMP_TEXT_MODULE_NAME_GALLERY', 'Photo Gallery');

class AMP_Content_Image_List extends AMPSystem_List {
    var $_path_files;
    var $suppress = array( 'header'=>true , 'editcolumn' =>true );
    var $col_headers = array( 
        'Image' => '_makeThumb',
        'File Name' => 'name',
        'Date Uploaded' => 'time',
        'Galleries' => 'galleryLinks');

    var $extra_columns;

    var $_source_counter = 0;
    var $_source_keys;

    var $_thumb_attr;
    var $_pager_active = true;
    var $editlink = AMP_SYSTEM_URL_IMAGE_UPLOAD;

    var $_sort;
    var $_observers_source = array( 'AMP_Content_Image_Observer' );

    function AMP_Content_Image_List( ) {
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

    function _after_init( ){
        $this->_source_keys = array_keys( $this->source );
        $this->addTranslation( 'time', '_makePrettyDate');
        $this->_initObservers( );
    }

    function _initObservers( ){
        foreach( $this->_observers_source as $observer_class ){
            $observer = &new $observer_class( );
            $observer->attach( $this->source );
        }
    }
    
    function _HTML_sortLink( $fieldname ) {
        if (isset($this->suppress['sortlinks']) && $this->suppress['sortlinks']) return "";
        $new_sort = $fieldname;
        $url_criteria = $this->_prepURLCriteria();
        $url_criteria[] = "sort=".$new_sort;
        if ($fieldname == $this->_sort ) $url_criteria[] = "sort_direction= DESC";
        return AMP_Url_AddVars( $_SERVER['PHP_SELF'], $url_criteria );
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

    function _makeThumb( &$source, $column_name ) {
        require_once( 'AMP/Content/Image.inc.php');
        $img = &new Content_Image( $source->getName( ));
        return $this->_HTML_link( $img->getURL( AMP_IMAGE_CLASS_ORIGINAL ), $this->_HTML_image($img->getURL( AMP_IMAGE_CLASS_THUMB ), $this->_thumb_attr ));
    }

    function galleryLinks( $source, $column_name ) {
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

    function _setSort() {
        //Sort the data
        if (isset($_REQUEST['sort']) && $_REQUEST['sort']) { 
            $local_sort_method = '_setSort'.ucfirst( $_REQUEST['sort']);
            $sort_direction = ( isset( $_REQUEST['sort_direction']) && $_REQUEST['sort_direction']) ?
                                $_REQUEST['sort_direction'] : false;

            if ( method_exists( $this, $local_sort_method)) return $this->$local_sort_method( $sort_direction );

            $fileSource = &new AMP_System_File( );
            if( $fileSource->sort( $this->source, $_REQUEST['sort'], $sort_direction )){
                $this->_sort = $_REQUEST['sort'];
            }
        }
    }

    function _setSortGalleryLinks( $sort_direction ){
        ampredirect( AMP_Url_AddVars( AMP_SYSTEM_URL_GALLERY_IMAGES, 'sort=galleryid'));
    }

}

?>
