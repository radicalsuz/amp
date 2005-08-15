<?php

require_once( 'AMP/Content/Articles/SetDisplay.inc.php' );
require_once( 'AMP/Content/Sections/SetDisplay.inc.php' );
require_once( 'AMP/Content/Sections/Contents/Display/Newsroom.inc.php' );
require_once( 'AMP/Content/Sections/Contents/Display/ArticlesBySubsection.inc.php' );
require_once( 'AMP/Content/Sections/Contents/Display/SubsectionsPlusArticles.inc.php' );

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
        $this->_manager = &$content_manager;
        $this->_section = &$this->_manager->getSection();

        $display_class = $this->getDisplayClass();
        $this->_display = &new $display_class( $this->_manager->getContents() );

        if (!method_exists( $this->_display, 'setParentSection' )) return;
        $this->_display->setParentSection( $this->_section->id );
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
        if (!$this->_section->showContentList()) return ($intro ? $intro->execute() : $intro ) ;

        if ($intro) $this->_setListIntro( $intro->execute() ) ;
        
        return  $this->_HTML_listIntro() . 
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


    function setListIntro( $html ) {
        $this->_listIntro = $html;
    }

    function getListIntro() {
        if (!isset($this->_listIntro)) return false;
        return $this->_listIntro;
    }

    function _HTML_listIntro() {
        if (!$this->isFirstPage()) return false;
        return $this->getListIntro() . $this->_HTML_newline();
    }
}
?>
