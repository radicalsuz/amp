<?php

/*****
 *
 * UserData Listing Page
 * 
 * Allows search for records admin side
 *
 * 
 *
 *****/
$mod_name='udm';
require_once( 'Connections/freedomrising.php' );
require_once('AMP/UserData/Set.inc.php');

if (isset($_REQUEST['modin']) && $_REQUEST['modin']) {
    $modin=$_REQUEST['modin'];
} else {
    header ("Location: modinput4_list.php");
}

$modidselect=$dbcon->CacheExecute("SELECT id, perid from modules where publish=1 and userdatamodid=" . $modin) or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");
$modin_permission=$modidselect->Fields("perid");


$view_permission = ($userper[54]&&$userper[$modin_permission]);

$admin=true;
$userlist=&new UserDataSet($dbcon, $modin, $admin);

$userlist->_register_default_plugins();

$uid= isset($_REQUEST['uid'])?$uid:false;

if ($uid && $modin) {

    $userlist->uid=$uid;
    $output= $userlist->output('DisplayHTML'); 

} else { 

    //display result list
    $output = $userlist->output_list('TableHTML');
}

if (!$view_permission) $output = "You do not have permission to view this list";

require_once( 'header.php' );

print $output;

        
// Append the footer and clean up.
require_once( 'footer.php' );

?>
