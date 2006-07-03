<?php

require_once( 'AMP/Content/Article/Form.inc.php');
require_once( 'AMP/Content/Article/Version.inc.php');

class Article_Version_Form extends Article_Form {
    var $submit_button = array( 'submitAction' => array(
        'type' => 'group',
        'elements'=> array(
            'save' => array(
                'type' => 'submit',
                'label' => 'Restore This Version',
                'attr' => array ( 
                    'onclick' => 
                    "return confirmSubmit('Restoring this record will OVERWRITE your current version.  Continue?');" ),
                ),
            'cancel' => array(
                'type' => 'submit',
                'label' => 'Cancel'),
            'delete_version' => array(
                'type' => 'submit',
                'label' => 'Delete Record',
                'attr' => array ( 
                    'onclick' => 
                    "return confirmSubmit('Are you sure you want to DELETE this record?');" ),
                )
            )
    ));

    var $allow_copy = false;
    var $_field_def_vid = array( 
        'type' => 'hidden'
    );


    function Article_Version_Form( ){
        $this->init( 'article', 'POST', AMP_SYSTEM_URL_ARTICLE );
    }
/*
    function setDynamicValues() {
        PARENT::setDynamicValues( );
        $this->removeSubmit( 'copy' );
        $this->removeSubmit( 'save' );
        $this->defineSubmit( 'restore', 'Restore This Version' );
    }
    */
    function getArticleIdValue( ){
        $id_field = 'id';
        if (isset($_REQUEST[ 'id' ]) && is_numeric( $_REQUEST[ $this->id_field ])) return $_REQUEST[ $this->id_field ];
        if ( !isset( $this->form )) return false;
        if ( !$this->isBuilt ) return false;
        
        $set = $this->getValues( $this->id_field );
        if ($set) return $set[ $this->id_field ];
        return false;
    }

    function adjustFields( $fields ){
        unset( $fields['comment_list_header']);
        unset( $fields['comment_list']);
        unset( $fields['top_submit_buttons']);
        $fields['vid'] = $this->_field_def_vid;
        $fields = array_merge( $fields, $this->_defineCustomFields( ));
        return $fields;
    }

    function _formHeader( ){
        $article = &$this->_get_model( );
        if ( !$article ) return false;

        require_once( 'AMP/Content/Article/Display/Info.php');
        $display = &new ArticleDisplay_Info( $article );
        return $display->execute( );
    }

    function &_get_model(  ){

        if ( isset( $this->_model )) return $this->_model;

        $id = $this->getIdValue( );
        if ( !$id ) return false;
        require_once( 'AMP/Content/Article/Version.inc.php');

        $article = &new Article_Version( AMP_Registry::getDbcon( ), $id ) ;
        if ( !$article->hasData( )) return false;

        $this->_model = &$article;
        return $this->_model;
    }

    function _formFooter( ){
        $article = &$this->_get_model( );
        if ( !$article ) return false;
        $id = $article->getArticleId( );
        $renderer = &new AMPDisplay_HTML( );
        $output = $renderer->inSpan( sprintf( AMP_TEXT_CURRENT_ACTION, AMP_TEXT_EDIT, sprintf( AMP_TEXT_VERSION_ID, $article->id )), array( 'class' => 'page_result' ));

        require_once( 'AMP/Content/Article/Version/List.inc.php');
        $list = &new Article_Version_List( AMP_Registry::getDbcon( ), array( 'article' => $id ));
        return $output . $list->execute( );
    }

    function _after_init( ){
        //do nothing
    }
}


?>
