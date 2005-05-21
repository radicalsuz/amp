<?php
$mod_name="content";
require("Connections/freedomrising.php");

set_time_limit(0);
$my_max_file_size 	= "65000000"; # in bytes
$image_max_width	= "50000";
$image_max_height	= "50000";
$the_path			= AMP_LOCAL_PATH."/downloads/";

$registered_types = array(
					"application/x-gzip-compressed" 	=> ".tar.gz, .tgz",
					"application/pdf"					=> ".pdf",		
					"application/x-zip-compressed" 		=> ".zip",
					"application/x-tar"					=> ".tar",
					"text/plain"						=> ".html, .php, .txt, .inc (etc)",
					"image/bmp" 						=> ".bmp, .ico",
					"image/gif" 						=> ".gif",
					"image/pjpeg"						=> ".jpg, .jpeg",
					"image/jpeg"						=> ".jpg, .jpeg",
					"application/x-shockwave-flash" 	=> ".swf",
					"application/msword"				=> ".doc",
					"application/vnd.ms-excel"			=> ".xls",
					"application/octet-stream"			=> ".exe, .fla (etc)",
					"application/vnd.ms-access"         => ".mdb",
					"application/wordperfect"			=> ".wpd"

					); # these are only a few examples, you can find many more!
$allowed_types = array("application/pdf", "application/msword", "application/vnd.ms-excel", "image/gif","image/pjpeg","image/jpeg", "application/vnd.ms-access", "text/plain", "application/x-zip-compressed");

function form($error=false) {
	global $my_max_file_size;
	if ($error) print $error . "<br><br>";
	print "\n<form ENCTYPE=\"multipart/form-data\"  action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"post\">";
	print "\n<INPUT TYPE=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"" . $my_max_file_size . "\">";
	print "\n<INPUT TYPE=\"hidden\" name=\"task\" value=\"upload\">";
	print "\n<div class=list_table><table class=list_table><tr class=intitle><td colspan=2>Upload a file</td></tr>";
	print "\n<tr><td colsapn=2><b>NOTE: Max file size is " . ($my_max_file_size / 1024) . "KB</b></td></tr>";
 	print "\n<tr><td>File to upload:</td><td><INPUT NAME=\"the_file\" TYPE=\"file\" SIZE=\"35\"></td></tr></table></div>";
	print "\n<input type=\"submit\" Value=\"Upload\">";
	print "\n</form>";
} 

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
} 

function list_files() {
	global $the_path;
	$handle = dir($the_path);
	print "\n<b>Uploaded files:</b><br>";
	while ($file = $handle->read()) {
		if (($file != ".") && ($file != "..")) {
			print "\n" . $file . "<br>";
	   }
	}
	print "<hr>";
}

function upload($the_file) {
	global $the_path,$the_file_name;
	$error = validate_upload($the_file);
	if ($error) {
		form($error);
	} else { # cool, we can continue
		if (!@copy($the_file, $the_path . $the_file_name)) {
			form("\n<b>Error, check the path to and the permissions for the upload directory</b>");
		} else {
		chmod($the_path . $the_file_name,0755);
			list_files();
			form();
		}
	}
} 

############ Start page

include("header.php");

echo  "<h2>Document Upload</h2>";

switch($task) {
	case 'upload':
		upload($the_file);
	break;
	default:
		form();
}

include("footer.php");

?>
