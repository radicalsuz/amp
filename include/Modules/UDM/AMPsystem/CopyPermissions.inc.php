<?php

require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_CopyPermissions_AMPsystem extends UserDataPlugin {

    var $name = "Copy a plugin.";
    var $available = false;

    function UserDataPlugin_CopyPermissions_AMPsystem ( &$udm ) {
        $this->init( $udm );
    }

    function execute ( $options = null ) {

        // nasty hack, sigh.
        udm_amp_copy_permissions( $this->udm, $options );

    }
}
function udm_amp_copy_permissions ( $udm, $options = null ) {

	$dbcon = $udm->dbcon;
	$sql = "SELECT * from per_description where id=".$options['per_id']." AND publish=1  LIMIT 0, 1";
	if ($old_permission=$dbcon->GetArray($sql)){
		$new_permission=$old_permission[0];
		$new_permission['name']=str_replace($options['old_name'], $options['new_name'], $new_permission['name']);
		$new_permission['description']=str_replace($options['old_name'], $options['new_name'], 	$new_permission['description']);

		$insert_sql="INSERT INTO per_description ( name, description, publish ) VALUES ( ";
		foreach ($new_permission as $key=>$lvalue) {
			if ($key!='id')	$insert_sql.= $dbcon->qstr($lvalue).", ";
		}
		$insert_sql=substr($insert_sql, 0, strlen($insert_sql)-2).")";

		if ($dbcon->Execute($insert_sql)) {

			$new_per_obj=$dbcon->GetArray("SELECT LAST_INSERT_ID()");
			$new_perid= join(",",$new_per_obj[0]);

			//Copy group permissions from old permission
			$insert_sql="INSERT INTO permission ( perid, groupid )  SELECT ".$new_perid." as new_per, groupid from permission where perid = ".$options['per_id'];
			$dbcon->Execute($insert_sql);
			
			return $new_perid;
		}
	}
	return false;
}

	

?>
