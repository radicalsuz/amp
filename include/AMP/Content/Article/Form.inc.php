<?php

require_once( 'AMP/System/Form/XML.inc.php');

class Article_Form extends AMPSystem_Form_XML {

    var $name_field = 'title';

    function Article_Form( ) {
        $name = "article";
        $this->init( $name );
    }

    function setDynamicValues() {
        $this->setFieldValueSet( 'doc', AMPfile_list( 'downloads'));
        $this->HTMLEditorSetup( );
    }

    function _configHTMLEditor( &$editor ){
        $editor->height = '800px';
    }
}
?>
