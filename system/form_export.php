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
require_once( 'AMP/System/Base.php' );
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

$view_permission = (AMP_Authorized(AMP_PERMISSION_FORM_DATA_EXPORT)
                 && AMP_Authorized($modin_permission));
if ( $view_permission ) {
    $admin = true;
    // Fetch the form instance specified by submitted modin value.
    $userlist = & new UserDataSet( $dbcon, $_REQUEST[ 'modin' ], $admin );


    /* Output the file

    */
    $userlist->unregisterPlugin('Pager', 'Output');
    $search_form = $userlist->getPlugins('SearchForm');
    $search = $userlist->getPlugins('Search');
    if (!$search_form) {
        $userlist->registerPlugin('Output', 'SearchForm'); 
    }
    if (!$search) {
        $userlist->registerPlugin('AMP', 'Search'); 
    }

        set_time_limit(150);

    if ($output = $userlist->doPlugin('Output', 'ExportFile')) {
        
        print $output;
    } else {
        $show_template=true;
        $output = "Export failed:<BR>".join("<BR>",$userlist->errors);
    }
} else {
    $show_template = true;
    $output = 'You do not have permission to export this list'; 
}



if ($show_template) {
    require_once('header.php');

    print $output; 

    require_once('footer.php');
}




?>
