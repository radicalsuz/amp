<?php
$modid = "21";
$mod_name = 'actions';

require("Connections/freedomrising.php");

$table = "action_text";
$listtitle ="Action Center";
$listsql ="select id, title  from $table  ";
$orderby =" order by id desc  ";
$fieldsarray=array( 'ID'=>'id',
					'Action Name'=>'title'
					);
$extra = array('Add to Content System'=>'module_contentadd.php?action=');

$filename="sendfax_edit.php";
$extra = array( 'report'=>'sendfax_report.php?report=',
				//'delete'=>'sendfax_reports.php?del='
					);

include ("header.php");
listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort,$extra);
include ("footer.php");
?>
