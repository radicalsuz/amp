<?php

require_once( 'AMP/Content/Section/Contents/Articles.inc.php' ); 

class SectionContentSource_Newsroom extends SectionContentSource_Articles {

    var $_included_classes = array(
        'news'  => AMP_CONTENT_CLASS_NEWS,
        'pr'    => AMP_CONTENT_CLASS_PRESSRELEASE
        );


    function SectionContentSource_Newsroom( &$section ) {
        $this->init( $section );
    }


    ###################################
    ### private data source methods ###
    ###################################

    function _setCriteria() {
        $this->_setBaseCriteria();
    }

    function _setBaseCriteria() {
        $this->_source->addCriteria( $this->_getClassCriteria() );
        $this->_display_crit_source->cleanStatus( $this->_source );
    }

    function _getClassCriteria() {
        if (empty($this->_included_classes)) return false;
        return "class in (" . join( ", ", $this->_included_classes ) . ")" ;
    }

    function _setSort() {
        $this->_setBaseSort();
    }

}
?>
