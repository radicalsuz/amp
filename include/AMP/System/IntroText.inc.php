<?php

/**************
 *  AMPSystem_IntroText 
 *  represents introductory and response texts
 *  used by the Modules system
 *
 *  AMP 3.5.0
 *  2005-27-06
 *
 *  Author: austin@radicaldesigns.org
 *
 *****/

require_once ( 'utility.functions.inc.php' );
require_once ( 'AMP/System/Data/Item.inc.php' );
require_once ( 'AMP/Content/Article/Display/Introtext.inc.php' );

 class AMPSystem_IntroText extends AMPSystem_Data_Item {

    var $textdata;
    var $_textdata_keys;
    var $id;
    var $datatable = "moduletext";
    var $name_field = 'name';

    function AMPSystem_IntroText ( &$dbcon, $text_id=null ) {
        $this->init( $dbcon, $text_id );
    }
    
    function _adjustSetData( $data ) {
        $this->legacyFieldname( $data, 'test', 'body' );
        $this->legacyFieldname( $data, 'subtitile', 'subtitle' );
    }

    function &getDisplay() {
        return new ArticleDisplay_IntroText( $this );
    }


    function getSection() {
        return $this->getData( 'type' );
    }

    function getTemplate() {
        return $this->getData( 'templateid' );
    }

    function getTitle() {
        return $this->getData( 'title' );
    }
    function getSubTitle() {
        return $this->getData( 'subtitle' );
    }


    function getBody() {
        return $this->getData( 'body' );
    }

    function isHtml() {
        return $this->getData( 'html' );
    }

    function mergeBodyFields( $fielddata ) {
        $replace_values = AMP_makeMergeFields( array_keys($fielddata) );
        return str_replace( $replace_values, $fielddata, $this->getBody() );
        #return ereg_replace( "%\w+%", "", $merged );
    }

    function getImageRef() {
        //for now, no images for introtexts
        return false;
    }

 }

 ?>
