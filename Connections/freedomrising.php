<?php 
$ampasp =1;
if ($ampasp == 1) {
  ADOLoadCode("mysql");
   $ampdbcon=&ADONewConnection("mysql");
   $ampdbcon->Connect("localhost","david","havlan","amp");
   $AMPsql = "SELECT * FROM system where server = '".$HTTP_SERVER_VARS['SERVER_NAME']."'";

  $ampversion=$ampdbcon->Execute($AMPsql)or DIE($ampdbcon->ErrorMsg()); 
$AmpPath = $ampversion->Fields("amppath");
$MX_type ="type"; //  $ampversion->Fields("typefield");
$MX_top = 1; // $ampversion->Fields("toptypelevel");

$ConfigPath = $AmpPath."custom/config.php";
$ConfigPath2 = $AmpPath."custom/config.php";
$ConfigPath3 = $AmpPath."custom/config.php";
}
else { 
$MX_type ="type";
$MX_top =1;
$ConfigPath = "custom/config.php";
$ConfigPath2 = "../custom/config.php";
$ConfigPath3 = "../../custom/config.php";
}


//header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
//header("Cache-Control: post-check=0, pre-check=0", false);
//header("Pragma: no-cache");

if (isset($subdir)){
require_once("$ConfigPath2");  }
elseif (isset($subdir2)){
require_once("$ConfigPath3");  }
else {
require_once("$ConfigPath"); }
	if(!isset($PHP_SELF)){ 
		$PHP_SELF=getenv("SCRIPT_NAME"); 
	}
	if (!isset($QUERY_STRING)){
		$QUERY_STRING="";
	}
	if (!isset($REQUEST_URI)){
		$REQUEST_URI=$PHP_SELF;
	}
   ADOLoadCode($MM_DBTYPE);
   $dbcon=&ADONewConnection($MM_DBTYPE);
   $dbcon->Connect($MM_HOSTNAME,$MM_USERNAME,$MM_PASSWORD,$MM_DATABASE);
    $getsysvars=$dbcon->CacheExecute("Select  emfaq, websitename, basepath, cacheSecs from sysvar where id=1")or DIE($dbcon->ErrorMsg()); 
	$SiteName = $getsysvars->Fields("websitename")  ;
	$Web_url  = $getsysvars->Fields("basepath")  ;
	$cacheSecs = $getsysvars->Fields("cacheSecs")  ;
	$admEmail = $getsysvars->Fields("emfaq") ;
   $dbcon->cacheSecs = $cacheSecs;
  
  	//set magic quotes
//set_magic_quotes_runtime (1);
if (get_magic_quotes_gpc()==1)
{ $MM_sysvar_mq ="1";
}
else
{ $MM_sysvar_mq ="0";
} 
//define functions

function DoDateTime($theObject, $NamedFormat) {
  if ($theObject == ($null)){ $parsedDate = '';}
	else {
	ereg("([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})", $theObject, $tgRegs);
	$parsedDate=date($NamedFormat, mktime($tgRegs[4],$tgRegs[5],$tgRegs[6],$tgRegs[2],$tgRegs[3],$tgRegs[1])); }
	if ($parsedDate == "12/31/69") { $parsedDate = NULL;}
	return $parsedDate;
}
function DoTimeStamp($theObject, $NamedFormat) {
  if ($theObject == ($null)){ $parsedDate = '';}
	else {
	ereg("([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})", $theObject, $tgRegs);
	$parsedDate=date($NamedFormat, mktime($tgRegs[4],$tgRegs[5],$tgRegs[6],$tgRegs[2],$tgRegs[3],$tgRegs[1])); }
	if ($parsedDate == "12/31/69") { $parsedDate = NULL;}
	return $parsedDate;
}

