<?php
require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'Modules/Blast/ComponentMap.inc.php');

class BlastSubscription_Set extends AMPSystem_Data_Set {
    var $datatable = PHPLIST_TABLE_LIST_USER;
    var $_id_field_lookups = 'listid';

    function BlastSubscription_Set( &$dbcon ) {
        $this->init( $dbcon );
    }

    function addCriteriaUser( $user_id ) {
        $this->addCriteria( 'userid='.$user_id);
    }

    function addCriteriaList( $list_id ) {
        $this->addCriteria(  'listid='.$list_id );
    }
    
	function getListsByUser($user_id) {
        $set = array();
        $this->addCriteriaUser( $user_id );
        $sql = "SELECT listid " . $this->_makeSource(). $this->_makeCriteria();
        $set = $this->dbcon->CacheGetArray( $sql );
        if (defined( $this->_debug_constant ) && constant( $this->_debug_constant )) AMP_DebugSQL( $sql, get_class($this)." lookup " .$field); 
        $final_set = array( );
        foreach( $set as $key => $row) {
            $final_set[] = $row['listid'];
        }

		return $final_set;
	}
    
}
?>
