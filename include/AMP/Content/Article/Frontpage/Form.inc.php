<?php
require_once( 'AMP/Content/Article/Form.inc.php');

class Article_Frontpage_Form extends Article_Form {

    function Article_Form( ) {
        $name = "article_frontpage";
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
    }

    function adjustFields( $fields ){
        return $fields;
    }

    function _formFooter( ){
        $id = $this->getIdValue( );
        if ( !$id ) return false;
        require_once( 'AMP/Content/Article/Version/List.inc.php');
        $list = &new Article_Version_List( AMP_Registry::getDbcon( ), array( 'article' => $id ));
        $list->setTargetLinks( AMP_SYSTEM_URL_ARTICLE_FRONTPAGE );
        return $list->execute( );
    }
}
?>
