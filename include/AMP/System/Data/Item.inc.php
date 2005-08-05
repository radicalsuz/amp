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

    function init ( &$dbcon, $item_id ) {
        $this->dbcon = & $dbcon;
        $this->_itemdata_keys = $this->dbcon->MetaColumnNames( $this->datatable );
		$this->_allowed_keys = $this->_itemdata_keys;
        if (isset($item_id) && $item_id) $this->readData( $item_id );
    }

	function _addAllowedKey( $key_name ) {
		if (array_search( $key_name, $this->_allowed_keys )!==FALSE) return true;
		$this->_allowed_keys[] = $key_name;
	}
		

    function readData ( $item_id ) {
        $sql = "Select * from ".$this->datatable." where ".$this->id_field." = ". $this->dbcon->qstr( $item_id );

        if ( $itemdata = $this->dbcon->CacheGetRow( $sql )) {
            $this->setData( $itemdata );
            return true;
        }
        if (isset($_REQUEST['debug'])) print get_class($this) .": ". $sql .'<BR><BR>';

        trigger_error ( get_class( $this ) . ' failed to read the database :' . $this->dbcon->ErrorMsg() );
        return false;
    }

    function hasData() {
        return (isset( $this->itemdata) && !empty($this->itemdata));
    }

    function deleteData( $item_id ) {
        $sql = "Delete from " . $this->datatable . " where ". $this->id_field ." = ". $this->dbcon->qstr( $item_id );
        if ( $itemdata = $this->dbcon->Execute( $sql )) {
            return true;
        }

        return false ;
    }

    function save() {
        $save_fields = array_combine_key($this->_itemdata_keys, $this->getData());
		if (!isset($save_fields[ $this->id_field ])) $save_fields[ $this->id_field ] = "";
        
        $result = $this->dbcon->Replace( $this->datatable, $save_fields, $this->id_field, $quote=true);

        if ($result == ADODB_REPLACE_INSERTED ) $this->id = $this->dbcon->Insert_ID();
        
        if ($result) {
            if (method_exists( $this, '_afterSave' )) $this->_afterSave();
            return true;
        }
        trigger_error ( get_class( $this ) . ' save failed: '. $this->dbcon->ErrorMsg() );

        return false;
    }

    function mergeData( $data ) {
        $this->itemdata = array_merge( $this->itemdata, array_combine_key( $this->_itemdata_keys, $data ));
        if (method_exists( $this, '_adjustSetData' ) ) $this->_adjustSetData( $data );
        if (isset($data['id']) && $data['id']) $this->id = $data['id'];
    }

    function setData( $data ) {
        $this->itemdata = array_combine_key( $this->_itemdata_keys, $data );
        if (method_exists( $this, '_adjustSetData' ) ) $this->_adjustSetData( $data );
        if (isset($data['id']) && $data['id']) $this->id = $data['id'];
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
        $sql .= " WHERE id=" . $dbcon->qstr( $this->id );

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