<?php

#make_labels.php
#makes labels from a search

#require_once('AMP/UserData/Search.inc.php');
#require_once ("Modules/UDM/Output/labels.inc.php");

/*
$label_list=new UserLabels;

if (isset($_REQUEST['sqlsend'])) {
	$label_list->current_sql= "Select Concat(First_Name, \" \", Last_Name) as Name, occupation, Company, Street, Street_2, Street_3, City, State, Zip, Country ".stripslashes($_REQUEST['sqlsend'])." ORDER BY Zip";
	$label_list->runSearch($dbcon);
	$label_list->list2labels('labels.pdf', $_REQUEST['UDM_label_type']);
}
*/

require("AMP/System/Base.php");
require("AMP/System/BaseTemplate.php");

$template = &new AMPSystem_BaseTemplate();
$template->setToolName( 'system' );

$script = "
<script type = 'text/javascript'>
//<!--
history.go(-1);
alert('Label creation is currently disabled -- \\nplease export the list and make labels locally');
//-->
</script>";

print $template->outputHeader();
print ('Label creation is currently disabled -- please export the list and make labels locally');
print $script;
print $template->outputFooter();

?>
  
?>
