<?php
/**/
require_once( 'AMP/System/Base.php');

$tool_id = isset( $_GET['modid'] ) && $_GET['modid'] ? $_GET['modid'] :false;
if ( !$tool_id ) {
    $tool_id = isset( $_GET['tool_id']) && $_GET['tool_id'] ? $_GET['tool_id'] :false;
}
if ( !$tool_id ) ampredirect( AMP_SYSTEM_URL_PUBLIC_PAGES );
$modid = $tool_id;

require_once( 'AMP/System/Component/Controller.php');
require_once( 'AMP/System/Introtext/List.inc.php');

$controller = &new AMP_System_Component_Controller( );
$controller->set_banner( AMP_TEXT_LIST , 'Public Page');
$display = &$controller->get_display( );
$display->add_nav( 'content' );

$list = &new AMPSystem_Introtext_List( $dbcon );
$list->setTool( $tool_id );
$display->add( $list );
print $display->execute( );

    
/*
$modid = $_GET['modid'];

require("Connections/freedomrising.php");
include ("header.php");

global $dbcon;

$table = "moduletext";
$listtitle ="Pages";
$listsql ="SELECT id, name, title FROM $table WHERE modid=" . $dbcon->qstr($modid);
$orderby =" ORDER BY name asc  ";
$fieldsarray=array( 'Name'=>'name','Title'=>'title');
$filename="module_header.php";

$extra =array('Navigation Files'=>'nav_position.php?mod_id=','Add Page to Content System'=>'module_contentadd.php?mod_id=',);
listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort,$extra);

include ("footer.php");
/*/
?>
