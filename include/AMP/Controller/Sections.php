<?php
require_once( 'AMP/Controller/Controller.php');
class AMP_Controller_Sections extends AMP_Controller {

    function show( ) {
        $this->_before( 'show' );
        $section = $this->current_object = &new Section( AMP_dbcon( ), $this->params['id']);
        if( !( $section && $section->hasData() 
          && ( $section->isDisplayable( ) || AMP_Authenticate( 'admin') ) ))  {
                return AMP_make_404();
        }
        $display = &$section->getDisplay( );
        $this->_render_section_header( $display );
        $this->render( $display );
    }

    function _render_section_header( $display ) {
        if ( isset( $display->api_version ) && $display->api_version == 2 ) return;
		$this->_page = &AMPContent_Page::instance();
		if( !isset( $display->pager ) || $display->pager->is_first_page( )) {
			if( method_exists( $display, 'render_intro')) {
				$this->_render_intro( $display->render_intro( ) );
			} else {
				$this->_render_intro( $this->_page->getListDisplayIntro( ) );
			}
		}

    }

    function _render_intro( $display ) {
        $page = & AMPContent_Page::instance( );
        $page->contentManager->add( $display, AMP_CONTENT_DISPLAY_KEY_INTRO );
    }

    function _before( $action ) {
        $this->_page_setup( );
    }

    function _page_setup( ) {
        $page = & AMPContent_Page::instance( );
        $page->setSection( $this->params['id']);
        $page->initLocation();
    }

}
?>
