<?php
/*********************
07-09-2003  v3.01
Module:  Index
Description:  Index template page
SYS VARS: $NAV_IMG_PATH, $indexreplace
functions  evalhtml
To Do: 
*********************/ 
if (isset($HTTP_GET_VARS["filelink"])) { header ("Location: $HTTP_GET_VARS[filelink]");}

$mod_id = 2 ;
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");

//include("sysfiles.php"); 
// include("headerdata.php");
ob_start(); 

 if ($indexreplace != NULL)
{
require ("$indexreplace");
 }
else{ include("index.inc.php");} 

//include ("footer.php");
include("AMP/BaseFooter.php");
?>