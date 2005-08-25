<?php

require_once( 'AMP/Content/Article/SetDisplay.inc.php' );
require_once( 'AMP/Content/Section/SetDisplay.inc.php' );
require_once( 'AMP/Content/Section/Contents/Display/Newsroom.inc.php' );
require_once( 'AMP/Content/Section/Contents/Display/ArticlesBySubsection.inc.php' );
require_once( 'AMP/Content/Section/Contents/Display/SubsectionsPlusArticles.inc.php' );

require_once( 'AMP/Content/Display/ListIntro.inc.php' );

class SectionContents_Display  extends AMPDisplay_HTML {

    var $_manager;
    var $_section;
    var $_listIntro;

    var $_custom_displays = array(
        'Articles'          => 'ArticleSet_Display',
        'Subsections'       => 'SectionSet_Display',
        'ArticlesAggregator'=> 'ArticleSet_Display'
        );

    function SectionContents_Display( &$contents_manager ) {
        $this->init( $contents_manager );
    }

    function init( &$contents_manager ) {
        $this->_manager = &$contents_manager;
        $this->_section = &$this->_manager->getSection();

        if (!($contents = &$this->_manager->getContents())) return;
        $this->initDisplay( $contents );
    }

    function initDisplay( &$contents ) {

        $display_class = $this->_getDisplayClass();
        $this->_display = &new $display_class( $contents );

        if (!method_exists( $this->_display, 'setSection' )) return;
        $this->_display->setSection( $this->_section );
    }

    function _getDisplayClass() {
        $result = 'SectionContentDisplay_' . $this->_manager->getContentsType();
        if (isset($this->_custom_displays [ $this->_manager->getContentsType() ] )) {
            $result = $this->_custom_displays [ $this->_manager->getContentsType() ];
        }
        if (!class_exists( $result )) return 'ArticleSet_Display';
        return $result;
    }

    function execute() {
        $intro = &$this->getIntroDisplay();
        if (! ( $this->_section->showContentList() && isset($this->_display))) 
            return ($intro ? $intro->execute() : $intro ) ;

        return  $this->_HTML_listIntro( $intro ) . 
                $this->_display->execute();
    }
        

    function &getIntroDisplay() {
        $intro = false;
        $currentPage = &AMPContent_Page::instance();
        if ($currentPage->contentManager->showListIntro()) {
            $intro = &new ListIntro_Display( $this->_section );
        }
        return $intro;
    }


    function _HTML_listIntro( &$intro ) {
        if (!(isset($this->_display) && isset($this->_display->_pager) && isset($this->_section))) return false;
        if (!($this->_display->isFirstPage() && $intro)) return $this->_display->_pager->_HTML_topNotice( $this->_section->getName() );
        return $intro->execute() . $this->_HTML_newline();
    }
}
?>