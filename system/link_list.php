<?php

$modid = "11";

require("Connections/freedomrising.php");

$table = "links";
$listtitle ="Links";
$listsql ="select id, url, linkname, publish from $table  ";
$orderby =" order by linkname asc  ";
$fieldsarray=array( 'Line Name'=>'linkname',
					'URL'=>'url',
					'ID'=>'id',
					'Publish'=>'publish');
$filename="link_edit.php";

include ("header.php");

listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort);


include ("footer.php");
?>