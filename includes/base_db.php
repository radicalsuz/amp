<?php

require_once("adodb/adodb.inc.php");
include_once("utility.functions.inc.php");

$ampasp = 1;

if ($ampasp == 1) {
 ADOLoadCode("mysql");
   $ampdbcon=&ADONewConnection("mysql");
   $ampdbcon->Connect("localhost","david","havlan","amp");
   $AMPsql = "SELECT * FROM system where server = '".$HTTP_SERVER_VARS['SERVER_NAME']."'";
#echo $AMPsql;
  $ampversion=$ampdbcon->Execute($AMPsql)or DIE($ampdbcon->ErrorMsg());
$AmpPath = $ampversion->Fields("amppath");
$MX_type ="type"; //  $ampversion->Fields("typefield");
$MX_top = 1; // $ampversion->Fields("toptypelevel");

$ConfigPath = $AmpPath."custom/config.php";
$ConfigPath2 = $AmpPath."custom/config.php";
$ConfigPath3 = $AmpPath."custom/config.php";

	##### AMP SERVER ####


	##########

} else { 

    $MX_type ="type";
    $MX_top =1;
    $ConfigPath = "custom/config.php";
    $ConfigPath2 = "../custom/config.php";
    $ConfigPath3 = "../../custom/config.php";

}

$PHP_SELF=$_SERVER['PHP_SELF'];

if (get_magic_quotes_gpc()==1) {
	$MM_sysvar_mq ="1";
} else{
	 $MM_sysvar_mq ="0";
} 


if (isset($subdir)) {
	require_once("$ConfigPath2");
} elseif (isset($subdir2)) {
	require_once("$ConfigPath3");
} else {
	require_once("$ConfigPath");
}


#connect to Database
ADOLoadCode($MM_DBTYPE);
$dbcon=&ADONewConnection($MM_DBTYPE);
$dbcon->Connect($MM_HOSTNAME,$MM_USERNAME,$MM_PASSWORD,$MM_DATABASE);
	
#load menu class	
include($base_path."Connections/menu.class.php");
$obj = new Menu;
# Get system vars
$getsysvars=$dbcon->CacheExecute("Select * from sysvar where id=1")or DIE($dbcon->ErrorMsg()); 

$SiteName = $getsysvars->Fields("websitename")  ;
$Web_url  = $getsysvars->Fields("basepath")  ;
$cacheSecs = $getsysvars->Fields("cacheSecs")  ;
$admEmail = $getsysvars->Fields("emfaq") ;					//needed for admin only
$MM_email_usersubmit = $getsysvars->Fields("emendorse");	//User Submitted Article
$MM_email_from = $getsysvars->Fields("emfrom");				//return email web sent emails
$meta_description= $getsysvars->Fields("metadescription");	//meta desc
$meta_content = $getsysvars->Fields("metacontent");			//meta content
$systemplate_id = $getsysvars->Fields("template");
		
#SET DATABASE CACHING
$dbcon->cacheSecs = $cacheSecs;
	
#INCLUDE FUNCTIONS
require ($base_path."Connections/functions.php");

?>



