<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/System/Permission/Group/Set.inc.php' );

class PermissionGroup_List extends AMPSystem_List {
    var $name = "Permission Group";
    var $col_headers = array( 
        'Group' => 'name',
        'ID'    => 'id');
    var $editlink = 'per.php';
    var $extra_columns = array( 'Rights' => 'rights');

    function PermissionGroup_List( &$dbcon ) {
        $source = & new PermissionGroupSet( $dbcon );
        $this->init( $source );
        $source->addSelectExpression( 'id as rights');
        $source->readData( );

        $this->addTranslation( 'rights', '_calculatePermissionsPercentage');

    }

    function _calculatePermissionsPercentage( $value, $fieldname, $data_array ){
        $allPermissions = &AMPSystem_Lookup::instance( 'permissionNames');
        $groupPermissions = &AMPSystemLookup_PermissionLevel::instance( $value );
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
