<?php 
/*********************
06-11-2003  v3.01
Module:  Template 
Description:  displays header  for all display pages. called from  header  index and article
GET VARS =id, list
To Do:  

four quires  
	gets css from type if $MM_type is set
	gets title short desc from articles  if $id


*********************/ 

$htmlheader.="<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">";

		if (($mod_id ) && ($_GET["id"])) {
			$headerdata=$dbcon->CacheExecute("SELECT title, shortdesc from articles where id= ".$_GET["id"]."") or DIE($dbcon->ErrorMsg());
			$headertitle = $headerdata->Fields("title");
			$meta_description = $headerdata->Fields("shortdesc");
			$meta_description = substr(trim($meta_description),0,250); 
			$meta_description = ereg_replace ("\"", "", $meta_description);
		}

		elseif  ($_GET["list"] == "type"){		$headertitle = $MM_typename;}
				else{$headertitle = $mod_name;}

		if ($mod_id != 2) {$headertitle = ":&nbsp;".$headertitle ;}
			else $headertitle = "";
		if ($headertitle =="Article"){$headertitle = "" ; }

$htmlheader.="<meta http-equiv=\"Description\" content=\"".$meta_description."\">";
$htmlheader.="<meta name=\"Keywords\" content=\"".$meta_content."\">";
$htmlheader.="<title>".$SiteName.$headertitle."</title>";
array($allsheets);
	$allsheets=explode(", ", $css);
	for ($i=0;  $i<count($allsheets);$i++) {
			$htmlheader.="<link href=\"".$Web_url.trim($allsheets[$i])."\" rel=\"stylesheet\" type=\"text/css\">";
	}
$htmlheader.="<script language=\"JavaScript\" src=\"Connections/functions.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"/custom/upload/script.js\">
</SCRIPT>
</head>";

?>
