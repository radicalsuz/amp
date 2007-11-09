<?php

require_once( 'AMP/Content/Section/Contents/Source.inc.php' );
require_once( 'AMP/Content/Section/Contents/Display.inc.php' );

require_once( 'AMP/Content/Section/Contents/Stub.php' );
require_once( 'AMP/Content/Section/Contents/Articles.inc.php' );
require_once( 'AMP/Content/Section/Contents/ArticlesBySubsection.inc.php' );
require_once( 'AMP/Content/Section/Contents/Subsections.inc.php' );
require_once( 'AMP/Content/Section/Contents/SubsectionsPlusArticles.inc.php' );
require_once( 'AMP/Content/Section/Contents/Newsroom.inc.php' );
require_once( 'AMP/Content/Section/Contents/ArticlesAggregator.inc.php' );
require_once( 'AMP/Content/Config.inc.php');

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

        $source_override_value = 'AMP_CONTENT_SOURCE_' . strtoupper( $this->getDisplayType( $listType ));
        if( defined ( $source_override_value )) {
            $contentSource_class = constant( $source_override_value ) ;
        } else {
            //standard parsing method to figure out source class
            $this->_contentSourceType = AMP_to_camelcase( strtolower( $this->getDisplayType( $listType ) )) ;
            $contentSource_class = 'SectionContentSource_'.$this->_contentSourceType;
            if ( !class_exists( $contentSource_class )) {
                trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $contentSource_class ));
                $contentSource_class = 'SectionContentSource_Articles';
            }
        }
        
        $result = &new $contentSource_class( $this->_section );
        return $result;
    }

    function getDisplayType( $listType=null ) {
        if ( !isset( $listType )) $listType = $this->_section->getListType( );
        $listTypes = filterConstants( 'AMP_SECTIONLIST' );
        if (!$display_type = array_search( $listType, $listTypes )) return "ARTICLES";
        return $display_type;

    }

    function getSectionCriteria() {
        //deprecated -- use 
        if (!method_exists( $this->_contentSource, 'getSectionCriteria' )) return false;
        return $this->_contentSource->getSectionCriteria();
    }

    function _setContentSourceSet( $result_dataset ) {
        $this->_contentSourceSet = $result_dataset;
    }

    function &getContents( $auto_read = false ) {
        if (!isset($this->_contentSourceSet) || !method_exists( $this->_contentSourceSet, 'makeReady' )) return false;
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
        $result = &new SectionContents_Display( $this );
        return $result;
    }

    function display() {
        $display = &$this->getDisplay();
        return $display->execute();
    }

    function getRelatedArticles( $section_id ) {
        require_once( 'AMP/Content/Section/RelatedSet.inc.php' );
        if ( !isset( $section_id )) return false;

        $relatedContent = AMPContentLookup_RelatedArticles::instance( $section_id );
        if (empty( $relatedContent )) return false;

        return "id in (" . join( ", ", array_keys( $relatedContent ) ). ")";
    }

}
?>
