<?php
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
?>
