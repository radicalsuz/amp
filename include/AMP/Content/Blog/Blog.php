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

 class AMPSystem_Blog extends AMPSystem_Data_Item {

    var $textdata;
    var $_textdata_keys;
    var $id;
    var $datatable = "articles";
    var $name_field = 'title';

    function AMPSystem_Blog ( &$dbcon, $text_id=null ) {
        $this->init( $dbcon, $text_id );
    }
    
	function setData( $data ) {
		$data['class'] = AMP_CONTENT_CLASS_BLOG;
		PARENT::setData($data);
	}

    function adjustSetData( $data ) {
        $this->legacyFieldname( $data, 'test', 'body' );
        $this->legacyFieldname( $data, 'subtitile', 'subtitle' );
    }

    function getSection() {
        return $this->getData( 'type' );
    }

    function getTitle() {
        return $this->getData( 'title' );
    }

 }

 ?>
