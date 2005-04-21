<?php

require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_CopyModule_AMPsystem extends UserDataPlugin {

    var $name = "Copy a plugin."
    var $available = false;

    function UserDataPlugin_CopyModule_AMPsystem ( &$udm ) {
        $this->init( $udm );
    }

    function execute ( $options = null ) {

        // nasty hack, sigh.
        udm_amp_copy_module( $this->udm, $options );

    }
}


function udm_amp_copy_module ( $udm, $options = null ) {

	$dbcon = $udm->dbcon;

	$sql = "SELECT * from modules where userdatamodid=".$options['old_modin']." AND publish=1  LIMIT 0, 1";
	$old_module=$dbcon->GetArray($sql);
	$new_module=$old_module[0];
	$new_module['name']=$options['new_name'];
	$new_module['userdatamodid']=$options['new_modin'];
	$new_module['file']=str_replace("modin=".$options['old_modin'], "modin=".$options['new_modin'], $new_module['file']);
	$options['per_id']=$new_module['perid'];
	$new_module['perid']=$udm->doPlugin("AMP", "copy_permissions", $options);
	$new_module['navhtml']=str_replace("modin=".$options['old_modin'], "modin=".$options['new_modin'], $new_module['navhtml']);
	$new_module['navhtml']=str_replace($options['old_name'], $options['new_name'], $new_module['navhtml']);
	$new_module['navhtml']=str_replace("?modid=".$old_module[0]['id'], "?modid=XX", $new_module['navhtml']);


	$final_insert="INSERT INTO modules ( name, userdatamod, userdatamodid, file, perid, navhtml, publish ) VALUES ( ";
	foreach ($new_module as $key=>$lvalue) {
		if ($key!='id')	$final_insert.= $dbcon->qstr($lvalue).", ";
	}
	$final_insert=substr($final_insert, 0, strlen($final_insert)-2).")";
	$rs=$dbcon->Execute($final_insert);

	$rs=$dbcon->GetArray("SELECT LAST_INSERT_ID()");
	$new_module_id=join(",",$rs[0]);
	
	$final_insert="Select navhtml from modules where id=".$new_module_id;
	$rs=$dbcon->GetArray($final_insert);
	$nav_html=$dbcon->qstr(str_replace("?modid=XX", "?modid=".$new_module_id, $rs[0]['navhtml']));
	$final_insert="UPDATE modules set navhtml =".$nav_html." where id=".$new_module_id;
	$rs=$dbcon->Execute($final_insert);
	
	
	
	
	return $new_module_id;

}




?>
