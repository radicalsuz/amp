<?php
$modid = $_GET['modid'];

require("Connections/freedomrising.php");
include ("header.php");

$table = "moduletext";
$listtitle ="Pages";
$listsql ="select id, name, title  from $table where modid = ".$_GET['modid'];
$orderby =" order by name asc  ";
$fieldsarray=array( 'Name'=>'name','Title'=>'title');
$filename="module_header.php";

$extra =array('Navigation Files'=>'nav_position.php?mod_id=','Add Page to Content System'=>'module_contentadd.php?mod_id=',);
listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort,$extra);

include ("footer.php");
?>