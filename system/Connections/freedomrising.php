<?php 

ob_start();

require_once('AMP/BaseDB.php');

$browser_ie =  strstr(getenv('HTTP_USER_AGENT'), 'MSIE') ;
$browser_win =  strstr(getenv('HTTP_USER_AGENT'), 'Win') ;
if (!strstr(getenv('HTTP_USER_AGENT'), 'Safari')){
	$browser_mo =  strstr(getenv('HTTP_USER_AGENT'), 'Mozilla/5') ;
}
if (strstr(getenv('HTTP_USER_AGENT'), '2002')){
        $browser_mo =  NULL ;
}


/*
if ($security == "inactive") {
	$userLevel = 1 ;
	$ID = 1 ;
} else {
	require("../password/secure.php");
}
*/

$userLevel = $_SERVER['REMOTE_GROUP'];

$gettop = $dbcon->Execute("SELECT subsite FROM per_group WHERE id = $userLevel")
            or die( "Couldn't find sub-site authentication information: " . $dbcon->ErrorMsg());
$MX_top = $gettop->Fields("subsite"); 

$valper = $dbcon->Execute("SELECT perid FROM permission WHERE groupid = $userLevel")
            or die( "Couldn't find permission values: " . $dbcon->ErrorMsg());
$userper = array();

while (!$valper->EOF) { 
	$perin = $valper->Fields("perid");
	$userper["$perin"] = 1;
	$valper->MoveNext();
}

$getsecper=$dbcon->Execute("SELECT typeid FROM per_section WHERE groupid = $userLevel") or DIE($dbcon->ErrorMsg());

while (!$getsecper->EOF) { 
	//$secin= $getsecper->Fields("typeid");
	$sectional_per["$secin"] = 1;
	$getsecper->MoveNext();
}

if ($userper[73] != 1) {
	header ("Location: index.php");
} 

function helpme2($link) {
	$output = "<a href=\"javascript:void(0)\" ONCLICK=\"open('help.php?file=$link','miniwin','location=1,scrollbars=1,resizable=1,width=550,height=400')\"><img src=\"images/help.png\" width=\"15\" height=\"15\" border=\"0\" align=\"absmiddle\"></a>&nbsp;";
	return $output;
}
	 
function helpme($link) {

	global $PHP_SELF;
	$output="<table width=\"15\" border=\"0\" align=\"right\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><a href=\"javascript:void(0)\" ONCLICK=\"open('help.php?file=";
	
	$pos = strrpos($PHP_SELF, "/");
	$pos = substr($PHP_SELF, ($pos + 1), -4);
	$output.= $pos;
	$output.= "#";
	$output.= $link;
	$output.="','miniwin','location=1,scrollbars=1,resizable=1,width=550,height=400')\"><img src=\"images/help.png\" border=\"0\" align=\"absmiddle\"></a></td></tr></table>";
	return $output;

}

require_once($base_path."includes/dbfunctions.php");
require_once("Connections/ampclass.php");

?>
