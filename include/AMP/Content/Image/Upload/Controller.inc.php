<?php

require_once( 'AMP/Content/Image/Upload/Form.inc.php');
require_once( 'AMP/System/Page/Controller.inc.php');

class AMP_Content_Image_Upload_Controller extends AMPSystemPage_Controller {

    function AMP_Content_Image_Upload_Controller( ){
        $this->init( );
    }

    function getDefaultAction( ){
        return 'input';
    }

    function input( ){
        $this->_page->setComponentName( 'AMP_Content_Image_Upload_Form', 'form');
        $this->_page->addComponent( 'form');
        $this->_page->_initForm( );
        $display = &$this->_page->getDisplay( );
        $display->setItemType( 'Image');
        $display->setNavName( 'gallery');
        $this->_page->action =  'Upload';

    }


}

?>