function DateConvertIn($date) {
if ((ereg ("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $date, $regs)) or (empty($date))) {
    $date = "$regs[3]-$regs[1]-$regs[2]";
	
} else {
   Die; echo "Invalid date format: $date";
	
}return $date;}

function DateConvertOut($date) {
if (ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $date, $regs)) {
    echo "$regs[2]-$regs[3]-$regs[1]";
} }


function converttext($text){
		 $text = ereg_replace("(([a-z0-9_\.-]+)(\@)[a-z0-9_-]+([\.][a-z0-9_-]+)+)", "<a href=\"mailto:\\0\">\\0</a>", $text);
//$text = preg_replace(" /([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i", "<A TARGET=\"_blank\" HREF=\"$1\">$1</A>", $text); //make all URLs links

	   $text = ereg_replace(" [[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\" target=_offsite>\\0</a>", $text); 
					 $text= nl2br($text) ;
	   return $text;}
	   
 function hotword($text){
     global $dbcon;
	$getwordlist=$dbcon->CacheExecute("Select  word, url from hotwords where publish =1 ")or DIE($dbcon->ErrorMsg()); 
	 while (!$getwordlist->EOF) {
	 $word = " ".$getwordlist->Fields("word")." ";
	  $url = $getwordlist->Fields("url");
	  $text = ereg_replace("$word", " <a href=\"$url\">".$getwordlist->Fields("word")."</a> ", $text);
	  $getwordlist->MoveNext();
}
		

	   return $text;}
	   
function DoDate($theObject, $NamedFormat) {
	if ($theObject == ($null)){ $parsedDate = '';}
	elseif ($theObject == ('0000-00-00')){ $parsedDate = '';}
	else {
	ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})", $theObject, $tgRegs);
	if (($NamedFormat == "F, Y") & ($tgRegs[2] == "00"))
	 {$NamedFormat = "Y" ;
	$parsedDate= $tgRegs[1];}
	else {
	$parsedDate=date($NamedFormat, mktime(0,0,0,$tgRegs[2],$tgRegs[3],$tgRegs[1])); }}
	if ($parsedDate == "12/31/69") { $parsedDate = NULL;}
	return $parsedDate;
}


function statelist($selectname) {
    global $dbcon;
	echo "<select NAME=\"$selectname\">";
	$state=$dbcon->CacheExecute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();
   
     echo  "<option value=\"\">Select State</option>";
                          if ($state__totalRows > 0){
    $state__index=0;
    $state->MoveFirst();
    WHILE ($state__index < $state__totalRows){

               echo       "<OPTION VALUE=\"".$state->Fields("id")."\">".$state->Fields("statename")."</OPTION>";
                      
      $state->MoveNext();
      $state__index++;
    }
    $state__index=0;  
    $state->MoveFirst();
  }
  echo "</select>";
  $state->Close();
  }
  
   function sectionimage($string){
global $dbcon, $MM_type; 
$sectionimg=$dbcon->CacheExecute("Select flash from articletype where id = $MM_type") or DIE($dbcon->ErrorMsg());
if ($sectionimg->Fields("flash") != NULL){
echo $sectionimg->Fields("flash");
}
else echo  $string;
}
  
  function evalhtml($string){
global $dbcon, $MM_type, $MM_parent, $MM_typename, $HTTP_GET_VARS, $list, $id, $MM_issue, $userper, $MM_region, $navalign;
   $pos = 0;
   $start = 0;

   /* Loop through to find the php code in html...  */
  while ( ($pos = strpos( $string, '<?php', $start )) != FALSE ) {
      /* Find the end of the php code.. */
       $pos2 = strpos( $string, "?>", $pos + 5);

       /* Eval outputs directly to the buffer. Catch / Clean it */ 
       ob_start();
     eval( substr( $string, $pos + 5, $pos2 - $pos - 5) );
      $value = ob_get_contents();
       ob_end_clean();

       /* Grab that chunk!  */
       $start = $pos + strlen($value);
      $string = substr( $string, 0, $pos ) . $value . substr( $string,
$pos2 + 2);
       }
return $string;
}

function getnavs($sql) {
	global $dbcon, $navside, $MM_type, $mod_id;
			$navsqlsel="SELECT navid FROM nav  ";
			$navsqlend =" and position like  '%$navside%' order by position asc";
 			$navsql =$navsqlsel.$sql.$navsqlend;
        	$navcalled=$dbcon->CacheExecute("$navsql") or DIE($dbcon->ErrorMsg());
	return $navcalled;
	}
?>
  
