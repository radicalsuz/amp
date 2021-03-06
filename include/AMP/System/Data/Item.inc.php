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

    var $itemdata = array( );
    var $_itemdata_keys;
	var $_allowed_keys;

    var $id;
    var $_class_name;

    var $_sort_property;
    var $_sort_direction = AMP_SORT_ASC;
    var $_sort_method = "";
    var $_sort_auto = true;

    var $_observers = array( );

    var $_search_source;
    var $_search_criteria_global = array( );

    var $_field_status = 'publish';
    var $_field_listorder = 'listorder';

    var $_exact_value_fields = array( );
    var $_allow_db_cache = true;

    //flag for actions based on whether a bulk request is being executed
    var $list_action;

    function AMPSystem_Data_Item ( &$dbcon ) {
        $this->init($dbcon);
    }

    function __construct( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function init ( &$dbcon, $item_id = null ) {
        if ( !is_object( $dbcon )){
            trigger_error( sprintf( AMP_TEXT_ERROR_DATABASE_CONNECTION_BAD, get_class( $this )));
            return false;
        }
        $this->dbcon = & $dbcon;
        $this->setSource( $this->datatable );
        if (isset($item_id) && $item_id) $this->readData( $item_id );
        $this->_after_init( );
    }

    function _after_init( ) {
        //stub
    }

    function read( $item_id ) {
        return $this->readData( $item_id );
    }

    function setSource( $sourcename ) {
        parent::setSource( $sourcename );
        $this->_itemdata_keys = $this->_getColumnNames( $this->datatable );
		$this->_allowed_keys = $this->_itemdata_keys;
    }

	function _addAllowedKey( $key_name ) {
		if (array_search( $key_name, $this->_allowed_keys )!==FALSE) return true;
		$this->_allowed_keys[] = $key_name;
	}

    function getAllowedKeys( ) {
        return $this->_allowed_keys;
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

    function addCriteriaGlobal( $criteria ){
        $this->_search_criteria_global = array_merge( $this->_search_criteria_global, $criteria );
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

        if ($this->dbcon->ErrorMsg() ) 
            trigger_error ( sprintf( AMP_TEXT_ERROR_DATABASE_READ_FAILED, get_class( $this ) , $this->dbcon->ErrorMsg() ));
        return false;
    }

    function _afterRead( ){
        //interface
    }

    function hasData() {
        return (isset( $this->itemdata) && !empty($this->itemdata));
    }

    function deleteById( $item_id ) {
        return $this->deleteData( $item_id );
    }

    function deleteByCriteria( $criteria ) {
        $sql_criteria_set = $this->makeCriteria( $criteria );
        if ( !$sql_criteria_set || empty( $sql_criteria_set ) ) {
            return false;
        }
        $sql_criteria = ' WHERE ' . join( " AND ", $sql_criteria_set ) ;
        $sql = "Delete from " . $this->datatable . $sql_criteria;
        if ( ( $itemdata = $this->dbcon->Execute( $sql )) && $this->dbcon->Affected_Rows( )) {
            return true;
        }
        
        trigger_error ( AMP_TEXT_DELETE . ' ' . sprintf( AMP_TEXT_ERROR_DATABASE_SAVE_FAILED, get_class( $this ), $this->dbcon->ErrorMsg() . ' // ' . $sql  ));
        return false ;
    }

    function get_url_edit( ) {
        return false;
    }

    function deleteData( $item_id ) {
        $sql = "Delete from " . $this->datatable . " where ". $this->id_field ." = ". $this->dbcon->qstr( $item_id );
        if ( ( $itemdata = $this->dbcon->Execute( $sql )) && $this->dbcon->Affected_Rows( )) {
            $cached_sql = $this->_assembleSqlByID( $item_id );
            $this->dbcon->CacheFlush( $cached_sql ) ;
            if (defined( $this->_debug_cache_constant ) && constant( $this->_debug_cache_constant )) {
                AMP_DebugSQL( $cached_sql, get_class($this)." cleared cache"); 
            }
            return true;
        } 

        trigger_error ( AMP_TEXT_DELETE . sprintf( AMP_TEXT_ERROR_DATABASE_SAVE_FAILED, get_class( $this ), $this->dbcon->ErrorMsg() ));
        return false ;
    }

    function delete( ){
        if ( !isset( $this->id )) return false;
        $this->before_delete( );
        if ( !$this->deleteData( $this->id )) return false;

        $this->notify( 'delete', $this->id );
        return true;
        
    }

    function before_delete( ) {

    }

    function _assembleSqlByID( $id ) {
         return $this->_makeSelect().
                $this->_makeSource().
                " WHERE ".$this->id_field." = ". $this->dbcon->qstr( $id );
    }

    function _blankIdAction( ){
        //interface
    }

    function _save_create_actions( $data ){
        return $data;
    }

    function _save_update_actions( $data ) {
        return $data;
    }


    function save() {
        $item_data = $this->getData( );
        $save_fields = array_combine_key($this->_itemdata_keys, $item_data );
		if ( !is_array( $this->id_field ) && !isset( $save_fields[ $this->id_field ] )) {
            $save_fields[ $this->id_field ] = "";
            $this->_blankIdAction();
        }
        if ( !isset( $this->id )) {
            $save_fields = $this->_save_create_actions(  $save_fields );
        } else {
            $save_fields = $this->_save_update_actions( $save_fields );
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
        $this->addError( sprintf( AMP_TEXT_ERROR_DATABASE_SAVE_FAILED, get_class( $this ), AMP_TEXT_ERROR_DATABASE_PROBLEM ) );
        trigger_error ( sprintf( AMP_TEXT_ERROR_DATABASE_SAVE_FAILED, get_class( $this ), $this->dbcon->ErrorMsg()  ));

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

    function clear_cache( ){
        if ( isset( $this->id )) {
            return $this->clearItemCache( $this->id );
        }
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
		$this->_addAllowedKey($newname);
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

    function getShortName( ) {
        return AMP_trimText( $this->getName( ), 25, false);
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

        foreach ( $fields as $key => $field ) {
            if ( !isset( $data[$field ])) {
                unset( $fields[ $key ]);
                continue;
            }

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
        $data_set = $this->getSearchSource( $criteria );
        if ( !$data_set->readData( )) return false;

        if ( !( isset( $class_name) || isset( $this->_class_name ))) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NO_CLASS_NAME_DEFINED, get_class( $this )) );
        }
        if ( !isset( $class_name )) $class_name = $this->_class_name;
        if ( !$class_name && !isset( $this->_class_name )) {
            trigger_error( "No _class_name assigned for class " . get_class( $this ) . ". search failed");
        }
        $result_set = $data_set->instantiateItems( $data_set->getArray( ), $class_name );
        if ( empty( $result_set )) return $result_set;

        if ( $this->_sort_auto && !$data_set->getSort( )) $this->sort( $result_set );
        return $result_set;
        
    }

    /**
     * Alias for search
     * 
     * @param mixed $criteria 
     * @param mixed $class_name 
     * @access public
     * @return void
     */
    function find( $criteria = null, $class_name = null  ) {
        if( $class_name ) {
            $finder = new $class_name( AMP_dbcon( ));
        } elseif( !isset( $this )) {
            $context = debug_backtrace( );
            trigger_error( 'class name not included for call to '.__FUNCTION__.' on '.$debug_backtrace[0]['class']);
            return false;
        } else {
            $finder = $this;
        }

        $order_by = isset( $criteria['sort']) && $criteria['sort'] ? $criteria['sort'] : false;
        $source_limit = isset( $criteria['limit']) && $criteria['limit'] ? $criteria['limit'] : false;
        $source_offset = isset( $criteria['offset']) && $criteria['offset'] ? $criteria['offset'] : false;

        if ( $order_by || $source_limit || $source_offset ) {
            $source = &$finder->_getSearchSource( );
            if( $order_by ) {
                $source->setSort( $order_by );
            }
            if ( $source_limit ) {
                $source->setLimit( $source_limit );
            }
            if ( $source_offset ) {
                $source->setOffset( $source_offset );
            }
        }
        return $finder->search( $finder->makeCriteria( $criteria ), $class_name );
    }

    function &getSearchSource( $criteria = null ){
        $result = &$this->_getSearchSource( $criteria );
        return $result;
    }

    function &_getSearchSource( $criteria = null ){
        if ( isset( $this->_search_source ) && $this->_search_source ) {
            if ( !isset( $criteria )) return $this->_search_source;
            $data_set = &$this->_search_source;
        } else {
            require_once( 'AMP/System/Data/Set.inc.php' );
            $data_set = &new AMPSystem_Data_Set( $this->dbcon );
            $data_set->setSource( $this->datatable );
        }
        if ( isset( $criteria )) {
            $data_set->setCriteria( $criteria );
            /*
            foreach( $criteria as $crit_phrase ){
                $data_set->addCriteria( $crit_phrase );
            }
            */
        }
        foreach( $this->_search_criteria_global as $crit_phrase ){
            $data_set->addCriteria( $crit_phrase );
        }
        if ( !$this->_allow_db_cache ) $data_set->clearCache( );
        
        $this->_search_source = &$data_set;
        return $this->_search_source;

    }

    //}}}

    //{{{ Sorting methods: sort, setSortMethod, _sort_default

    function sort( &$item_set, $sort_property=null, $sort_direction = null ){
        if ( !( isset( $sort_property) && $sort_property )) {
            $this->_sort_default( $item_set );
            return true;
        }

        if ( !$this->setSortMethod( $sort_property )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_SORT_PROPERTY_FAILED, $sort_property, get_class( $this )));
            return false;
        }

        if ( isset( $sort_direction ))  $this->_sort_direction = $sort_direction;

        uasort( $item_set, array( $this, '_sort_compare' ));
        return true;

    }

    function _sort_compare( $file1, $file2 ) {
        if ( !( $sort_method = $this->_sort_accessor )) return 0;

        if ( !is_object( $file2)) {
            return 0;
        }

        //sort descending
        if ( $this->_sort_direction == AMP_SORT_DESC ) {
            return strnatcasecmp( $file2->$sort_method( ) , 
                                    $file1->$sort_method( ) ); 
        }

        //sort ascending
        return strnatcasecmp( $file1->$sort_method( ) , $file2->$sort_method( ) );
    }

    function setSortMethod( $sort_property ) {
        $access_method = 'get' . AMP_to_camelcase( $sort_property );
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
            $crit_method1 = 'makeCriteria' . ucfirst( $key );
            $crit_method2 = 'makeCriteria' . AMP_to_camelcase( $key );
            $crit_method = ( method_exists( $this, $crit_method1)) ? $crit_method1 : $crit_method2;

            if (method_exists( $this, $crit_method )) {
                $return[$key] = $this->$crit_method( $value );
                continue;
            }
            if ( $crit_method = $this->_getCriteriaMethod( $key, $value )){
                $return[$key] = $this->$crit_method( $key, $value );
            }

        }
        return array_filter( $return );
    }
    function _getCriteriaMethod( $fieldname, $value  ) {
        if ( !$this->isColumn( $fieldname )) return false;
        if (array_search( $fieldname, $this->_exact_value_fields ) !==FALSE) return '_makeCriteriaEquals';
        if ( is_numeric( $value )) return '_makeCriteriaEquals';
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
            trigger_error( sprintf( AMP_TEXT_ERROR_REQUIRED_FIELD_MISSING, get_class( $this ), $this->_field_status ));
            return false;
        }
        return ($this->getData( $this->_field_status ) == AMP_CONTENT_STATUS_LIVE);
    }

    function getPublish( ){
        return $this->isLive( ) ;
    }

    function getStatus( ) {
        if (!$this->isColumn( $this->_field_status )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_REQUIRED_FIELD_MISSING, get_class( $this ), $this->_field_status ));
            return false;
        }
        return $this->getData( $this->_field_status );
    }

    function getStatusText( ){
        $status_options = AMP_lookup( 'status' );
        $status_value = $this->getStatus( ) ;
        if ( !( $status_value && isset( $status_options[$status_value]))) return AMP_TEXT_CONTENT_STATUS_DRAFT;
        return $status_options[ $status_value ];
        //if ( $this->isLive( )) return AMP_TEXT_CONTENT_STATUS_LIVE;
        //return AMP_TEXT_CONTENT_STATUS_DRAFT; 
    }

    function publish( ){

        if ( $this->isLive( )) return false;
        $this->mergeData( array( $this->_field_status => AMP_CONTENT_STATUS_LIVE ));
        if ( !isset( $this->id )) return true;
        $this->list_action= 'publish';

        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update');
        $this->notify( 'publish');
        return $result;
    }

    function unpublish( ){
        if ( $this->getStatus( ) == AMP_CONTENT_STATUS_DRAFT ) return false;
        $this->mergeData( array( $this->_field_status => AMP_CONTENT_STATUS_DRAFT ));
        if ( !isset( $this->id )) return true;
        $this->list_action= 'unpublish';

        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update');
        $this->notify( 'unpublish');
        return $result;
    }

    function makeCriteriaLive( ){
        return $this->_field_status . '=' . AMP_CONTENT_STATUS_LIVE;
    }

    function makeCriteriaPublish( $value ){
        if ( !$value ) return '( isnull( '. $this->_field_status.') OR ' . $this->_field_status . ' = ' . AMP_CONTENT_STATUS_DRAFT . ' )';
        return $this->_field_status . '=' . $value;
    }

    function makeCriteriaId( $value ) {
        if ( !$value ) return 'TRUE';
        if ( !is_array( $value )) return $this->_makeCriteriaEquals( 'id', $value );
        return 'id in ( '.join( ',', $value).')';
    }

    function makeCriteriaNotId( $ids ) {
        if ( !$ids ) return "TRUE";
        if ( !( is_array( $ids ))) return 'id != '.$ids;
        return "id not in ( " . join(",", $ids ) . ")";
    }

    //}}}

    function setDefaults( ) {
        //interface
    }

    function getListOrder( ){
        if ( !$this->isColumn( $this->_field_listorder )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_REQUIRED_FIELD_MISSING, get_class( $this ), $this->_field_listorder ));
            return false;
        }
        $result = $this->getData( $this->_field_listorder );
        if ( !$result ) return AMP_SORT_MAX. $this->getName( );
        return $result;
    }

    function getImageRef( ) {
        return false;
    }

    function getBlurb( ) {
        return false;
    }

	function export_keys() {
        return $this->_allowed_keys;
        /*
		if (!is_array($this->itemdata)) return $this->_allowed_keys;
		return array_keys($this->itemdata);
        */
	}

    function before_export( $values ) {
        //stub
        return false;

    }

    function getId( ) {
        return $this->id;
    }

    function get_live_url( $url_type ) {
        return $this->get_constant_url( $url_type, 'AMP_CONTENT_URL_');
    }

    function get_constant_url( $url_type, $interface_type ) {
        $url_constant = strtoupper( $interface_type . $url_type );
        if ( !defined( $url_constant )) return false;
        if ( !( isset( $this->id ) && $this->id )) {
            return constant( $url_constant );
        }
        return AMP_url_update( constant( $url_constant ), array( $this->id_field => $this->id));

    }

    function get_system_url( $url_type ) {
        return $this->get_constant_url( $url_type, 'AMP_SYSTEM_URL_');
    }

    function get_select_from_sort( $sort_sql ) {
        $sort_clauses = preg_split( '/\s?,\s?/', $sort_sql );

        if ( count( $sort_clauses) === 1 ) {
            return str_replace( AMP_SORT_DESC, '', $sort_sql );
        }

        $previous_sort_segment = false;
        $delimiter= "''";
        foreach( $sort_clauses as $sort_segment ) {
            if ( $previous_sort_segment ) {
                $delimiter = 'if ( isnull( '.$previous_sort_segment.'), \'\', \', \')';
            } 

            $index_segment = str_replace( AMP_SORT_DESC, '', $sort_segment );
            $local_method = 'make_readable_select_for_'. AMP_from_camelcase( $index_segment );

            if ( method_exists( $this, $local_method )) {
                $index_segment = $this->$local_method( ) ;
            }
            $index_sort[] = '( if( isnull( '.$index_segment.'), \'\', Concat( '.$delimiter.','.$index_segment.')))';

            //after first time through loop all segments need leading punctuation
            $previous_sort_segment = $index_segment;
        }

        return "Concat( ".join( ',', $index_sort )." ) as index_field";
    }

    function make_readable_select_for_publish( ) {
        return 'if ( isnull( publish ) or publish = \'\' or publish=0, \''.AMP_PUBLISH_STATUS_DRAFT.'\', \''.AMP_PUBLISH_STATUS_LIVE.'\' )';
    }

    function getListNameSuffix( ) {
        return false;
    }

    function create( $attributes = array( ), $class_name = null ) {
        if( !$class_name ) {
            $context = debug_backtrace( );
            trigger_error( 'class name not included for call to '.__FUNCTION__.' on '.$debug_backtrace[0]['class']);
            return false;
        }

        $item = new $class_name( AMP_dbcon( ));
        $item->setDefaults( );
        $item->mergeData( $attributes );
        return $item;
    }


    function update_all( $action, $criteria = array( )){
        if( !is_array( $action )) {
            $action = array( $action );
        }
        $search = &$this->getSearchSource( );

        if( is_array( $criteria )) {
            $scope = join( ' AND ', $this->makeCriteria( $criteria )) ;
        } else {
            $scope = $criteria;
        }

        return $search->updateData( $action, $scope );
    }


}
?>
