<?php

require_once ('AMP/System/Form/XML.inc.php');
require_once ('AMP/Content/Blog/Blog.php' );
require_once ('AMP/Content/Blog/ComponentMap.inc.php' );
require_once ('AMP/Content/Map/Select.inc.php' );

class Blog_Form extends AMPSystem_Form_XML {

	var $inital_form_links = array();

	function Schedule_Form() {
		$name = "Blog";
		$this->init( $name );
	}

    function setDynamicValues() {
        $map = ContentMap_Select::getIndentedValues();
        $this->setFieldValueSet( 'type' , $map );
    }

}

?> 
