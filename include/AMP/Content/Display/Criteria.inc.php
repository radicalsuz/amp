<?php

class AMPContent_DisplayCriteria {

    var $_include_draft_status = false;
    var $_status_field = "publish";

    var $_excluded_classes = array(
        AMP_CONTENT_CLASS_SECTIONHEADER,
        AMP_CONTENT_CLASS_FRONTPAGE,
        AMP_CONTENT_CLASS_USERSUBMITTED
        );

    function AMPContent_DisplayCriteria() {
        if (AMP_CONTENT_CLASS_SECTIONFOOTER) $this->_excluded_classes[] = AMP_CONTENT_CLASS_SECTIONFOOTER;
    }

    function clean( &$contentSource ) {
        $contentSource->addCriteria( $this->_getClassCriteria() );
        $this->cleanStatus( $contentSource );
        $this->cleanPermissions( $contentSource );
    }

    function cleanPermissions( &$contentSource ){
        if ( AMP_Authenticate( 'content')) return;

        if ( method_exists( $contentSource, 'addCriteriaPublic' )) $contentSource->addCriteriaPublic( );
    }

    function cleanStatus( &$contentSource ) {
        if ( method_exists( $contentSource, 'addCriteriaStatus' )) {
            return $contentSource->addCriteriaStatus( AMP_CONTENT_STATUS_LIVE );
        }
        
        $contentSource->addCriteria( $this->_getStatusCriteria() );
    }

    function allowClass( $class_id ) {
        if (empty($this->_excluded_classes)) return true;
        $classKey = array_search( $class_id, $this->_excluded_classes );
        if ($classKey === FALSE ) return true;
        unset( $this->_excluded_classes[ $classKey ] );
    }

    function _getClassCriteria() {
        if (empty($this->_excluded_classes )) return false;
        return "class not in (" . join( ", ", $this->_excluded_classes ) . ")" ;
    }

    function _getStatusCriteria() {
        if ( $this->_includeDraftStatus() ) return false;
        $status_field = $this->_getStatusField(); 
        return "$status_field=" . AMP_CONTENT_STATUS_LIVE;
    }

    function _getStatusField() {
        return $this->_status_field;
    }

    function _includeDraftStatus() {
        return $this->_include_draft_status;
    }

    function setStatusField( $fieldname ) {
        $this->_status_field = $fieldname;
    }

    function setIncludeDraft( $new_value = true ) {
        $this->_include_draft_status = $new_value;
    }

}
?>
