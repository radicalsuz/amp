<?php

if (!defined( 'AMP_CONTENT_LIST_INTRO_DISPLAY' )) define ('AMP_CONTENT_LIST_INTRO_DISPLAY', 'ListIntro_Display' );

require_once ('AMP/Content/Article/SetDisplay.inc.php' );
class ContentClass_Display extends ArticleSet_Display {

    var $_class;

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
        $intro = false;
        $currentPage = &AMPContent_Page::instance();
        if ($currentPage->contentManager->showListIntro()) {
            $introClass = AMP_CONTENT_LIST_INTRO_DISPLAY;
            $intro = &new $introClass( $this->_class );
        }
        return $intro;
    }

    function _HTML_listIntro( &$intro ) {
        if ( isset ($this->_pager) && !( $this->isFirstPage() && $intro )) return $this->_pager->_HTML_topNotice( $this->_class->getName() );
        return $intro->execute(); # . $this->_HTML_newline();
    }
}
?>
