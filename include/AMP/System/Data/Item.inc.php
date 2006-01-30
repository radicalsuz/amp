<?php

/* * * * * * * * *
 *
 *  AMPSystem_Data_Item
 *
 *  A base class for retrieving and changing
 *  a single Database record
 *
 *  AMP 3.5.0
 *  2005-07-04
 *  Author: austin@radicaldesigns.org
 *
 * * * * * **/

 require_once ( 'AMP/System/Data/Data.inc.php' );

class AMPSystem_Data_Item extends AMPSystem_Data {

    var $dbcon;

    var $itemdata;
    var $_itemdata_keys;
	var $_allowed_keys;

    var $id;

    function AMPSystem_Data_Item ( &$dbcon ) {
        $this->init($dbcon);
    }

    function init ( &$dbcon, $item_id = null ) {
        $this->dbcon = & $dbcon;
        $this->_itemdata_keys = $this->_getColumnNames( $this->datatable );
		$this->_allowed_keys = $this->_itemdata_keys;
        if (isset($item_id) && $item_id) $this->readData( $item_id );
    }

	function _addAllowedKey( $key_name ) {
		if (array_search( $key_name, $this->_allowed_keys )!==FALSE) return true;
		$this->_allowed_keys[] = $key_name;
	}

    function dropID( ){
        unset ( $this->_itemdata_keys[ $this->id_field ] );
        unset ( $this->id );
    }

    function _beforeRead( $item_id ){
        if ( $item_id !== FALSE ) $this->addCriteriaId( $item_id );
    }

    function addCriteriaId( $item_id ){
        $this->addCriteria( $this->id_field." = ".$this->dbcon->qstr( $item_id ) );
    }
		

    function readData ( $item_id ) {
        $this->_beforeRead( $item_id );
        $sql = $this->_assembleSQL();

        if ( $itemdata = $this->dbcon->CacheGetRow( $sql )) {
            $this->setData( $itemdata );
            $this->_afterRead( );
            return true;
        }

        if (defined( $this->_debug_constant ) && constant( $this->_debug_constant )) AMP_DebugSQL( $sql, get_class($this)); 

        if ($this->dbcon->ErrorMsg() ) trigger_error ( get_class( $this ) . ' failed to read the database :' . $this->dbcon->ErrorMsg() );
        return false;
    }

    function _afterRead( ){
        //interface
    }

    function hasData() {
        return (isset( $this->itemdata) && !empty($this->itemdata));
    }

    function deleteData( $item_id ) {
        $sql = "Delete from " . $this->datatable . " where ". $this->id_field ." = ". $this->dbcon->qstr( $item_id );
        if ( $itemdata = $this->dbcon->Execute( $sql )) {
            $cached_sql = $this->_assembleSqlByID( $item_id );
            $this->dbcon->CacheFlush( $cached_sql ) ;
            if (defined( $this->_debug_cache_constant ) && constant( $this->_debug_cache_constant )) {
                AMP_DebugSQL( $cached_sql, get_class($this)." cleared cache"); 
            }
            return true;
        }

        return false ;
    }

    function _assembleSqlByID( $id ) {
         return $this->_makeSelect().
                $this->_makeSource().
                " WHERE ".$this->id_field." = ". $this->dbcon->qstr( $id );
    }

    function _blankIdAction( ){
        //interface
    }


    function save() {
        $save_fields = array_combine_key($this->_itemdata_keys, $this->getData());
		if ( !is_array( $this->id_field ) && !isset( $save_fields[ $this->id_field ] )) {
            $save_fields[ $this->id_field ] = "";
            $this->_blankIdAction();
        }
        
        $result = $this->dbcon->Replace( $this->datatable, $save_fields, $this->id_field, $quote=true);

        if ($result == ADODB_REPLACE_INSERTED ) {
            $this->mergeData( array( $this->id_field => $this->dbcon->Insert_ID() ));
        }
        
        if ($result) {
            $this->clearItemCache( $this->id );
            if (method_exists( $this, '_afterSave' )) $this->_afterSave();
            return true;
        }
        trigger_error ( get_class( $this ) . ' save failed: '. $this->dbcon->ErrorMsg() );

        return false;
    }

    function clearItemCache( $id ) {
        $sql = $this->_assembleSqlByID( $id );
        $this->dbcon->CacheFlush( $sql );
        if (defined( $this->_debug_cache_constant ) && constant( $this->_debug_cache_constant )) AMP_DebugSQL( $sql, get_class($this)." cleared cache"); 
    }

    function mergeData( $data ) {
        $this->itemdata = array_merge( $this->itemdata, array_combine_key( $this->_allowed_keys, $data ));
        if (method_exists( $this, '_adjustSetData' ) ) $this->_adjustSetData( $data );
        if (isset($data[$this->id_field]) && $data[$this->id_field]) $this->id = $data[$this->id_field];
    }

    function setData( $data ) {
        $this->itemdata = array_combine_key( $this->_allowed_keys, $data );
        if (method_exists( $this, '_adjustSetData' ) ) $this->_adjustSetData( $data );
        if (is_string( $this->id_field ) && isset($data[$this->id_field]) && $data[$this->id_field]) $this->id = $data[$this->id_field];
    }

    function legacyFieldname( $data, $oldname, $newname ) {
        if (isset($data[$oldname])) $this->itemdata[$newname] = $data[$oldname];
        if (isset($data[$newname])) {
            $this->itemdata[$newname] = $data[$newname];
            $this->itemdata[$oldname] = $data[$newname];
        }
    }

    function getData( $fieldname = null ) {
        if (!isset($fieldname)) return $this->itemdata;
        if (isset($this->itemdata[$fieldname])) return $this->itemdata[$fieldname];

        return false;
    }

    function getName() {
        if (!isset($this->name_field)) return;
        return $this->getData( $this->name_field );
    }

	function existsValue($column, $value) {
		$records = $this->dbcon->Execute('SELECT * FROM '.$this->datatable
										.' WHERE '.$column.' = '. $this->dbcon->qstr($value));
		if(false != $records && $records->RecordCount() != 0) {
			return true;
		}

		return false;
	}

    function debugSave() {
        $save_sql = $this->id ? $this->debug_updateSQL():
                                $this->debug_insertSQL();

        $rs = $this->dbcon->CacheExecute( $save_sql ) or
                    die( "Unable to save " . get_class( $this) . " data using SQL $save_sql: " . $this->dbcon->ErrorMsg() );

        if ($rs) {
            if (!$this->id) $this->id = $this->dbcon->Insert_ID();
            return true;
        }

        return false;
    }

    function debug_updateSQL ( ) {
        $data = $this->itemdata;

        $dbcon =& $this->dbcon;

        $sql = "UPDATE " . $this->datatable . " SET ";

        $save_fields = $this->_itemdata_keys;

        foreach ($save_fields as $field) {
            $elements[] = $field . "=" . $dbcon->qstr( $data[$field] );
        }

        $sql .= implode( ", ", $elements );
        $sql .= " WHERE ". $this->id_field . "=" . $dbcon->qstr( $this->id );

        return $sql;

    }

    function debug_insertSQL ( ) {

        $dbcon =& $this->dbcon;
        $data = $this->itemdata;

        $fields = $this->_itemdata_keys;
        $values_noescape = array_values( $data );

        foreach ( $fields as $field ) {
            $value = $data[$field];
            $values[] = $dbcon->qstr( $value );
        }

        $sql  = "INSERT INTO " . $this->datatable . "(";
        $sql .= join( ", ", $fields ) .
                ") VALUES (" .
                join( ", ", $values ) .
                ")";

        return $sql;

    }

}
?>
