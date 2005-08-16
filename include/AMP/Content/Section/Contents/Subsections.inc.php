<?php

#require_once( 'AMP/Content/Section/Contents/Source.inc.php' );

class SectionContentSource_Subsections extends SectionContentSource {

    var $_base_sort = array(
        'date2 desc',
        );
    var $_status_field = 'usenav'; 

    function SectionContentSource_Subsections( &$section ) {
        $this->init( $section );
    }

    function _setSource() {
        $this->_source = &new SectionSet( $this->_section->dbcon );
    }

    function _setCriteria() {
        $this->_source->addCriteria( $this->_getSectionCriteria() );
        $this->_source->addCriteria( $this->_getStatusCriteria() );
    }

    function _getSectionCriteria() {
        return "parent=". $this->_section->id;
    }

    function _setSort() {
        $this->_source->setSort( $this->_base_sort);
        $this->_source->addSort(
        "if(isnull(textorder) or textorder='', ". AMP_CONTENT_LISTORDER_MAX .", textorder) ASC");
    }
}
?>
