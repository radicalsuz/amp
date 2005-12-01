<?php

require_once ('AMP/System/Form/XML.inc.php');
require_once ('AMP/Content/Map/Select.inc.php');
require_once ('Modules/Podcast/PodcastItem.php' );
require_once ('Modules/Podcast/Item/ComponentMap.inc.php' );


class PodcastItem_Form extends AMPSystem_Form_XML {

	var $inital_form_links = array();
	

	function PodcastItem_Form() {
		$name = "Podcast";

		$this->init( $name );
	
	}
    
    function setDynamicValues( ){
        $this->setFieldValueSet( 'file', AMPfile_list( 'downloads','mp3'));
    }
	
 
  
}

?> 
