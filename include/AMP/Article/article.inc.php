<?php
/*********************
 06-18-2003  v3.01
Module:  Content
Description:  display page  for  articles
CSS:    text,  photocaption, subtitle, title,  author,  date, 
To Do:

*********************/ 
//allow for versioned info;
$art_table = "articles";
$art_id_field = "id";
$art_id_value = $MM_id;
if (isset($_GET['vid']) && $_GET['vid']) {
    $art_table = "articles_version";
    $art_id_field = "vid";
    $art_id_value = $_GET['vid'];
}
//get data and check to see if we display the page or redirect
if (isset($_GET['preview']) && $_GET["preview"] == 1) {
	$Recordset1=$dbcon->CacheExecute("SELECT * FROM $art_table WHERE $art_id_field = $art_id_value") or DIE($dbcon->ErrorMsg());
    $MM_type = $Recordset1->Fields("type");
} else {
	$Recordset1=$dbcon->CacheExecute("SELECT * FROM articles WHERE id = $MM_id and publish=1") or ampredirect("search.php");				//DIE($dbcon->ErrorMsg());
}

if ($Recordset1->RecordCount() == 0) {
	ampredirect("index.php");
}

if ($Recordset1->Fields("linkover") == 1){
	$goodbye = $Recordset1->Fields("link");
	ampredirect($goodbye) ;
}


#table frame
echo '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="text"><tr><td>';
#title
echo '<p class="title">'  .  converttext($Recordset1->Fields("title"))  .  '</p>';
#subtitle
if ($Recordset1->Fields("subtitile") != (NULL)) { 
	echo '<span class="subtitle">' . converttext($Recordset1->Fields("subtitile")) . '</span><br>';
} 
#author
if (trim($Recordset1->Fields("author")) != (NULL)) {
	echo '<span class="author">by&nbsp;' . converttext($Recordset1->Fields("author")) . '</span>';
}
if (trim(($Recordset1->Fields("author")) != (NULL)) &&  ($Recordset1->Fields("source") != (NULL))) { 
	echo ',&nbsp;';
} 
#source
if ($Recordset1->Fields("source") != (NULL)) { 
	echo '<span class="author">';
	if ($Recordset1->Fields("sourceurl") != NULL){
		echo "<a href=\"".$Recordset1->Fields("sourceurl")."\">";
	}
	echo $Recordset1->Fields("source");
	if ($Recordset1->Fields("sourceurl") != NULL){
		echo "</a>";
	}
	echo '</span>';
} 
if (($Recordset1->Fields("author") != (NULL))or  ($Recordset1->Fields("source") != (NULL))) { 
	echo '<br>';
	} 
#contact 
if ($Recordset1->Fields("contact") != (NULL)) { 
	echo '<span class="author">Contact:&nbsp;' . converttext($Recordset1->Fields("contact")) . '</span><br>';
} 
#date
if ($Recordset1->Fields("usedate") != (1))  { 
	if ($Recordset1->Fields("date") != "0000-00-00") {
		echo '<span class="date">' . DoDate( $Recordset1->Fields("date"), 'F jS, Y') . '</span><br>';
	 } 
}

echo '</td></tr><td></td><tr><td  class="text"><br>';
#image 
if ($Recordset1->Fields("picuse") == (1)) {  
    $pselection = ($Recordset1->Fields("pselection")?$Recordset1->Fields("pselection"):'pic');
	$fpathtoimg = AMP_LOCAL_PATH.'/'.$NAV_IMG_PATH .$pselection."/".$Recordset1->Fields("picture");
	$pathtoimg = $NAV_IMG_PATH .$Recordset1->Fields("pselection")."/".$Recordset1->Fields("picture");
	$pathtoimg2 = $Web_url.$NAV_IMG_PATH."original/".$Recordset1->Fields("picture");
    $imageInfo = getimagesize($fpathtoimg); 
	$pwidth = $imageInfo[0]; 
	$pheight = $imageInfo[1];

	#set table
	echo '<table width="' . $pwidth . '" border="0" align="';
	echo ($Recordset1->Fields("alignment") == "left") ? "left" : "right";
	echo '" cellpadding="0" cellspacing="0"><tr><td>';
	#set image
	echo '<a href="'. $pathtoimg2 .'" target="_blank"> <img src="' . $pathtoimg . '" alt="' . $Recordset1->Fields("alttag") . '" hspace="4" vspace="4" border="0" class="img_main"></a>';
	#set caption
	echo '</td></tr><Tr align="center"><td width="' .  $pwidth  . '" class="photocaption">' . $Recordset1->Fields("piccap") . '</td>';
	echo '</TR></table>';
}  
#post text 

echo '<p class="text"> ';
 
if ($Recordset1->Fields("html") == (0)) {   
	echo hotword(converttext($Recordset1->Fields("test"))); 
}  
if ($Recordset1->Fields("html") == (1)) {  
	echo hotword($Recordset1->Fields("test"));
} 	
if ($Recordset1->Fields("comments") == (1)){
	include ("comments.inc.php"); 
}
if ($Recordset1->Fields("doc") != NULL){  
	include ("docbox.inc.php"); 
}

echo "</td></tr></table>";

?>
