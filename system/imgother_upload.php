<?php
$mod_name="content";
  require("Connections/freedomrising.php");?>
   <?php  include("header.php");?>
   <h2><?php echo helpme(""); ?>Image Upload</h2>
   <?php
$my_max_file_size 	= "18000000"; # in bytes
$image_max_width	= "50000";
$image_max_height	= "50000";
$the_path1			= "".$base_path_amp."img/pic/";
$the_path2			= "".$base_path_amp."img/thumb/";
$the_path3			= "".$base_path_amp."img/original/";

$registered_types = array(
					"image/bmp" 						=> ".bmp, .ico",
					"image/gif" 						=> ".gif",
					"image/pjpeg"						=> ".jpg, .jpeg",
					"image/jpeg"						=> ".jpg, .jpeg",
					
					); # these are only a few examples, you can find many more!

$allowed_types = array("application/pdf","image/gif","image/pjpeg","image/jpeg");

# --

function form($error=false) {

global $PHP_SELF,$my_max_file_size;

	if ($error) print $error . "<br><br>";
	
	print "\n<form ENCTYPE=\"multipart/form-data\"  action=\"" . $PHP_SELF . "\" method=\"post\"><div class=list_table><table class=list_table>";
	print "\n<tr class=intitle><td colspan=2><INPUT TYPE=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"" . $my_max_file_size . "\">";
	print "\n<INPUT TYPE=\"hidden\" name=\"task\" value=\"upload\">";
	print "\n Upload a file</td></tr>";
	print "\n<tr><td colspan=2><br>NOTE: Max file size is " . ($my_max_file_size / 1024) . "KB<br></td></tr>";
 	print "\n<tr><td>Optimized: </td><td><INPUT NAME=\"the_file1\" TYPE=\"file\" SIZE=\"35\"> </td</tr>";
	print "\n<tr><td>Thumbnail: </td><td><INPUT NAME=\"the_file2\" TYPE=\"file\" SIZE=\"35\"> </td</tr>";
	print "\n<tr><td>Full Size: </td><td><INPUT NAME=\"the_file3\" TYPE=\"file\" SIZE=\"35\"> </td</tr><tr><td colspan=2>If you do not have multiple sizes of this file please upload the same file in all fields</td></tr></table></div>";
	print "\n<input type=\"submit\" Value=\"Upload\">";
	print "\n</form>";

} # END form

# --



# --

function validate_upload($the_file) {

global $my_max_file_size, $image_max_width, $image_max_height,$allowed_types,$the_file_type,$registered_types;
	
	$start_error = "\n<b>Error:</b>\n<ul>";
	
	if ($the_file == "none") { # do we even have a file?
	
		$error .= "\n<li>You did not upload anything!</li>";
	
	} else { 
		
		if ($error) {
			$error = $start_error . $error . "\n</ul>";
			return $error;
		} else {
			return false;
		}
	}
} # END validate_upload

# --


function list_files($path) {
 print "<img src =\"$path\"<br>";

	/* 
	$handle = dir($the_path);
	print "\n<b>Uploaded files:</b><br>";
	while ($file = $handle->read()) {
		if (($file != ".") && ($file != "..")) {
			print "\n" . $file . "<br>";
	   }
	}
	print "<hr>"; */
}

# --

function upload($the_file, $the_path, $name) {

//global $the_file1_name;
	
	$error = validate_upload($the_file);
	if ($error) {
		form($error);
	} else { # cool, we can continue
	$path = $the_path . $name;
		if (!@copy($the_file, $path)) {
			form("\n<b>Something barfed, check the path to and the permissions for the upload directory</b>");
		} else {
		chmod($path,0755);
			list_files($path);
			//form();
		}
	}
} # END upload

# --

############ Start page


switch($task) {
	case 'upload':
	$name = $the_file1_name;
		upload($the_file1, $the_path1, $name);
		$name = $the_file2_name;
		upload($the_file2, $the_path2, $name);
		$name = $the_file3_name;
		upload($the_file3, $the_path3, $name);
	break;
	default:
		form();
}

  

?><?php include("footer.php");?>