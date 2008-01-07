<?php
require_once( 'AMP/Content/Article/Form.inc.php');

class Article_Frontpage_Form extends Article_Form {

    function Article_Frontpage_Form( ) {
        $name = "article";
        $this->init( $name, 'POST', AMP_SYSTEM_URL_ARTICLE_FRONTPAGE );
    }

    function setDynamicValues( ){
        $this->addTranslation( 'image_upload', '_manageUpload',     'get');
        $this->addTranslation( 'picture',      '_checkUploadImage', 'get');
        $this->addTranslation( 'date',         '_makeDbDateTime',   'get');

    }

    function _initJavascriptActions( ){
        $this->HTMLEditorSetup( );
        $header = &AMP_getHeader( );
        $this->_initAutoLookups( $header );
        $this->_initPhotoLookup( $header );
    }

    function adjustFields( $fields ){
        return $fields;
    }

}
?>
