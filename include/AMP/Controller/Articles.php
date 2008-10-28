<?php
require_once( 'AMP/Controller/Controller.php');
class AMP_Controller_Articles extends AMP_Controller {

    function show( ) {
        $this->_before( 'show' );
        $article = $this->current_object = &new Article( AMP_dbcon( ), $this->params['id']);
        if( !( $article && $article->hasData() 
          && ( $article->isDisplayable( ) || AMP_Authenticate( 'admin') ) ))  {
                return AMP_make_404();
        }
        $this->render( $article->getDisplay( ) );
    }

    function _before( $action ) {
        $this->_page_setup( );
    }

    function _page_setup( ) {
        $page = & AMPContent_Page::instance( );
        $page->setArticle( $this->params['id']);
        $page->initLocation();
    }

}
?>
