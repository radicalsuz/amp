<?php

if (!defined( 'AMP_CONTENT_LIST_INTRO_DISPLAY' )) define ('AMP_CONTENT_LIST_INTRO_DISPLAY', 'ListIntro_Display' );

require_once ('AMP/Content/Article/SetDisplay.inc.php' );
require_once ('AMP/Content/Class.inc.php' );

class ContentClass_Display extends ArticleSet_Display {

    var $_class;
    var $_showListIntro = true;

    function ContentClass_Display( &$classRef, $read_data = true ) {
        $this->init( $classRef->getContents(), $read_data );
        $this->_class = &$classRef;
    }

    function execute() {
        $intro = &$this->getIntroDisplay();
        return  $this->_HTML_listIntro( $intro ) . 
                PARENT::execute();
    }

    function &getIntroDisplay() {
        if (! $this->_showListIntro) return false; 

        $introClass = AMP_CONTENT_LIST_INTRO_DISPLAY;
        $path = split( '_', $introClass );
        include_once( 'AMP/Content/'.join( '/', array_reverse( $path ) ) .'.inc.php' );

        if( class_exists( $introClass ) ) {
            return new $introClass( $this->_class );
        }
        trigger_error( $introClass.'is not declared');
        return false;
    }

    function setListIntro( $show_intro = true ) {
        $this->_showListIntro = $show_intro;
    }

    function _HTML_listIntro( &$intro ) {
        if ( !$intro ) return false;
        if ( isset ($this->_pager) && !( $this->isFirstPage() && $intro )) return $this->_pager->_HTML_topNotice( $this->_class->getName() );
        return $intro->execute(); # . $this->_HTML_newline();
    }
}
?>
