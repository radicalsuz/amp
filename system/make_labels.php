<?php

#make_labels.php
#makes labels from a search
require_once("../adodb/adodb.inc.php");
require_once ($base_path.'Connections/freedomrising.php');
#require_once('AMP/UserData/Input.inc.php');
require_once('AMP/UserData/Search.inc.php');
require_once ("Modules/UDM/Output/labels.inc.php");

$label_list=new UserLabels;

if (isset($_REQUEST['sqlsend'])) {
	$label_list->current_sql= "Select Concat(First_Name, \" \", Last_Name) as Name, occupation, Company, Street, Street_2, Street_3, City, State, Zip, Country ".stripslashes($_REQUEST['sqlsend'])." ORDER BY Zip";
	$label_list->runSearch($dbcon);
	$label_list->list2labels('labels.pdf', $_REQUEST['UDM_label_type']);
}
?>
