<?php

if (!defined( 'AMP_CONTENT_LIST_INTRO_DISPLAY' )) define ('AMP_CONTENT_LIST_INTRO_DISPLAY', 'ListIntro_Display' );

require_once( 'AMP/Content/Article/SetDisplay.inc.php' );
require_once( 'AMP/Content/Section/SetDisplay.inc.php' );
require_once( 'AMP/Content/Section/Contents/Display/Newsroom.inc.php' );
require_once( 'AMP/Content/Section/Contents/Display/ArticlesBySubsection.inc.php' );
require_once( 'AMP/Content/Section/Contents/Display/SubsectionsPlusArticles.inc.php' );

require_once( 'AMP/Content/Display/ListIntro.inc.php' );

if ( !defined( 'AMP_SECTION_DISPLAY_DEFAULT' ))             define( 'AMP_SECTION_DISPLAY_DEFAULT',          'ArticleSet_Display' );
if ( !defined( 'AMP_SECTION_DISPLAY_ARTICLES' ))            define( 'AMP_SECTION_DISPLAY_ARTICLES',         'ArticleSet_Display' );
if ( !defined( 'AMP_SECTION_DISPLAY_SUBSECTIONS' ))         define( 'AMP_SECTION_DISPLAY_SUBSECTIONS',      'SectionSet_Display' );
if ( !defined( 'AMP_SECTION_DISPLAY_ARTICLESAGGREGATOR' ))  define( 'AMP_SECTION_DISPLAY_ARTICLESAGGREGATOR','ArticleSet_Display' );
if ( !defined( 'AMP_PREFIX_SECTION_DISPLAY' ))              define( 'AMP_PREFIX_SECTION_DISPLAY',           'SectionContentDisplay_' );

class SectionContents_Display  extends AMPDisplay_HTML {

    var $_manager;
    var $_section;
    var $_listIntro;
/*
    var $_custom_displays = array(
        'Articles'          => 'ArticleSet_Display',
        'Subsections'       => 'SectionSet_Display',
        'ArticlesAggregator'=> 'ArticleSet_Display'
        );
    */

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
        $result = AMP_PREFIX_SECTION_DISPLAY . $this->_manager->getContentsType();
        $custom_displays = filterConstants( 'AMP_SECTION_DISPLAY' );
        if (isset($custom_displays [ strtoupper( $this->_manager->getContentsType() ) ] )) {
            $result = $custom_displays [ strtoupper( $this->_manager->getContentsType() ) ];
        }
        if (!class_exists( $result )) return AMP_SECTION_DISPLAY_DEFAULT;
        return $result;
    }

    function execute() {
        $intro = &$this->getIntroDisplay();
        if (! ( $this->_section->showContentList() && isset($this->_display))) 
            return ($intro ? $intro->execute() : $intro ) ;

        return  $this->_HTML_listIntro( $intro ) . 
                $this->_display->execute() .
                $this->_HTML_listFooter() ;
    }
        

    function &getIntroDisplay() {
        $intro = false;
        $currentPage = &AMPContent_Page::instance();
        if ($currentPage->contentManager->showListIntro()) {
            $introClass = AMP_CONTENT_LIST_INTRO_DISPLAY;
            $intro = &new $introClass( $this->_section );
        }
        return $intro;
    }


    function _HTML_listIntro( &$intro ) {
        if (!(isset($this->_display) && isset($this->_section))) return false;
        if ( isset( $this->_display->_pager ) && !($this->_display->isFirstPage() && $intro)) return $this->_display->_pager->_HTML_topNotice( $this->_section->getName() );
        return $intro->execute() . $this->_HTML_newline();
    }

    function _HTML_listFooter() {
        if (!AMP_CONTENT_CLASS_SECTIONFOOTER) return false;
        $footer = false;
        $currentPage = &AMPContent_Page::instance();

        if (!($currentPage->contentManager->showListIntro() && ($footer = &$this->_section->getFooterRef() ))) return false;

        $display = &$footer->getDisplay();
        return $display->execute();
    }
}
?>
