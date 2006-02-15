<?php
require_once ('AMP/Content/Section/Contents/Articles.inc.php' );
if (!defined( 'AMP_CONTENT_SECTION_PLUS_CLASS' )) define ( 'AMP_CONTENT_SECTION_PLUS_CLASS', 5 );

class SectionContentSource_ArticlesPlusClass extends SectionContentSource_Articles {

    function SectionContentSource_ArticlesPlusClass ( &$section ) {
        $this->init( $section );
    }

    function _addCriteriaSection( ){
        $this->_source->addCriteriaSectionOrClass( $this->_section->id, AMP_CONTENT_SECTION_PLUS_CLASS );
    }

    function getSectionCriteria() {
        //deprecated -- this is not good
        $section_crit = PARENT::getSectionCriteria();
        $plusclass = "class =". AMP_CONTENT_SECTION_PLUS_CLASS;
        return "( ". $plusclass ." OR ". $section_crit . " )";
    }
}
?>
