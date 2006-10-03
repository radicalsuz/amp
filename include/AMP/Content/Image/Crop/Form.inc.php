<?php

require_once( 'AMP/Content/Image.inc.php');
require_once( 'AMP/System/File/Image.php');
require_once( 'AMP/System/Form/XML.inc.php' );

/**
 * AMP_Content_Image_Crop_Form
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

class AMP_Content_Image_Crop_Form extends AMPSystem_Form_XML {
    var $_image;

    var $_original_height;
    var $_original_width;

    var $_display_height;
    var $_display_width;

    var $_display_height_limit =   2000;
    var $_display_width_limit  =   2000;
    var $_display_ratio = 1;

    var $_renderer;
    //var $_script_file = '/scripts/12cropimage.js';
    // this code applies to non-implemented cropper based on prototype
    var $_script_file = '/scripts/cropper.js';
    
    var $_window_x = 220;
    var $_window_y = 170;
	var $xml_pathtype = "crop_fields";

    var $submit_button = array( 'submitCropAction' => array(
        'type' => 'group',
        'elements'=> array(
            'crop' => array(
                'type' => 'submit',
                'label' => 'Crop',
                /*
                'attr' => array ( 
                    'onclick' => 
                //    "return window.cropper.Check('def', 'AMP_Content_Image_Crop_Form');" ),
                    */
                ),
            'cancel' => array(
                'type' => 'submit',
                'label' => 'Cancel'),
            )
    ));

    var $_crop_width    = AMP_IMAGE_WIDTH_THUMB;
    var $_crop_height   = AMP_IMAGE_WIDTH_THUMB;

    function AMP_Content_Image_Crop_Form( &$image ){
        $this->_image = &$image;
        $this->_initDisplaySize( );
        $this->_initRenderer( );
        $this->init( 'AMP_Content_Image_Crop_Form', 'POST', AMP_SYSTEM_URL_IMAGES );
    }

    function _initRenderer( ){
        require_once( 'AMP/Content/Display/HTML.inc.php');
        $this->_renderer = & new AMPDisplay_HTML( );
    }

    function _initDisplaySize( ) {
        if (   $this->_image->height <= $this->_display_height_limit 
            && $this->_image->width  <= $this->_display_width_limit ) {
            $this->setDisplayHeight( $this->_image->height );
            $this->setDisplayWidth( $this->_image->width );
            return;
        }
        if ( $this->_image->height > $this->_image->width ){
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

    function adjustFields( $fields ){
        $interface_html = $this->renderInterface( );
        $fields['crop_interface']['default'] = $interface_html ;
        //$fields['crop_interface_options']['default'] = $this->_renderOptions( );
        return $fields;
    }

    function _selectAddNull( $valueset, $fieldname ){
        if ( $fieldname != 'target' ) return parent::_selectAddNull( $valueset, $fieldname );
        return $valueset;
    }


    function getDisplayRatio( ){
        return $this->_display_ratio;
    }

    function _initJavascriptActions( ){
        $header = & AMP_getHeader( );
        //$header->addJavascriptOnLoad( 'window.cropper = new CropInterface( "cropDiv" );' );
        //$header->addJavascriptOnLoad( 'window.cropper.setImage( "image_to_crop", "'. $this->_image->getName( ).'" );' );
        $header->addJavaScript( $this->_script_file, 'crop' );
        $this->declareCSS( );

        // this code applies to non-implemented cropper based on prototype
        $header->addJavaScriptDynamic( $this->_scriptEndCrop( ), 'crop_value_commit' );
        $header->addJavaScriptDynamic(  $this->_scriptInit( ) , 'crop_init');
    }


    function declareCSS( ){
        /*
        $css = "body{font-family:arial,helvetica; font-size:12px}\n";
        $css .= "#cropDiv{  \n"
                ."   position:absolute;\n"
                ."   left:". $this->_window_x ."px; top:".$this->_window_y."px; \n"
                ."   width:" . intval( $this->_crop_width /$this->_display_ratio )."px;\n"
                ."   height:" . intval( $this->_crop_height / $this->_display_ratio )."px;\n "
                ."   z-index:2; \n"
                ."   background-image: url(".AMP_SYSTEM_URL_SYSTEM_IMAGES.AMP_ICON_SPACER.");\n"
                ."}\n";
                */
        $page_header = &AMP_getHeader( );
//        $page_header->addStylesheetDynamic( $css, 'cropper');

    // this code applies to non-implemented cropper based on prototype
        $page_header = &AMP_getHeader( );
        #$page_header->addStylesheet( '/cropper.css', 'cropper');

    }

    function renderInterface( ){
        $output =   $this->_renderer->inDiv(  
                    $this->_renderer->image( $this->_displayImageURL( ), array( 'id' => 'image_to_crop', 'name' => 'image_to_crop' ))
                    );
        #$output .= $this->_renderCropDiv( );
        #$output .= $this->_renderer->newline( 2 );
        #$output .= $this->_renderControls( );
        return $output;
    }

    function submitted( ){
        if ( !isset( $_REQUEST['submitCropAction'] )) return false;
        $submitAction = $_REQUEST['submitCropAction'];
        if (!is_array($submitAction)) return false;

        $key = key($submitAction);
        if (isset($this->submit_button['submitCropAction']['elements'][$key])) return $key;

        return false;
    }

    function _renderControls( ){
        $stuff = AMP_TEXT_SIZE . ': ' 
                 . '<input name="bigger" value="+" type="button" onMouseDown="window.cropper.Bigger( );" onMouseUp="window.cropper.Stop( );" onMouseOut="window.cropper.Stop( );">&nbsp;'
                 . '<input name="smaller" value="-" type="button" onMouseDown="window.cropper.Smaller( );" onMouseUp="window.cropper.Stop( );" onMouseOut="window.cropper.Stop( );">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                 . '<input name="preview" value="'.AMP_TEXT_PREVIEW.'" type="button" onClick="window.cropper.Check( \'pre\');"><br />';
        return $stuff;
    }

    function _renderOptions( ){
           return  '<input name="wider" value="+" type="button" onMouseDown="window.cropper.Wider( );" onMouseUp="window.cropper.Stop( );" onMouseOut="window.cropper.Stop( );">&nbsp;'
                 . '<input name="thinner" value="-" type="button" onMouseDown="window.cropper.Thinner( );" onMouseUp="window.cropper.Stop( );" onMouseOut="window.cropper.Stop( );">&nbsp;&nbsp;'
                 . ': ' . AMP_TEXT_WIDTH . '<br /> ' 
                 . '<input name="taller" value="+" type="button" onMouseDown="window.cropper.Taller( );" onMouseUp="window.cropper.Stop( );" onMouseOut="window.cropper.Stop( );">&nbsp;'
                 . '<input name="shorter" value="-" type="button" onMouseDown="window.cropper.Shorter( );" onMouseUp="window.cropper.Stop( );" onMouseOut="window.cropper.Stop( );">&nbsp;&nbsp;'
                 . ': ' . AMP_TEXT_HEIGHT . '<br /> ' ;

    }

    function _renderCropDiv( ){
        $table_html = 
            '<table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">'."\n"
            . '        <tr><td>'."\n"
            . $this->_renderer->image( AMP_SYSTEM_URL_SYSTEM_IMAGES . AMP_ICON_SPACER )
            . '</td></tr></table>';
        return $this->_renderer->inDiv( $table_html, array( 'id' => 'cropDiv'));
    }

    // this function applies to non-implemented cropper based on prototype
    function _scriptEndCrop( ){
        return 
        "function onEndCrop( coords, dimensions ) {
            $( 'x1' ).value = coords.x1;
            $( 'y1' ).value = coords.y1;
            $( 'width' ).value = dimensions.width;
            $( 'height' ).value = dimensions.height;
        }";
    }

    // this function applies to non-implemented cropper based on prototype
    function _scriptInit(){
                        //minWidth: ". $this->_crop_width . ", 
                        //minHeight: ". $this->_crop_height . ", 
        return
        "Event.observe( window, 'load', function() { 
                new Cropper.Img( 
                    'image_to_crop', 
                    { 
                        minWidth: ". $this->_crop_width . ", 
                        onEndCrop: onEndCrop,
                        displayOnInit: true, 
                        onloadCoords: { x1: 10, y1: 10, x2: ".( $this->_crop_width+10) . ", y2: ".( $this->_crop_height+10 ) . "}
                    } 
                ) 
            } 
        );";
    }

    function setCropHeight( $height ){
        $this->_crop_height = $height;
    }

    function setCropWidth( $width ){
        $this->_crop_width = $width;
    }

    function _displayImageURL( ){
        $image_url = AMP_Url_AddVars(  AMP_SYSTEM_URL_IMAGE_VIEW, 
                                        array( 'filename='.$this->_image->getName( ),
                                                'class='.AMP_IMAGE_CLASS_ORIGINAL ));
        if ( $this->_display_ratio == 1 ) return $image_url;
        return AMP_Url_AddVars( $image_url, array( 'action=resize', 'height='.$this->_display_height, 'width='.$this->_display_width ));
                                        
    }

}

?>
