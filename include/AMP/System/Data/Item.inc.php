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

    var $id;

    function AMPSystem_Data_Item ( &$dbcon ) {
        $this->init($dbcon);
    }

    function init ( &$dbcon, $item_id ) {
        $this->dbcon = & $dbcon;
        $this->_itemdata_keys = $this->dbcon->MetaColumnNames( $this->datatable );
        if (isset($item_id) && $item_id) $this->readData( $item_id );
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

    function deleteData( $item_id ) {
        $sql = "Delete from " . $this->datatable . " where ". $this->id_field ." = ". $this->dbcon->qstr( $item_id );
        if ( $itemdata = $this->dbcon->Execute( $sql )) {
            return true;
        }

        return false ;
    }

    function save() {
        $save_fields = array_combine_key($this->_itemdata_keys, $this->getData());
        
        $result = $this->dbcon->Replace( $this->datatable, $save_fields, $this->id_field, $quote=true);

        if ($result == ADODB_REPLACE_INSERTED ) $this->id = $this->dbcon->Insert_ID();
        if ($result) return true;

        return false;
    }

    function setData( $data ) {
        $this->itemdata = array_combine_key( $this->_itemdata_keys, $data );
        if (method_exists( $this, 'adjustSetData' ) ) $this->adjustSetData( $data );
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

}
?>
