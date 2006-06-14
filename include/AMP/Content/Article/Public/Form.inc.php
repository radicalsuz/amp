<?php

require_once( 'AMP/Content/Article/Form.inc.php');

class Article_Public_Form extends Article_Form {

    function Article_Public_Form( ) {
        $name = "article";
        $this->init( $name, 'POST', AMP_CONTENT_URL_ARTICLE_INPUT );
    }

    function setDynamicValues() {
        //override to nada
    }

    function _initJavascriptActions( ){
        #$this->HTMLEditorSetup( );
    }

    function _formHeader( ){
        return false;
    }

    function _formFooter( ){
        return false;
    }

    function adjustFields( $fields ){
        return $fields;
    }
}

?>
