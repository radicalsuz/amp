<?php

/*****
 *
 * AMP UserData Form View
 *
 * (c) 2004 Radical Designs
 * Written by Blaine Cook, blaine@radicaldesigns.org
 *
 *****/
header("Location: form_export.php?".$_SERVER['QUERY_STRING']);
/*
$mod_name='udm';
require_once( 'AMP/UserData/Set.inc.php' );
require_once( 'Connections/freedomrising.php' );
require_once( 'utility.functions.inc.php' );

// Fetch the form instance specified by submitted modin value.
$udm = new UserDataSet( $dbcon, $_REQUEST[ 'modin' ] );

$modidselect=$dbcon->Execute("SELECT id, perid from modules where userdatamodid=" . $udm->instance ) or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");
$modin_permission=$modidselect->Fields("perid");


//Accept URL values for editlink and sortby options
if (isset($_GET['editlink'])) { $options['editlink_action']=$_GET['editlink'];
} else { $options=array();}
if (isset($_GET['sortby'])) { $options['sort_by']=$_GET['sortby'].", First_Name, Last_Name";
}


	$udm->admin = true;
	$options['allow_publish']=true;
	$udm->authorized = true;
	$options['allow_edit']=true;
	$options['allow_export']=true;
	$options['allow_lookups']=true;
	$options['include_id_column']=true;
	$options['allow_include_modins']=true;
	$options['include_modin_column']=true;
} else {
	$udm->admin=false;
}

if ($userper[$modin_permission]) {
	$options['allow_export']=true;
	$udm->authorized=true;
} else {
	$udm->authorized=false;
}



# Output the file



$mod_id = $udm->modTemplateID;

	if (isset($udm->plugins['UserlistCSV'])) {
		
        $output=$udm->doAction("UserlistCSV", $options); 
    } else {
		$udm->registerPlugin("Output", "UserlistCSV");
		$output=$udm->doAction("UserlistCSV", $options);
	}
	print $output;


?>
