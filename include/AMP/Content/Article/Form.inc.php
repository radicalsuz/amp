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
        $this->_initTabDisplay( );
    }

    function _configHTMLEditor( &$editor ){
        $editor->height = '600px';
    }

    function _initTabDisplay( ){
        $header = &AMP_getHeader( );
        $header->addJavaScript( 'scripts/tabs.js', 'tabs');
        
        $header->addJavascriptOnload( 
            'current_tab = document.getElementById( "tab_0" );'."\n"
            .'if ( current_tab ) Tabs_highlight( current_tab ) ;'
            );
        
    }

    function _selectAddNull( $valueset, $name ) {
        if ( $name != 'type' ) return PARENT::_selectAddNull( $valueset, $name );
        return array( AMP_CONTENT_MAP_ROOT_SECTION => '-- ' . AMP_SITE_NAME . ' --') + $valueset;
    }

}
?>
