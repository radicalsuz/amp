<?php
/*********************
07-09-2003  v3.01
Module:  Index
Description:  Index template page
SYS VARS: $NAV_IMG_PATH, $indexreplace
functions  evalhtml
To Do: 
*********************/ 
if (isset($_GET['filelink'])) header ("Location: " . $_GET['filelink']);
include("AMP/BaseTemplate2.php");

ob_start(); 

if (isset($indexreplace) && $indexreplace) {
    require ("$indexreplace");
} else {
    include("AMP/Article/index.inc.php");
}

include("AMP/BaseFooter2.php");
?>
