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

$show_template=false;
$modin = false;
$flash = & AMP_System_Flash::instance( );

if (isset($_REQUEST['modin']) && $_REQUEST['modin']) {
    $modin=$_REQUEST['modin'];
} else {
    $flash->add_error( sprintf( AMP_TEXT_ERROR_NO_SELECTION, AMP_TEXT_FORM ));
    ampredirect( AMP_SYSTEM_URL_FORMS );
    exit;
}

$form_permissions = &AMPSystem_Lookup::instance( 'PermissionsbyForm');
$modin_permission = ( isset( $form_permissions[$modin]) && $form_permissions[$modin]) ? $form_permissions[$modin] : false;

$view_permission = (AMP_Authorized(AMP_PERMISSION_FORM_DATA_EXPORT)
                 && ( $modin_permission ? AMP_Authorized($modin_permission) : true ));

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
        $flash = AMP_System_Flash::instance( );
        $renderer = AMP_get_renderer( );
        $error_message = sprintf( AMP_TEXT_ERROR_FAILED, AMP_TEXT_EXPORT );
        $error_message .= $renderer->newline( ).join($renderer->newline( ),$userlist->errors);
        $flash->add_error( $error_message );
    }
} else {
    $show_template = true;
    $error_message = sprintf( AMP_TEXT_ERROR_ACTION_NOT_ALLOWED, AMP_TEXT_EXPORT );
    $flash->add_error( $error_message ); 
    $output = $error_message; 
}



if ($show_template) {
    require_once('header.php');

    print $output; 

    require_once('footer.php');
}




?>
