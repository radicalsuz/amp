<?php
/* this is not used and has the potential to be very bad ap 2008-07

include("AMP/BaseDB.php");

$fileinfo= $dbcon->Execute("Select * from custom where id = $file ");
$file = $fileinfo->Fields("file");

if ($fileinfo->Fields("mod_id")) {$mod_id = $fileinfo->Fields("mod_id");}
if ($fileinfo->Fields("modid")) {$modid = $fileinfo->Fields("modid");}
if ($_GET[modid]) {$modid = $_GET[modid]; }
if ($_GET[mod_id]) {$mod_id = $_GET[mod_id]; }

 
if ($fileinfo->Fields("noincludes") != '1' ){
	include("AMP/BaseTemplate.php"); 
	include("AMP/BaseModuleIntro.php");
}

include ($file);

//if  (!$fileinfo->Fields("noincludes") == 0 ){
include("AMP/BaseFooter.php");
//}
*/
?>
