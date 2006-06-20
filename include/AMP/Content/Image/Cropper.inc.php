<?php

require_once( 'AMP/Content/Image.inc.php');
require_once( 'AMP/System/File/Image.php');
require_once( 'AMP/System/Form.inc.php' );

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

class AMP_Content_Image_Crop_Form extends AMPSystem_Form {
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
    var $_window_x = 220;
    var $_window_y = 170;

    var $_interface_controls = array( 
        'cropimage'     =>  array( 
            'type'  => 'button',
            'label' => AMP_TEXT_CROP,
            //'attr'  => array( 'onclick="CropInterface.Check( \'def\');"')),
            'attr'  => array( 'onclick="cropCheck( \'def\');"')),
            /*
        'preview'       =>  array( 
            'type'  => 'button',
            'label' => AMP_TEXT_PREVIEW,
            'attr'  => array( 'onclick="CropInterface.Check( \'pre\');"')),
            */
        'bigger'        =>  array( 
            'type'  => 'button',
            'label' => '+',
            //'attr'  => array( 'onclick="CropInterface.Zoom( \'in\');"')),
            'attr'  => array( 'onclick="cropZoom( \'in\');"')),
        'smaller'       =>  array( 
            'type'  => 'button',
            'label' => '-',
            //'attr'  => array( 'onclick="CropInterface.Zoom( \'out\');"')),
            'attr'  => array( 'onclick="cropZoom( \'out\');"')),
        'closewindow'   =>  array( 
            'type'  => 'button',
            'label' => AMP_TEXT_CANCEL,
            'attr'  => array( 'onclick="top.close();"'))
            );
  #      'selectioninpicture'    => AMP_ERROR_IMAGE_SELECTION_OUT_OF_RANGE );
    var $submit_button = array( 'submitCropAction' => array(
        'type' => 'group',
        'elements'=> array(
            'crop' => array(
                'type' => 'submit',
                'label' => 'Crop',
                'attr' => array ( 
                    'onclick' => 
                    "return window.cropCheck('def', 'AMP_Content_Image_Crop_Form');" ),
                ),
            'cancel' => array(
                'type' => 'submit',
                'label' => 'Cancel'),
            )
    ));
    var $_crop_fields = array( 
            'crop_interface' =>  array( 
                'type' => 'static',
                ),
            'start_x' => array( 
                'type' => 'hidden'
                ),
            'start_y' => array( 
                'type' => 'hidden'
                ),
            'end_x' => array( 
                'type' => 'hidden'
                ),
            'end_y' => array( 
                'type' => 'hidden'
                ),
            'height' => array( 
                'type' => 'hidden'
                ),
            'width' => array( 
                'type' => 'hidden'
                )
        );


    var $_crop_action;
    var $_allowed_actions = array( 'crop', 'preview', 'def' );

    var $_crop_width    = AMP_IMAGE_WIDTH_THUMB;
    var $_crop_height   = AMP_IMAGE_WIDTH_THUMB;

    var $crop_start_x;
    var $crop_start_y;

    var $_crop_jpeg_quality = 80;
    var $_target_page = 'crop_image2.php';

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

/*
    function execute( ){
        $action_method = 'commit' . ucfirst( $this->_crop_action );
        if ( !method_exists( $this, $action_method )) $action_method = 'commitDefault';
        return $this->$action_method( );
    }
    */

    function execute( ){
        $this->setupOnLoad( );
        $this->declareCSS( );
        $interface_html = $this->renderInterface( );
        $this->_crop_fields['crop_interface']['default'] = $interface_html ;
        $this->addFields( $this->_crop_fields )  ;
        $this->Build( true );
        return $this->output( );

    }

    function getDisplayRatio( ){
        return $this->_display_ratio;
    }

