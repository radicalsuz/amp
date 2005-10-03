<?php
require_once( 'AMP/Content/Display/Criteria.inc.php' );

class SectionContentSource {

    var $_source;
    var $_section;
    var $_status_field = "publish";
    var $_include_draft_status = false;

    function SectionContentSource() {
        //interface - do -not- use
    }

    function init( &$section ) {
        $this->_section = &$section;
        $this->_display_crit_source = &new AMPContent_DisplayCriteria();
        $this->_display_crit_source->setStatusField( $this->_status_field );
    }

    function execute() {
        $this->_setSource();
        $this->_setCriteria();
        $this->_setSort();
        return $this->_source;
    }

    ###################################
    ### private data source methods ###
    ###################################

    function _setBaseSort() {
        $this->_source->setSort( $this->_base_sort );
    }

    function _getStatusField() {
        return $this->_status_field;
    }

    function _setSource() {
        //interface
    }

    function _setCriteria() {
        //interface
    }

    function _setSort () {
        //interface
    }
}
?>
