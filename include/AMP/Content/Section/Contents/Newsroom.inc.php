<?php

require_once( 'AMP/Content/Section/Contents/Articles.inc.php' ); 

class SectionContentSource_Newsroom extends SectionContentSource_Articles {

    var $_included_classes = array(
        AMP_CONTENT_CLASS_NEWS,
        AMP_CONTENT_CLASS_PRESSRELEASE
        );


    function SectionContentSource_Newsroom( &$section ) {
        $this->init( $section );
    }


    ###################################
    ### private data source methods ###
    ###################################

    function _setCriteria() {
        $this->_getBaseCriteria();
    }

    function _getClassCriteria() {
        if (empty($this->_included_classes)) return false;
        return "class in (" . join( ", ", $this->_included_classes ) . ")" ;
    }

}
?>
