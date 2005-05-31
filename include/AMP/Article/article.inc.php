<?php
/*********************
 06-18-2003  v3.01
Module:  Content
Description:  display page  for  articles
CSS:    text,  photocaption, subtitle, title,  author,  date, 
To Do:

*********************/ 
//get data and check to see if we display the page or redirect
if (isset($_GET['preview']) && $_GET["preview"] == 1) {
	$Recordset1=$dbcon->CacheExecute("SELECT * FROM articles WHERE id = $MM_id") or DIE($dbcon->ErrorMsg());
} else {
	$Recordset1=$dbcon->CacheExecute("SELECT * FROM articles WHERE id = $MM_id and publish=1") or header("Location: search.php");				//DIE($dbcon->ErrorMsg());
}

if ($Recordset1->RecordCount() == 0) {
	header ("Location: index.php");
}

if ($Recordset1->Fields("linkover") == 1){
	$goodbye = $Recordset1->Fields("link");
	header ("Location: $goodbye") ;
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
	$fpathtoimg = AMP_LOCAL_PATH.'/'.$NAV_IMG_PATH .$Recordset1->Fields("pselection")."/".$Recordset1->Fields("picture");
	$pathtoimg = $NAV_IMG_PATH .$Recordset1->Fields("pselection")."/".$Recordset1->Fields("picture");
	$imageInfo = getimagesize($fpathtoimg); 
	$pwidth = $imageInfo[0]; 
	$pheight = $imageInfo[1];

	#set table
	echo '<table width="' . $pwidth . '" border="0" align="';
	echo ($Recordset1->Fields("alignment") == "left") ? "left" : "right";
	echo '" cellpadding="0" cellspacing="0"><tr><td>';
	#set image
	echo '<img src="' . $pathtoimg . '" alt="' . $Recordset1->Fields("alttag") . '" hspace="4" vspace="4" border="0" class="img_main">';
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
