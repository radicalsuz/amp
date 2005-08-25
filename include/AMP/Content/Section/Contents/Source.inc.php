<?php

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

    function _includeDraftStatus() {
        return $this->_include_draft_status;
    }


    function _getStatusCriteria() {
        if ( $this->_includeDraftStatus() ) return false;
        $status_field = $this->_getStatusField(); 
        return "$status_field=" . AMP_CONTENT_STATUS_LIVE;
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