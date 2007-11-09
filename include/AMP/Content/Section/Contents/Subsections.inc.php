<?php

require_once( 'AMP/Content/Section/Set.inc.php' );
require_once( 'AMP/Content/Section/Contents/Source.inc.php');

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
        $this->_addCriteriaSection( );
        #$this->_source->addCriteria( $this->getSectionCriteria() );
        $this->_display_crit_source->cleanStatus( $this->_source );
    }

    function _addCriteriaSection( ){
        $crit = $this->_section->makeCriteria( $this->_section->getDisplayCriteria( ));
        foreach( $crit as $crit_item ) {
            $this->_source->addCriteria( $crit_item );
        }
    }

    function getSectionCriteria() {
        //deprecated
        return "parent=". $this->_section->id;
    }

    function _setSort() {
        $this->_source->setSort( $this->_base_sort );
        $this->_source->addSort(
            "if(isnull(textorder), ". AMP_SORT_MAX.", textorder) ASC");
    }
}
?>
