<?php
require_once( 'AMP/System/Base.php');
$modid = isset( $_GET['modid'] ) && $_GET['modid'] ? $_GET['modid'] : false;

if ( !$modid ) ampredirect( AMP_SYSTEM_URL_HOME );
if ( $modid ) ampredirect( AMP_Url_AddVars( AMP_SYSTEM_URL_TOOLS, 'id='.$modid ));
/*

require("Connections/freedomrising.php");
include ("header.php");

$table = "module_control";
$listtitle ="Module Settings";
$listsql ="select description, setting, id  from $table where modid = ".$_GET['modid'];
$orderby =" order by description asc  ";
$fieldsarray=array( 'Module Setting'=>'description','Setting'=>'setting');
$filename="module_control.php";
$extra = NULL;
listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort,$extra);


$table = "navtbl";
$listtitle ="Related Navigation Files";
$listsql ="select id, name  from $table where modid = ".$_GET['modid'];
$orderby =" order by name asc  ";
$fieldsarray=array( 'Navigation File'=>'name');
$filename="nav.php";
$extra = NULL;
listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort,$extra);


include ("footer.php");
*/
?>
