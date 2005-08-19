<?php
$mod_name="content";

require_once("Connections/freedomrising.php");

$listtitle ="Documents";
$filename="docdir.php";

//delete file 
if ( isset($_GET['actdel']) && $actdel=$_GET['actdel'] ){
	$dir_name=AMP_LOCAL_PATH."/downloads/";
	unlink($dir_name.$actdel);
}

// get file list
$dir_name=AMP_LOCAL_PATH."/downloads";
$dir = opendir($dir_name);
$basename = basename($dir_name);
$fileArr = array();

while ($file_name = readdir($dir)){
	if (($file_name !=".") && ($file_name != "..")) {
    #Get file modification date...<a href="calendar_type.php">calendar_type.php</a>
        $fName = "$dir_name/$file_name";
    	$fTime = filemtime($fName);
    	$fileArr[$file_name] = $fTime;    
  	}
}

# Use arsort to get most recent first
# and asort to get oldest first
arsort($fileArr);
$numberOfFiles = sizeOf($fileArr);

for($t=0;$t<$numberOfFiles;$t++){
    $thisFile = each($fileArr);
    $thisName = $thisFile[0];
    $thisTime = $thisFile[1];
    $thisTime = date("M d Y", $thisTime);
	
	$fieldsarray[$t]['Document'] = '<a href="../downloads/'.$thisName.'" target="_blank">'.$thisName.'</a>';
	$fieldsarray[$t]['Date'] = $thisTime;
	$fieldsarray[$t]['Delete'] = '<a href="docdir.php?actdel='.urlencode($thisName).'">delete</a>';
}
closedir ($dir);

include ("header.php");
listpage_basic($listtitle,$fieldsarray,$filename);
include ("footer.php");
?>
