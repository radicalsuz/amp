<?php

require_once( 'AMP/Content/Section/Contents/Source.inc.php' );
require_once( 'AMP/Content/Section/Contents/Display.inc.php' );

require_once( 'AMP/Content/Section/Contents/Articles.inc.php' );
require_once( 'AMP/Content/Section/Contents/ArticlesBySubsection.inc.php' );
require_once( 'AMP/Content/Section/Contents/Subsections.inc.php' );
require_once( 'AMP/Content/Section/Contents/SubsectionsPlusArticles.inc.php' );
require_once( 'AMP/Content/Section/Contents/Newsroom.inc.php' );
require_once( 'AMP/Content/Section/Contents/ArticlesAggregator.inc.php' );

if (!defined( 'AMP_CONTENT_LISTORDER_MAX')) define('AMP_CONTENT_LISTORDER_MAX', 999999999);

if (!defined( 'AMP_SECTIONLIST_ARTICLES'))      define( 'AMP_SECTIONLIST_ARTICLES', '1' );
if (!defined( 'AMP_SECTIONLIST_NEWSROOM'))      define( 'AMP_SECTIONLIST_NEWSROOM', '2' );
if (!defined( 'AMP_SECTIONLIST_SUBSECTIONS'))   define( 'AMP_SECTIONLIST_SUBSECTIONS', '5' );
if (!defined( 'AMP_SECTIONLIST_ARTICLES_BY_SUBSECTION')) 
                                                define( 'AMP_SECTIONLIST_ARTICLES_BY_SUBSECTION', '3' );
if (!defined( 'AMP_SECTIONLIST_SUBSECTIONS_PLUS_ARTICLES')) 
                                                define( 'AMP_SECTIONLIST_SUBSECTIONS_PLUS_ARTICLES', '6' );
if (!defined( 'AMP_SECTIONLIST_ARTICLES_AGGREGATOR')) 
                                                define( 'AMP_SECTIONLIST_ARTICLES_AGGREGATOR', '7' );
if ( file_exists_incpath( 'custom.layouts.inc.php' )) include_once ('custom.layouts.inc.php' );
if ( file_exists_incpath( 'custom.sources.inc.php' )) include_once ('custom.sources.inc.php' );

class SectionContents_Manager {

    var $_section;
    var $_contentSource;
    var $_contentSourceType;
    var $_contentSourceSet;

    function SectionContents_Manager ( &$section ) {
        $this->init( $section );
    }

    function init( &$section ) {
        $this->_section = &$section;
        $this->_contentSource = &$this->_getContentSource( $section->getListType() );
        $this->_setContentSourceSet( $this->_contentSource->execute() );
    }

    function &_getContentSource( $listType ) {
        $listTypes = filterConstants( 'AMP_SECTIONLIST' );
        if (!$display_type = array_search( $listType, $listTypes )) $display_type = "ARTICLES";

        $this->_contentSourceType = str_replace( " ", "", ucwords( str_replace( "_", " ", strtolower( $display_type ) ) ) );
        
        $contentSource_class = 'SectionContentSource_'.$this->_contentSourceType;
        return new $contentSource_class( $this->_section );
    }

    function getSectionCriteria() {
        if (!method_exists( $this->_contentSource, 'getSectionCriteria' )) return false;
        return $this->_contentSource->getSectionCriteria();
    }

    function _setContentSourceSet( $result_dataset ) {
        $this->_contentSourceSet = &$result_dataset;
    }

    function &getContents( $auto_read = false ) {
        if (!isset($this->_contentSourceSet)) return false;
        if ((!$this->_contentSourceSet->makeReady()) && $auto_read ) $this->_contentSourceSet->readData();

        return $this->_contentSourceSet;
    }

    function &getSection() {
        return $this->_section;
    }

    function getContentsType() {
        return $this->_contentSourceType;
    }


    ######### display methods ############
    ######################################

    function &getDisplay() {
        return new SectionContents_Display( $this );
    }

    function display() {
        $display = &$this->getDisplay();
        return $display->execute();
    }


}
?>
