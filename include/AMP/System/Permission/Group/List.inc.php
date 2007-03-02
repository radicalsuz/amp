<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/System/Permission/Group/Set.inc.php' );

class PermissionGroup_List extends AMPSystem_List {

    var $name = "Permission Group";
    var $col_headers = array( 
        'Group' => 'name',
        'ID'    => 'id',
        'Rights'=> '_calculatePermissionsPercentage');
    var $editlink = 'per.php';
    //var $extra_columns = array( 'Rights' => 'rights');
    var $name_field = 'name';
    var $_source_object = 'PermissionGroup';

    function PermissionGroup_List( &$dbcon, $criteria = array( ) ) {
        //$source = & new PermissionGroupSet( $dbcon );
        $this->init( $this->_init_source( $dbcon, $criteria ));
        //$source->addSelectExpression( 'id as rights');
        //$source->readData( );

//        $this->addTranslation( 'rights', '_calculatePermissionsPercentage');

    }

    //function _calculatePermissionsPercentage( $value, $fieldname, $data_array ){
    function _calculatePermissionsPercentage( &$source, $column_name ) {
        $allPermissions = &AMPSystem_Lookup::instance( 'permissionNames');
        $groupPermissions = &AMPSystemLookup_PermissionLevel::instance( $source->id );
        return sprintf( '%d%% allowed', ( count( $groupPermissions) / count( $allPermissions) * 100 ));

    }

    function _HTML_footer( ){
        return 
            $this->_HTML_endList( ).
            $this->_HTML_addLink( ).
            $this->_HTML_detailLink( );
    }

    function _HTML_detailLink( ){
        return $this->_HTML_newline( ). '&nbsp;&nbsp;'.
                $this->_HTML_link( AMP_SYSTEM_URL_PERMISSION_DETAILS, 'Edit Permission Details');
    }

}

?>
