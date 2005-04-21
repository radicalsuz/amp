<?php

require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_CopyModuletext_AMPsystem extends UserDataPlugin {

    var $name = "Copy a plugin."
    var $available = false;

    function UserDataPlugin_CopyModuletext_AMPsystem ( &$udm ) {
        $this->init( $udm );
    }

    function execute ( $options = null ) {

        // nasty hack, sigh.
        udm_amp_copy_moduletext( $this->udm, $options );

    }
}


function udm_amp_copy_moduletext ( $udm, $options = null ) {

	$dbcon = $udm->dbcon;
	foreach ($options['modtexts'] as $thismod) {
		$sql = "SELECT * from moduletext where id=".$thismod."  LIMIT 0, 1";
		if ($old_modtext=$dbcon->GetArray($sql)){
			$new_modtext=$old_modtext[0];
			$new_modtext['title']=str_replace($options['old_name'], $options['new_name'], $new_modtext['title']);
			$new_modtext['name']=str_replace($options['old_name'], $options['new_name'], $new_modtext['name']);
			$new_modtext['modid']=$options['new_module_id'];

			$insert_sql="INSERT INTO moduletext ( title, name, subtitile, test, html, searchtype, date, type, subtype, catagory, templateid, modid ) VALUES ( ";
			foreach ($new_modtext as $key=>$lvalue) {
				if ($key!='id')	$insert_sql.= $dbcon->qstr($lvalue).", ";
			}
			$insert_sql=substr($insert_sql, 0, strlen($insert_sql)-2).")";
				
			if ($dbcon->Execute($insert_sql)) {
				$new_modtext_obj=$dbcon->GetArray("SELECT LAST_INSERT_ID()");
				$options['new_modtext'][]= join(",",$new_modtext_obj[0]);

			}
		}
	}
	$sql="UPDATE userdata_fields SET modidinput=".$options['new_modtext'][0].", modidresponse=".$options['new_modtext'][1]." WHERE id=".$options['new_modin'];
	$dbcon->Execute($sql);
	
	return $options;
}


	

?>
