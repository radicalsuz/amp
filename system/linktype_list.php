<?php

$modid = "11";

require("Connections/freedomrising.php");

$table = "linktype";
$listtitle ="Links";
$listsql ="select id, name, publish from $table  ";
$orderby =" order by name asc  ";
$fieldsarray=array( 'Type Name'=>'name',
					'ID'=>'id',
					'Publish'=>'publish');
$filename="linktype_edit.php";

include ("header.php");

listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort);


include ("footer.php");
?>