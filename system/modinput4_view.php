<?php

/*****
 *
 * AMP UserData Form View
 *
 * (c) 2004 Radical Designs
 * Written by Blaine Cook, blaine@radicaldesigns.org
 *
 *****/
$mod_name='udm';
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'Connections/freedomrising.php' );
require_once( 'utility.functions.inc.php' );

// User ID.
$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
if ( !$uid ) $uid = ( isset( $_REQUEST['id']) ? $_REQUEST['id'] : false );

#set_error_handler( 'e' );
$modin = (isset($_REQUEST['modin']) && $_REQUEST['modin'])?$_REQUEST['modin']:false;
if ( $uid && !$modin ) {
    //look up the modin via uid
    require_once( 'AMP/System/User/Profile/Profile.php');
    $profile = new AMP_System_User_Profile( AMP_Registry::getDbcon( ), $uid );
    if ( $profile->hasData( ) &&  ( $modin = $profile->getModin( ))) {
        //redirect to the standard URL
        $url_vars = AMP_URL_Values( );
        if ( !$url_vars ) $url_vars = array( );
        $url_vars = array_merge( $url_vars, array( 'uid' => 'uid=' . $uid, 'modin' => 'modin=' . $modin ));
        unset( $url_vars['id']);
        ampredirect( AMP_url_add_vars( AMP_SYSTEM_URL_FORM_ENTRY, $url_vars ));
    } 
}

if ($modin) {
    $form_id_nav = $modin;
    $form_permissions = &AMPSystem_Lookup::instance( 'PermissionsbyForm');
    $tools = AMP_lookup( 'ToolsbyForm');
    $modin_permission = ( isset( $form_permissions[$modin]) && $form_permissions[$modin]) ? $form_permissions[$modin] : false;
    //$modidselect=$dbcon->GetRow("SELECT id, perid from modules where userdatamodid=" . $modin) or DIE($dbcon->ErrorMsg());
    //$modid=$modidselect['id'];
    $modid = ( isset( $tools[$modin]) && $tools[$modin]) ? $tools[$modin] : false;
} else {
    ampredirect("modinput4_list.php");
}

$admin = (AMP_Authorized(AMP_PERMISSION_FORM_DATA_EDIT)
       && AMP_Authorized($modin_permission) );

// Fetch the form instance specified by submitted modin value.
$udm = &new UserDataInput( $dbcon, $modin ,$admin );

// User ID.
$udm->authorized = true;
$udm->uid = $uid;

// Was data submitted via the web?
$sub = (isset($_REQUEST['btnUdmSubmit'])) ? $_REQUEST['btnUdmSubmit'] : false;

// Fetch or save user data.
if ( $sub ) {

    // Save only if submitted data is present, and the user is
    // authenticated, or if the submission is anonymous (i.e., !$uid)
	if($udm->saveUser()) {
			ampredirect( AMP_SYSTEM_URL_FORM_DATA . "?modin=".$udm->instance);
	}
	$udm->showForm = true;

} elseif ( !$sub && $uid ) {

    // Fetch the user data for $uid if there is no submitted data
    // and the user is authenticated.
    $udm->getUser( $uid ); 

}

/* Now Output the Form.

   Any necessary changes to the form should have been registered
   before now, including any error messages, notices, or
   complete form overhauls. This can happen either within the
   $udm object, or from print() or echo() statements.

   By default, the form will include AMP's base template code,
   and any database-backed intro text to the appropriate module.

*/

$mod_id = $udm->modTemplateID;

//require_once( 'header.php' );
require_once( 'AMP/Content/Buffer.php' );
require_once( 'AMP/System/Page/Display.php' );

$page_output = //'<div id="AMP_flash"></div>'
             "<h2>Add/Edit " . $udm->name . "</h2>"
             . "<font color = \"red\">".$udm->outputErrors()."</font>"
             . $udm->output();

$display = new AMP_Content_Buffer( );
$display->add( $page_output );

$flash = AMP_System_Flash::instance( );
$fake_controller = $flash;

$complete_page = &AMP_System_Page_Display::instance( $fake_controller );
$complete_page->add( $flash, AMP_CONTENT_DISPLAY_KEY_FLASH );
$complete_page->add( $display );
print $complete_page->execute( );

// Append the footer and clean up.
//require_once( 'footer.php' );

?>
