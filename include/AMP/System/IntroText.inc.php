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

require_once ( 'AMP/System/Data/Item.inc.php' );

 class AMPSystem_IntroText extends AMPSystem_Data_Item {

    var $textdata;
    var $_textdata_keys;
    var $id;
    var $datatable = "moduletext";
    var $name_field = 'name';

    function AMPSystem_IntroText ( &$dbcon, $text_id=null ) {
        $this->init( $dbcon, $text_id );
    }
    
    function adjustSetData( $data ) {
        $this->legacyFieldname( $data, 'test', 'body' );
        $this->legacyFieldname( $data, 'subtitile', 'subtitle' );
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

 }

 ?>
