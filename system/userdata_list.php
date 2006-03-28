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
require_once( 'AMP/System/Base.php' );
require_once('AMP/UserData/Set.inc.php'); 

if (isset($_REQUEST['modin']) && $_REQUEST['modin']) {
    $modin=$_REQUEST['modin'];
} else {
    header ("Location: modinput4_list.php");
}

$form_permissions = &AMPSystem_Lookup::instance( 'PermissionsbyForm');
$modin_permission = ( isset( $form_permissions[$modin]) && $form_permissions[$modin]) ? $form_permissions[$modin] : false;

$view_permission = (AMP_Authorized(AMP_PERMISSION_FORM_DATA_EDIT)
                 && ( $modin_permission ? AMP_Authorized($modin_permission) : true ));

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

if (!$view_permission) $output = AMP_TEXT_PERMISSION_DENIED_LIST;

require_once( 'header.php' );

print $output;

        
// Append the footer and clean up.
require_once( 'footer.php' );

?>
