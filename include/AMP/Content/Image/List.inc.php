<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/System/File/Image.php');
require_once( 'AMP/Content/Page/Urls.inc.php');
require_once( 'AMP/Content/Image/Observer.inc.php');

class AMP_Content_Image_List extends AMP_System_List_Form {
    var $_path_files;
    var $suppress = array( 'header'=>true , 'editcolumn' =>true );
    var $col_headers = array( 
        'Image'         => '_makeThumb',
        'File Name'     => 'name',
        'Date Uploaded' => 'time',
        'Galleries'     => 'galleryLinks',
        'Crop'          => 'cropLink'
        );

    var $extra_columns;

    var $_source_object = 'AMP_System_File_Image';

    var $_thumb_attr;
    var $_pager_active = true;
    //var $editlink = AMP_SYSTEM_URL_IMAGE_UPLOAD;
    var $_url_add = AMP_SYSTEM_URL_IMAGE_UPLOAD;

    var $_observers_source = array( 'AMP_Content_Image_Observer' );
    var $_actions = array( 'delete', 'gallery' );
    var $_action_args = array( 
        'gallery' => array( 'gallery_id' )
        );

    function AMP_Content_Image_List( ) {
        $this->_path_files = AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . AMP_CONTENT_URL_IMAGES . "original/";
        $listSource = &new $this->_source_object( );
        $source = &$listSource->search( $this->_path_files );
        $this->init( $source );
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
        $this->addTranslation( 'time', '_makePrettyDate');
        $this->_initThumbAttrs( );
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

    function cropLink( &$source, $column_name ){
        $renderer = &$this->_getRenderer( );
        return $renderer->link( 
                AMP_Url_AddVars( AMP_SYSTEM_URL_IMAGES, array( 'action=crop', 'id='.$source->getName( ))),
                'Crop' );
    }

    function _setSortGalleryLinks( $sort_direction ){
        ampredirect( AMP_Url_AddVars( AMP_SYSTEM_URL_GALLERY_IMAGES, 'sort=galleryid'));
    }

    function _getSourceRow( ){
        $row_data = PARENT::_getSourceRow( );
        if ( $row_data ) $row_data['id'] = $row_data['name'];
        return $row_data;
    }

    function renderGallery( &$toolbar ){
        $renderer = &$this->_getRenderer( );
        $gallery_options = &AMPContent_Lookup::instance( 'galleries' );
        $gallery_options = array( '' => 'Select Gallery') + $gallery_options;
                
        $toolbar->addEndContent( 
                $renderer->inDiv( 
                        '<a name="gallery_targeting"></a>'
                        . AMP_buildSelect( 'gallery_id', $gallery_options, null, $renderer->makeAttributes( array( 'class' => 'searchform_element')))
                        . '&nbsp;'
                        . $toolbar->renderDefault( 'gallery')
                        . '&nbsp;'
                        . "<input type='button' name='hideGallery' value='Cancel' onclick='window.change_any( \"gallery_targeting\");'>&nbsp;",
                        array( 
                            'class' => 'AMPComponent_hidden', 
                            'id' => 'gallery_targeting')
                    ), 'gallery_targeting');

        return "<input type='button' name='showGallery' value='Gallery' onclick='window.change_any( \"gallery_targeting\");window.scrollTo( 0, document.anchors[\"gallery_targeting\"].y );'>&nbsp;";

    }

}

?>
