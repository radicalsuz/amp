<?php
$modid = $_GET['modid'];

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
?>