    function commitCrop( ){
        $target_image = &new Content_Image( $this->_image->getName( ) );
        $target_path  = $target_image->getPath( AMP_IMAGE_CLASS_CROP );
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

    function setupOnLoad( ){
        $header = & AMP_getHeader( );
        $header->addJavascriptOnLoad( 'libinit( );' );
        $header->addJavaScript( $this->_script_file, 'crop' );
        $header->addJavascriptDynamic( $this->_generateScript( ));
    }

    function _generateScript( ){
        return 
	"
    function libinit(){
	   obj=new lib_obj('cropDiv')
	   obj.dragdrop()
	}

	function cropCheck(crA, formname ){
	   if (!((((obj.x + obj.cr)-".$this->_window_x.") <= ".$this->_display_width . ")&&(((obj.y + obj.cb)-".$this->_window_y.") <= ".$this->_display_height . ")&&(obj.x >= ".$this->_window_x.")&&(obj.y >= ".$this->_window_y."))) {
	        alert('" . AMP_TEXT_ERROR_SELECTION_OUTSIDE_IMAGE . "');
            return false;
       }
       formRef = document.forms[formname];
       if (  formname >= ''){
           formRef.elements['start_x'].value = obj.x -".$this->_window_x.";
           formRef.elements['start_y'].value = obj.y -".$this->_window_y.";
           formRef.elements['width'].value = obj.cr;
           formRef.elements['height'].value = obj.cb;
           return true;
       }
        alert( 'formRef not Found');
        var url = '". AMP_SYSTEM_URL_IMAGE_VIEW 
                    . "?action=crop&filename=" . $this->_image->getName( )
                    . "&width=" . $this->_crop_width 
                    . "&height=" . $this->_crop_height
                    . "&start_x='+(obj.x-".$this->_window_x.")+'&start_y='+(obj.y-".$this->_window_y.")+'&s='+obj.cr;
        if (crA == 'pre'){
           window.open(url,'prevWin','width=".$this->_crop_width.",height=".$this->_crop_height."');
        } else {
           location.href=url;
           return true;
        }
    }

    function stopZoom() {
       loop = false;
       clearTimeout(zoomtimer);
    }

    function cropZoom(dir) {
       loop = true;
       prop = " . ( $this->_crop_height / $this->_crop_width ).";
       zoomtimer = null;
       direction = dir;
       if (loop == true) {
        if (direction == \"in\" ) {
       if ((obj.cr > ". ( $this->_crop_width / $this->_display_ratio ) .") && ( obj.cb > ".( $this->_crop_height / $this->_display_ratio ). " )) {
			cW = obj.cr - 1;
			cH = parseInt(prop * cW);
			obj.clipTo(0,cW,cH,0,1);
		   }
		} else {
		   if ((obj.cr < (" . $this->_display_width . "-5))&&(obj.cb < (".$this->_display_height . "-5))){
			cW = obj.cr + 1;
			cH = parseInt(prop * cW);
			obj.clipTo(0,cW,cH,0,1);
		   }
		}
		zoomtimer = setTimeout(\"cropZoom(direction)\", 10);
	   }
	}";

    }

    function declareCSS( ){
        $css = "body{font-family:arial,helvetica; font-size:12px}\n";
        $css .= "#cropDiv{  \n"
                ."   position:absolute;\n"
                ."   left:". $this->_window_x ."px; top:".$this->_window_y."px; \n"
                ."   width:" . intval( $this->_crop_width /$this->_display_ratio )."px;\n"
                ."   height:" . intval( $this->_crop_height / $this->_display_ratio )."px;\n "
                ."   z-index:2; \n"
                ."   background-image: url(".AMP_CONTENT_URL_IMAGES.AMP_ICON_SPACER.");\n"
                ."}\n";
        $page_header = &AMP_getHeader( );
        $page_header->addStylesheetDynamic( $css, 'cropper');
    }

    function renderInterface( ){
        $output = $this->_renderer->newline( 2 );
        $output .= $this->_renderer->image( $this->_displayImageURL( ), array( 'border' => 1 ));
        $output .= $this->_renderCropDiv( );
        $output .= $this->_renderer->newline( 2 );
        $output .= $this->_renderControls( );
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
        $stuff = 'Size: <input name="bigger" value="+" type="button" onMouseDown="window.cropZoom( \'out\');" onMouseUp="window.stopZoom( );" onMouseOut="window.stopZoom( );">&nbsp;'
                 . '<input name="smaller" value="-" type="button" onMouseDown="window.cropZoom( \'in\');" onclick="window.stopZoom( );" onMouseOut="window.stopZoom( );">&nbsp;<br />';
                 /*
                 . '<form name="image_crop_form" method="POST" action="'. AMP_SYSTEM_URL_IMAGES.'">'
                 . '<input name="height" type="hidden">'
                 . '<input name="width" type="hidden">'
                 . '<input name="start_x" type="hidden">'
                 . '<input name="start_y" type="hidden">'
                 . '<input name="end_x" type="hidden">'
                 . '<input name="end_y" type="hidden">'
                 . '<input name="submitCropAction[crop]" value="Crop" type="button" onclick="window.cropCheck( \'def\', this.form );">&nbsp;'
                 . '<input name="submitCropAction[cancel]" value="Cancel" type="button" >'
                 . '</form>';
                 */
        return $stuff;
        require_once( 'AMP/Form/SearchForm.inc.php');
        $form = &new AMPSearchForm( );
        $form->addFields( $this->_interface_controls );
        $form->Build( true );
        return $form->output( );
    }

    function _renderCropDiv( ){
        $table_html = 
            '<table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">'."\n"
            . '        <tr><td>'."\n"
            . $this->_renderer->image( AMP_CONTENT_URL_IMAGES . AMP_ICON_SPACER )
            . '</td></tr></table>';
        return $this->_renderer->inDiv( $table_html, array( 'id' => 'cropDiv'));
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
        return $this->_renderer->link( 
                    $this->_renderer->_image( $this->_displayCropPreviewURL( ), array( 'border' => 0)),
                    "#", array( 'onClick'=> $this->_crop_script . '.closePreview( );')) ;
            
    }

}

?>
