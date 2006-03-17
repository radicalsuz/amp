<?php

class AMP_System_UserData_Plugin extends AMPSystem_Data_Item {

    var $_class_name = 'AMP_System_UserData_Plugin';
    var $datatable = 'userdata_plugins';
    var $_options = array( );

    function AMP_System_UserData_Plugin( &$dbcon, $id = null ){
        $this->init( $dbcon, $id ) ;
        $this->_init_options( );
    }

    function _init_options( ){
        require_once( 'AMP/System/UserData/Plugin/Observer.php');
        $options_observer = &new AMP_System_UserData_Plugin_Observer( );
        $options_observer->attach( $this );
    }

    function getName( ){
        if (!( $namespace = $this->getData( 'namespace'))) return false;
        return $namespace . '_' . $this->getData( 'action');
    }

    function readOptions( ){
        $this->_options = FormLookup_PluginOptions( $this->id );
    }

    function deleteOptions( ){
        require_once( 'AMP/System/Data/Set.inc.php');
        $data_set = &new AMP_System_Data_Set( $this->dbcon );
        $data_set->setSource( 'userdata_plugins_options');
        return $data_set->deleteData( 'plugin_id='.$this->id );
    }

    function updateOptions( ){
        if ( !isset( $this->_options )){
            return false;
        }
        $update_sql_template = 'REPLACE INTO userdata_plugins_options values ( '.$this->id .', %s, %s )';
        foreach( $this->_options as $option_name => $option_value ){
            $update_sql = sprintf( $update_sql_template, $this->dbcon->qstr( $option_name), $this->dbcon->qstr( $option_value ));
            if ( !$this->dbcon->Execute( $update_sql )) {
                AMP_DebugSQL( $update_sql, 'Option Update');
            }
            
        }
        $this->dbcon->CacheFlush( );
    }

}

?>
