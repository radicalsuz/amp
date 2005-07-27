<?php

/* * * * * * * *
 * 
 *  AMPSystem_Data_Set
 *
 *  Data Source for List Operations
 *
 *
 *  AMP 3.5.0
 *  2005-07-04
 *  Author: austin@radicaldesigns.org
 *
 * * **/

 require_once ('AMP/System/Data/Data.inc.php');

 class AMPSystem_Data_Set extends AMPSystem_Data {

    var $sort = array();
    var $limit;
    var $offset;
    
    var $source;

    function AMPSystem_Data_Set ( &$dbcon ) {
        $this->init($dbcon);
    }

    //Data Management Functions

    function readData() {
        $sql = $this->_assembleSQL();
        if (isset($_REQUEST['debug'])) print get_class($this).':<BR>'.$sql.'<P>';
        if ($this->source = $this->dbcon->CacheExecute($sql)) {
            return true;
        }

        trigger_error ( get_class( $this ) . ' failed to get data : ' . $this->dbcon->ErrorMsg() );
        return false;
        
    }

    function getData() {
        if (!$this->hasData()) return false;
        return $this->source->FetchRow();
    }

    function isReady() {
        return $this->makeReady();
    }

    function makeReady() {
        if (!$this->hasData()) return false;
        $this->source->MoveFirst();
        return true;
    }

    function setSort( $expression_set ) {
        if (!(is_array($expression_set) || is_string($expression_set)) ) return false;
        $this->sort = array();

        if (is_string($expression_set)) return $this->addSort( $expression_set, false );

        foreach ($expression_set as $exp) {
            $this->addSort ( $exp, false );
        }
    }

    function addSort ( $exp, $primary = true ) {
        if (!is_string($exp)) return false;
        if (array_search( $exp, $this->sort )!==FALSE) return true;

        if ($primary) return array_unshift( $this->sort, $exp );

        return ( $this->sort[] = $exp);
        return true;
    }

    function getSort() {
        if (empty($this->sort)) return false;
        reset ($this->sort);
        return current( $this->sort );
    }


    function _assembleSQL() {
        $sql  = $this->_makeSelect();
        $sql .= $this->_makeSource();
        $sql .= $this->_makeCriteria();
        $sql .= $this->_makeSort();
        $sql .= $this->_makeLimit();
        return $sql;
    }

    function deleteData($id) {
        $sql = "DELETE" . $this->_makeSource(). " where id = ".$id;
        if($this->dbcon->Execute($sql)) {
            return true;
        }

        return false;
        
    }

    function _makeSort() {
        if (empty($this->sort)) return false;
        return " ORDER BY ". join(", ", $this->sort);
    }

    function _makeLimit() {
        if (!isset($this->limit)) return false;
        return " LIMIT " .$this->_buildLimit();
    }

    function _buildLimit() {
        if (!isset($this->offset)) return $this->limit;
        return $this->offset . ', ' . $this->limit;
    }

    function setLimit( $qty ) {
        $this->limit = $qty;
    }

    function setOffset( $offset ) {
        $this->offset = $offset;
    }

    function getGroupedIndex($column) {
        $sql = "SELECT $column, count(" . $this->id_field . ") as qty FROM "
            . $this->datatable . $this->_makeCriteria() . " GROUP BY $column";
        if (isset($_GET['debug'])) AMP_DebugSQL( $sql, get_class( $this ));
        return $this->dbcon->CacheGetAssoc($sql);
    }

    function RecordCount() {
        if (!isset($this->source)) return false;
        return $this->source->RecordCount();
    }

    function NoLimitRecordCount() {
        $sql = "SELECT count(" . $this->id_field . ") as qty from "
            . $this->datatable . $this->_makeCriteria();
        $set = $this->dbcon->Execute( $sql );
        return $set->Fields( 'qty' );
    }

	function getLookup($field) {
		if(!$this->hasData) {
			$sql = "SELECT " . $this->id_field . ", $field " . $this->_makeSource()
            . $this->_makeCriteria();
			$set = $this->dbcon->CacheGetAssoc( $sql );
		} else {
			while($record = $this->getData()) {
				$set[$record['id']] = $record[$field];
			}
		}

		return $set;
	}
 }

 ?>
