<?php

require_once ('AMP/Content/Section/Contents/Articles.inc.php' );
if (!defined( 'AMP_CONTENT_CLASS_FEATURE' )) define ( 'AMP_CONTENT_CLASS_FEATURE', 1 );

class SectionContentSource_ArticlesFeatures extends SectionContentSource_Articles {

    function SectionContentSource_ArticlesFeatures ( &$section ) {
        $this->init( $section );
    }

    function _setBaseCriteria() {
        $this->_source->addCriteriaClass( AMP_CONTENT_CLASS_FEATURE );
        $this->_display_crit_source->cleanStatus( $this->_source );
    }

    function _getClassCriteria() {
        //deprecated
        return "class=".AMP_CONTENT_CLASS_FEATURE;
    }
}
?>
