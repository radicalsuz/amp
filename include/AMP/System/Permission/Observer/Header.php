<?php

require_once( 'AMP/System/Observer.php');

class AMP_System_Permission_Observer_Header extends AMP_System_Observer {

    var $_saved_header;

    function AMP_System_Permission_Observer_Header( ) {

    }

    function onInitForm( &$controller ) {
        $model = $controller->get_model( );
        if ( !isset( $model->id )) {
            $model->readData( $controller->get_model_id( ));
        }
        $header_id = $model->getHeaderTextId( );
        if ( !$header_id ) return false;
        require_once( 'AMP/Content/Article.inc.php');
        $article = new Article( AMP_Registry::getDbcon( ), $header_id ) ;
        $section = $article->getParent( );
        $allowed_sections = AMP_lookup( 'sectionMap');
        if ( !isset( $allowed_sections[ $section ])) {
            $this->_saved_header = $header_id;
            $form = $controller->get_form( );
            $form->dropField( 'url') ;
        }
    }

    function onBeforeSave( &$controller ) {
        if ( !isset( $this->_saved_header )) return;
        $model = $controller->get_model( );
        $model->mergeData( array( 'url' => $this->_saved_header ));

    }
}


?>
