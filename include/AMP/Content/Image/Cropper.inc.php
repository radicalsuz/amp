<?php
define( 'AMP_ERROR_IMAGE_SELECTION_OUT_OF_RANGE', 'The selection has to be completely on the image.');
define( 'AMP_TEXT_CROP', 'Crop');
define( 'AMP_TEXT_PREVIEW', 'Preview');
define( 'AMP_TEXT_CANCEL', 'Cancel');

require_once( 'AMP/Content/Image.inc.php');

/**
 * AMP_Content_Image_Cropper 
 *
 * crops an image file
 * Based on 1 2 Crop Image script by Roel Meurders <scripts@roelmeurders.com>
 * which itself has long credits for Thomas Brattli's work showcased on DHTMLCentral.com'
 * 
 * @uses Content_Image
 * @package 
 * @version 3.5.8
 * @copyright 2006 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMP_Content_Image_Cropper extends AMP_Content_Image {


    var $_gd_version = 2;
    var $_spacer = AMP_ICON_SPACER; 

    function AMP_Content_Image_Cropper( $filename = null ){
        if ( isset( $filename )) $this->setFile( $filename );
    }

    function getImageClass( ){
        return AMP_IMAGE_CLASS_ORIGINAL;
    }
}

class AMP_Content_Image_Crop_Display {
    var $_image;

    var $_original_height;
    var $_original_width;

    var $_display_height;
    var $_display_width;

    var $_display_height_limit =   2000;
    var $_display_width_limit  =   2000;
    var $_display_ratio = 1;

    var $_renderer;
    var $_script_file = '/scripts/12cropimage.js';

    var $_interface_controls = array( 
        'cropimage'     =>  array( 
            'type'  => 'button',
            'label' => AMP_TEXT_CROP,
            'attr'  => array( 'onclick="CropInterface.Check( \'def\');"')),
        'preview'       =>  array( 
            'type'  => 'button',
            'label' => AMP_TEXT_PREVIEW,
            'attr'  => array( 'onclick="CropInterface.Check( \'pre\');"')),
        'bigger'        =>  array( 
            'type'  => 'button',
            'label' => '+',
            'attr'  => array( 'onclick="CropInterface.Zoom( \'in\');"')),
        'smaller'       =>  array( 
            'type'  => 'button',
            'label' => '-',
            'attr'  => array( 'onclick="CropInterface.Zoom( \'out\');"')),
        'closewindow'   =>  array( )AMP_TEXT_CANCEL,
            'type'  => 'button',
            'label' => AMP_TEXT_CANCEL,
            'attr'  => array( 'onclick="top.close();"'));
  #      'selectioninpicture'    => AMP_ERROR_IMAGE_SELECTION_OUT_OF_RANGE );

    var $_crop_action;
    var $_allowed_actions = array( 'crop', 'preview', 'def' );

    var $_crop_width    = AMP_IMAGE_WIDTH_THUMB;
    var $_crop_height   = AMP_IMAGE_WIDTH_THUMB;

    var $crop_start_x;
    var $crop_start_y;

    var $_crop_jpeg_quality = 80;
    var $_target_page = 'crop_image2.php';

    function AMP_Content_Image_Crop_Display( &$image ){
        $this->_image = &$image;
        $this->_initDisplaySize( );
        $this->_initRenderer( );
    }

    function _initRenderer( ){
        require_once( 'AMP/Display/HTML.inc.php');
        $this->_renderer = new AMPDisplay_HTML( );
    }

    function _initDisplaySize( ) {
        if (   $this->_image->getHeight( ) <= $this->_display_height_limit 
            && $this->_image->getWidth( )  <= $this->_display_width_limit ) {
            $this->setDisplayHeight( $this->_image->getHeight( ));
            $this->setDisplayWidth( $this->_image->getWidth( ));
            return;
        }
        if ( $this->_image->isTall( )){
            $this->setDisplayHeight( $this->_display_height_limit );
            $this->_display_ratio = $this->_display_height / $this->_image->height;
            $this->setDisplayWidth(     $this->_image->width * 
                                    (   $this->_display_height / $this->_image->height )) ;
        } else {
            $this->setDisplayWidth( $this->_display_width_limit );
            $this->_display_ratio = $this->_display_width / $this->_image->width;
            $this->setDisplayHeight( $this->_image->height *
                                   ( $this->_display_width / $this->_image->width ));

        }
    }

    function setDisplayHeight( $height ){
        $this->_display_height = $height;
    }

    function setDisplayWidth( $width ){
        $this->_display_width  = $width;
    }

    function execute( ){
        $action_method = 'commit' . ucfirst( $this->_crop_action );
        if ( !method_exists( $this, $action_method )) $action_method = 'commitDefault';
        return $this->$action_method( );
    }

    function commitDefault( ){
        $this->setupOnLoad( );
        $this->declareCSS( );
        return $this->renderInterface( );

    }

    function commitCrop( ){
        $target_image = &new Content_Image( $this->_image->getName( ) );
        $target_path  = $target_image->getPath( AMP_IMAGE_CLASS_CROP );
        $sizes = array( 
            'start_x' => $this->crop_start_x,
            'start_y' => $this->crop_start_y,
            'width'   => $this->_crop_width,
            'height'  => $this->_crop_height );
        $real_sizes = &$this->_resize_ratio( $sizes, $this->_display_ratio );

        $new_image = &$this->_image->crop( $real_sizes['start_x'], $real_sizes['start_y'], $real_sizes['width'], $real_sizes['height']);
        $this->_image->write_image_resource( $new_image, $target_path );
        
        $cropped_image = &new AMP_System_File_Image( $target_path );
        $target_path = $target_image->getPath( AMP_IMAGE_CLASS_THUMBNAIL );

        $thumb_ratio = AMP_IMAGE_WIDTH_THUMB / $cropped_image->width;
        $thumb_sizes = $this->_resize_ratio( 
                            array( 'height' => $cropped_image->height,
                                    'width' => $cropped_image->width ), 
                            $thumb_ratio );

        $thumb_image = &$cropped_image->resize( $thumb_sizes['width'], $thumb_sizes['height'] );
        $cropped_image->write_image_resource( $thumb_image, $target_path );
    }

    function _resizeRatios( $original_sizes, $ratio ){
        foreach( $original_sizes as $key => $size ){
            $result_sizes[$key] = ceil( $size * $ratio );
        }
        return $result_sizes;
    }

    function setupOnLoad( ){
        $header = & AMP_getHeader( );
        $header->addJavascriptOnLoad( 'libinit;' );
        $header->addJavascript( $this->_script_file, 'crop' );
    }

    function declareCSS( ){
        $css = "body{font-family:arial,helvetica; font-size:12px}\n";
        $css .= "#cropDiv{  \n"
                ."   position:absolute;\n"
                ."   left:11px; top:11px; \n"
                ."   width:" . intval( $this->_crop_width /$this->_display_ratio )."px;\n"
                ."   height:" . intval( $this->_crop_height / $this->_display_ratio )."px;\n "
                ."   z-index:2; \n"
                ."   background-image: url(".AMP_CONTENT_URL_IMAGES.AMP_ICON_SPACER.");\n"
                ."}\n";
        $page_header = &AMP_getHeader( );
        $page_header->addStylesheetDynamic( $css, 'cropper');
    }

    function renderInterface( ){
        $output = $this->_renderer->_HTML_image( $this->_displayImageURL( ), array( 'border' => 1 ));
        $output .= $this->_renderer->_HTML_newline( 2 );
        $output .= $this->_renderControls( );
        $output .= $this->_renderCropDiv( );
        return $output;
    }

    function _renderControls( ){
        $form = &new AMPForm( );
        $form->addFields( $this->_interface_controls );
        $form->Build( );
        return $form->output( );
    }

    function _renderCropDiv( ){
        $table_html = 
            '<table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">'."\n"
            . '        <tr><td>'."\n"
            . $this->_renderer->_HTML_image( AMP_CONTENT_URL_IMAGES . AMP_ICON_SPACER )
            . '</td></tr></table>';
        return $this->_renderer->_HTML_div( $table_html, array( 'id' => 'cropDiv'));
    }

    function setCropAction( $action ){
        if ( !$this->_allowed( $action )) return false;
        $this->_crop_action = $action;
    }

    function _allowed( $action ){
        return ( array_search( $action, $this->_allowed_actions ) !== FALSE );
    }

    function setCropHeight( $height ){
        $this->_crop_height = $height;
    }

    function setCropWidth( $width ){
        $this->_crop_width = $width;
    }

    function _displayImageURL( ){
        return AMP_SITE_URL . str_replace( AMP_LOCAL_PATH, "", $this->_image->getPath( ));
    }

    function _renderPreviewInterface( ){
        return $this->_renderer->_HTML_link( 
                    $this->_renderer->_HTML_image( $this->_displayCropPreviewURL( ), array( 'border' => 0)),
                    "#", array( 'onClick'=> $this->_crop_script . '.closePreview( );')) ;
            
    }

}

?>
