<?php

/*****
 *
 * AMP UserData Form View
 *
 * (c) 2004 Radical Designs
 * Written by Blaine Cook, blaine@radicaldesigns.org
 *
 *****/
header("Location: userdata_list.php?".$_SERVER['QUERY_STRING']);
/*
$mod_name='udm';
require_once( 'AMP/UserData/Set.inc.php' );
require_once( 'Connections/freedomrising.php' );
require_once( 'utility.functions.inc.php' );

// Fetch the form instance specified by submitted modin value.
$udm = new UserDataSet( $dbcon, $_REQUEST[ 'modin' ] );
$modidselect=$dbcon->Execute("SELECT id, perid from modules where publish=1 and userdatamodid=" . $udm->instance ) or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");
$modin_permission=$modidselect->Fields("perid");


//Accept URL values for editlink and sortby options
if (isset($_GET['editlink'])) { 
    $options['editlink_action']=$_GET['editlink'];
} else { $options=array();}
if (isset($_GET['sortby'])) { $options['sort_by']=$_GET['sortby'].", First_Name, Last_Name";
}


if (AMP_Authorized( AMP_PERMISSION_FORM_DATA_EDIT) && AMP_Authorized($modin_permission)) { 
	$udm->admin = true;
	$options['allow_publish']=true;
	$udm->authorized = true;
    #$udm->plugins['UserlistHTML']['Output']->udm->authorized=true;
	$options['allow_edit']=true;
	$options['allow_export']=true;
	$options['allow_include_modins']=true;
	$options['allowed_modins']="*";
} elseif ($userper[54] && $userper[$modin_permission]) {
	$udm->authorized = true;
	$options['allow_edit']=false;
	$options['allow_publish']=false;
} else {
	$udm->authorized=false;
}
// Authorize plugins individually -- please remove when passing bug has been
// fixed
if ($udm->authorized) {
    foreach ($udm->plugins as $udm_action=>$udm_plug) {
        foreach ($udm_plug as $udm_namespace=>$udm_obj) {
            if (isset ($udm_obj->udm)) {
                $udm_obj->udm->authorized=true;
            }
        }
    }
}

# Now Output the List.




$mod_id = $udm->modTemplateID;

require_once( 'header.php' );

echo "<h2>View/Edit " . $udm->name . "</h2>";

$output=$udm->doPlugin("Output", "UserlistHTML", $options);
echo $output;


// Append the footer and clean up.
require_once( 'footer.php' );
*/

?>
