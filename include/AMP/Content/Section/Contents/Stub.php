<?php

require_once( 'AMP/Content/Section/Contents/Articles.inc.php');

class SectionContentSource_Stub extends SectionContentSource_Articles {

    function SectionContentSource_Stub( &$section ) {
        $this->__construct( $section );
    }

    function __construct( &$section ) {
        $this->_section = &$section;
    }

    function _setBaseCriteria() {
    }

}

?>
