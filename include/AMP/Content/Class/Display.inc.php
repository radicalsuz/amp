<?php

if (!defined( 'AMP_CONTENT_LIST_INTRO_DISPLAY' )) define ('AMP_CONTENT_LIST_INTRO_DISPLAY', 'ListIntro_Display' );

require_once ('AMP/Content/Article/SetDisplay.inc.php' );
require_once ('AMP/Content/Class.inc.php' );

class ContentClass_Display extends ArticleSet_Display {

    var $_class;
    var $_showListIntro = true;

    function ContentClass_Display( &$classRef ) {
        $this->init( $classRef->getContents() );
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
        return new $introClass( $this->_class );
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
