<?php
$modid = "21";

require("Connections/freedomrising.php");

$table = "action_text";
$listtitle ="Action Center";
$listsql ="select id, title  from $table  ";
$orderby =" order by id desc  ";
$fieldsarray=array( 'ID'=>'id',
					'Action Name'=>'title'
					);
$filename="sendfax_edit.php";
//$extra = array( 'reports'=>'sendfax_reports.php?report=',
				//'delete'=>'sendfax_reports.php?del='
//					);

include ("header.php");
listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort,$extra);
include ("footer.php");
?>
