<?php

/* * * * * * * *
 * 
 *  AMPSystem_Data
 *
 *  Base Class for Data Models
 *
 *
 *  AMP 3.5.0
 *  2005-07-04
 *  Author: austin@radicaldesigns.org
 *
 * * **/
require_once( 'AMP/System/Config.inc.php');

class AMPSystem_Data {

    var $dbcon;
    var $datatable;
    var $source;

    var $sql_criteria = array();
    var $_sql_select = array();

    var $_nativeColumns = array( );
    var $id_field = "id";
    var $name_field = "name";

    var $_debug_constant = 'AMP_DISPLAYMODE_DEBUG';
    var $_debug_cache_constant = 'AMP_DISPLAYMODE_DEBUG_CACHE';

    var $errors = array();

    function AMPSystem_Data( &$dbcon ) {
        $this->init ( $dbcon );
    }

    function init( &$dbcon ) {
        $this->dbcon = &$dbcon;
        $this->setSource( $this->datatable );

        if (method_exists( $this, '_register_criteria_dynamic' )) {
            $this->_register_criteria_dynamic();
        }
    }

    function setSource( $sourcename = null ) {
        if (!isset($sourcename)) return false;
        if (!$cols = $this->_getColumnNames( $sourcename )) return false;
        $this->datatable = $sourcename;
        $this->_nativeColumns = $cols;
    }

    function setSelect( $expression_set ) {
        if (!is_array($expression_set)) return false;
        $this->_sql_select = array();
        foreach ($expression_set as $exp) {
            $this->addSelectExpression( $exp );
        }
    }
    function setCriteria( $expression_set ) {
        if (!is_array($expression_set)) return false;
        $this->sql_criteria = array();
        foreach ($expression_set as $exp) {
            $this->addCriteria ( $exp );
        }
    }

    function getCriteria () {
        if (empty($this->sql_criteria)) return false;
        return $this->sql_criteria;
    }

    function addColumn ( $name ) {
        if (!isColumn( $name )) return false;
        return $this->addSelectExpression( $name );
    }

    function isColumn( $exp ) {
        if ( empty( $this->_nativeColumns ) && !isset( $this->datatable )) return false;
        if ( empty( $this->_nativeColumns )) {
            $this->setSource( $this->datatable );
            if ( empty( $this->_nativeColumns )) return false;
        }
        if (array_search( $exp, $this->_nativeColumns ) === FALSE) return false;
        return true;
    }

    function addSelectExpression( $exp ) {
        if (!is_string($exp)) return false;
        if (array_search( $exp, $this->_sql_select )!==FALSE) return true;
        $this->_sql_select[] = $exp;
        return true;
    }
    function addCriteria( $exp ) {
        if (!is_string($exp)) return false;
        if (array_search( $exp, $this->sql_criteria )!==FALSE) return true;
        $this->sql_criteria[] = $exp;
        return true;
    }

    function hasData() {
        if (empty($this->source)) return false;
        if (method_exists($this->source, "RecordCount")) return $this->source->RecordCount();
        return true;
    }

    function _assembleSQL( $criteria = null ) {
        $sql  = $this->_makeSelect();
        $sql .= $this->_makeSource();
        $sql .= isset( $criteria ) ? 
                    ' WHERE ' . $criteria 
                    : $this->_makeCriteria();
        return $sql;
    }

    function _makeSelect( ) {
        $output  = "Select ";
        if (empty($this->_sql_select)) return $output . "*";
        return $output . join(", ", $this->_sql_select);
    }

    function _makeSource( ) {
        if ($this->datatable) return " FROM " . $this->datatable;

        trigger_error ("No datatable set in ". get_class($this));
        return false;
    }

    function _makeCriteria() {
        if (empty($this->sql_criteria)) return false;
        return ' WHERE ' . join( " AND ", $this->sql_criteria );
    }

    function _getColumnNames( $sourceDef ) {
        $reg = &AMP_Registry::instance();
        $definedSources = &$reg->getEntry( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS );
        if (isset($definedSources[ $sourceDef ])) return $definedSources[ $sourceDef ];

        if ( !isset( $this->dbcon )) trigger_error( get_class( $this ));
        $colNames = $this->dbcon->MetaColumnNames( $sourceDef );
        $definedSources[ $sourceDef ] = $colNames;
        $reg->setEntry( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS, $definedSources );
        return $colNames;
    }

    function addError( $error ) {
        $this->errors[] = $error;
    }

    function getErrors() {
        return join("<BR>" , $this->errors);
    }

    function _setSourceIncrement( $new_value ){
        if ( $lowest_id = lowerlimitInsertID($this->datatable, $new_value)) {
            $this->dbcon->Execute( "ALTER TABLE ".$this->datatable." AUTO_INCREMENT = ".$lowest_id);
        }

    }

    function notify( $action ){
        foreach( $this->_observers as $observer ){
            $observer->update( $this, $action );
        }
    }

    function addObserver( &$observer, $observer_key = null ){
        if ( isset( $observer_key )){
            $this->_observers[$observer_key] = &$observer;
            return;
        }
        $this->_observers[] = &$observer;
    }


}
?>
