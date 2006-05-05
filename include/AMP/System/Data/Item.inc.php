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
    var $_class_name;

    var $_sort_property;
    var $_sort_direction = AMP_SORT_ASC;
    var $_sort_method = "";

    var $_observers = array( );
    var $_search_source;
    var $_field_status = 'publish';

    function AMPSystem_Data_Item ( &$dbcon ) {
        $this->init($dbcon);
    }

    function init ( &$dbcon, $item_id = null ) {
        $this->dbcon = & $dbcon;
        $this->setSource( $this->datatable );
        if (isset($item_id) && $item_id) $this->readData( $item_id );
    }

    function setSource( $sourcename ){
        PARENT::setSource( $sourcename );
        $this->_itemdata_keys = $this->_getColumnNames( $this->datatable );
		$this->_allowed_keys = $this->_itemdata_keys;
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

        if (defined( $this->_debug_constant ) && constant( $this->_debug_constant )) AMP_DebugSQL( $sql, get_class($this) . ' read'); 

        if ( $itemdata = $this->dbcon->CacheGetRow( $sql )) {
            $this->setData( $itemdata );
            $this->_afterRead( );
            return true;
        }

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

    function delete( ){
        if ( !isset( $this->id )) return false;
        if ( !$this->deleteData( $this->id )) return false;

        $this->notify( 'delete');
        return true;
        
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
        $item_data = $this->getData( );
        $save_fields = array_combine_key($this->_itemdata_keys, $item_data );
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
            $this->_afterSave();
            $this->notify( 'save' );
            return true;
        }
        trigger_error ( get_class( $this ) . ' save failed: '. $this->dbcon->ErrorMsg() );

        return false;
    }

    function _afterSave( ){
        //interface
    }

    function clearItemCache( $id ) {
        $sql = $this->_assembleSqlByID( $id );
        $this->dbcon->CacheFlush( $sql );
        if (defined( $this->_debug_cache_constant ) && constant( $this->_debug_cache_constant )) AMP_DebugSQL( $sql, get_class($this)." cleared cache"); 
        $data_set = &$this->_getSearchSource( );
        $data_set->clearCache( );
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


    //{{{ debug methods: debugSave, debug_updateSQL, debug_insertSQL

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
        #if ( !is_array( $data )) return false;

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
    //}}}

    //{{{ Search methods: search

    function search( $criteria = null, $class_name = null ){
        $data_set = &$this->_getSearchSource( $criteria );
        if ( !$data_set->readData( )) return false;
        if ( !isset( $class_name )) $class_name = $this->_class_name;
        $result_set = &$data_set->instantiateItems( $data_set->getArray( ), $class_name );
        if ( empty( $result_set )) return $result_set;
        $this->sort( $result_set );
        return $result_set;
        
    }

    function &_getSearchSource( $criteria = null ){
        if ( isset( $this->_search_source )) return $this->_search_source;
        require_once( 'AMP/System/Data/Set.inc.php');
        $data_set = &new AMPSystem_Data_Set( $this->dbcon );
        $data_set->setSource( $this->datatable );
        if ( isset( $criteria )) {
            foreach( $criteria as $crit_phrase ){
                $data_set->addCriteria( $crit_phrase );
            }
        }
        $this->_search_source = &$data_set;
        return $this->_search_source;

    }
    //}}}

    //{{{ Sorting methods: sort, setSortMethod, _sort_default

    function sort( &$item_set, $sort_property=null, $sort_direction = null ){
        if ( !isset( $sort_property)) {
            $this->_sort_default( $item_set );
            return true;
        }

        if ( !$this->setSortMethod( $sort_property )) {
            trigger_error( 'sort by '.$sort_property.' failed in '.get_class( $this ).": no access method found" );
            return false;
        }

        if ( isset( $sort_direction ))  $this->_sort_direction = $sort_direction;

        usort( $item_set, array( $this ,'_sort_compare'));
        return true;

    }

    function _sort_compare( $file1, $file2 ) {
        if ( !( $sort_method = $this->_sort_accessor )) return 0;
        if ( $this->_sort_direction == AMP_SORT_DESC )
            return strnatcasecmp( $file2->$sort_method( ) , $file1->$sort_method( ) ); 
        return strnatcasecmp( $file1->$sort_method( ) , $file2->$sort_method( ) );
        #    return ( $file1->$sort_method( ) < $file2->$sort_method( ) ) ? 1 : -1; 
        #return ( $file1->$sort_method( ) > $file2->$sort_method( ) ) ? 1 : -1; 
    }

    function setSortMethod( $sort_property ) {
        $access_method = 'get' . ucfirst( $sort_property );
        if ( !method_exists( $this, $access_method )) return false;
        $this->_sort_accessor = $access_method;
        return true;
    }

    function _sort_default( &$item_set ){
        return $this->sort( $item_set, 'name');
    }
    //}}}

     //{{{ Observer methods: notify, add_observer

    function notify( $action ){
        foreach( $this->_observers as $observer ){
            $observer->update( $this, $action );
        }
    }

    function add_observer( &$observer, $observer_key = null ){
        if ( isset( $observer_key )){
            $this->_observers[$observer_key] = &$observer;
            return;
        }
        $this->_observers[] = &$observer;
    }
    //}}}

     //{{{ Object based Search methods: makeCriteria

    /**
     * makeCriteria 
     * 
     * @param mixed $data 
     * @access public
     * @return void
     */
    function makeCriteria( $data ){
        $return = array( );
        if ( !( isset( $data ) && is_array( $data ))) return false;
        foreach ($data as $key => $value) {
            $crit_method = 'makeCriteria' . ucfirst( $key );

            if (method_exists( $this, $crit_method )) {
                $return[$key] = $this->$crit_method( $value );
                continue;
            }
            if ( $crit_method = $this->_getCriteriaMethod( $key )){
                $return[$key] = $this->$crit_method( $key, $value );
            }

        }
        return $return;
    }
    function _getCriteriaMethod( $fieldname ) {
        if ( !$this->isColumn( $fieldname )) return false;
        if (array_search( $fieldname, $this->_exact_value_fields ) !==FALSE) return '_makeCriteriaEquals';
        return '_makeCriteriaContains';
    }

    function _makeCriteriaContains( $key, $value ) {
        $dbcon = &AMP_Registry::getDbcon( );
        return $key . ' LIKE ' . $dbcon->qstr( '%' . $value . '%' );
    }

    function _makeCriteriaEquals( $key, $value ) {
        $dbcon = &AMP_Registry::getDbcon( );
        return $key . ' = ' . $dbcon->qstr( $value );
    }
    
    //}}}

    //{{{ Status methods: isLive, publish, unpublish
    
    function isLive() {
        if (!$this->isColumn( $this->_field_status )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_STATUS_FIELD_MISSING, get_class( $this ), $this->_field_status ));
            return false;
        }
        return ($this->getData( $this->_field_status ) == AMP_CONTENT_STATUS_LIVE);
    }

    function getPublish( ){
        return $this->isLive( ) ;
    }

    function publish( ){

        if ( $this->isLive( )) return false;
        $this->mergeData( array( $this->_field_status => AMP_CONTENT_STATUS_LIVE ));
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update');
        $this->notify( 'publish');
        return $result;
    }

    function unpublish( ){
        if ( !$this->isLive( )) return false;
        $this->mergeData( array( $this->_field_status => AMP_CONTENT_STATUS_DRAFT ));
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update');
        $this->notify( 'unpublish');
        return $result;
    }

    function makeCriteriaLive( ){
        return $this->_field_status . '=' . AMP_CONTENT_STATUS_LIVE;
    }

    //}}}
}
?>
