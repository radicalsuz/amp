<?php
$intro_id = 13;
require_once("AMP/BaseDB.php");

/**
 * Check for a cached copy of this request
 */
if ( $cached_output = AMP_cached_request( )) {
    print $cached_output;
    exit;
}


require_once("AMP/BaseTemplate.php");
require_once("AMP/BaseModuleIntro.php");  
require_once( 'AMP/Content/Map/Public/List.php');

$list = new AMP_Content_Map_Public_List( );
print $list->execute( );
/*
require_once('Connections/menu.class.php');

$obj = &new Menu();
echo $obj->print_full_menu_tree($MX_top); 
*/

require_once("AMP/BaseFooter.php"); 
?>
