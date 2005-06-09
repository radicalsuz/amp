<?php

/*****
 *
 * AMP UserData Form View
 *
 * (c) 2004 Radical Designs
 * Written by Blaine Cook, blaine@radicaldesigns.org
 *
 *****/

$mod_name = "udm";
require_once( 'AMP/UserData/Set.inc.php' );
require_once( 'Connections/freedomrising.php' );
require_once( 'utility.functions.inc.php' );

$modin = (isset($_REQUEST['modin']) && $_REQUEST['modin'])?$_REQUEST['modin']:false;
$show_template=false;

if ($modin) {
    $modidselect=$dbcon->GetRow("SELECT id, perid from modules where userdatamodid=" . $modin) or DIE($dbcon->ErrorMsg());
    $modid=$modidselect['id'];
    $modin_permission=$modidselect["perid"];
} else {
    ampredirect("modinput4_list.php");
}

if ($userper[53]&&$userper[$modin_permission]) { 
    $admin = true;
} else {
    $show_template = true;
    $output = 'You do not have permission to export this list'; 
}

// Fetch the form instance specified by submitted modin value.
$userlist = & new UserDataSet( $dbcon, $_REQUEST[ 'modin' ], $admin );


/* Output the file

*/
    $userlist->unregisterPlugin('Pager', 'Output');
    set_time_limit(150);

    if (true) {
    #if ($userlist->doAction('Search')) { 
        if ($output = $userlist->doPlugin('Output', 'ExportFile')) {
            
            print $output;
        } else {
            $show_template=true;
            $output = "Export failed:<BR>".join("<BR>",$userlist->errors);
        }
    
    } else {
        $show_template = true;
        $output = join ("<BR>",$userlist->errors); 
    }

if ($show_template) {
    require_once('header.php');

    print $output; 

    require_once('footer.php');
}




?>
