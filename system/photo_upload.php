<?php

$my_max_file_size 	= "800000"; # in bytes
$image_max_width	= "1500";
$image_max_height	= "1500";
$the_path			= "/var/www/artandrevolution/gallery";

$registered_types = array(
					"application/x-gzip-compressed" 	=> ".tar.gz, .tgz",
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
					"application/octet-stream"			=> ".exe, .fla (etc)"
					); # these are only a few examples, you can find many more!

$allowed_types = array("image/bmp","image/gif","image/pjpeg","image/jpeg");

# --

function form($error=false) {

global $PHP_SELF,$my_max_file_size;

	if ($error) print $error . "<br><br>";
	
	print "\n<form ENCTYPE=\"multipart/form-data\"  action=\"" . $PHP_SELF . "\" method=\"post\">";
	print "\n<INPUT TYPE=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"" . $my_max_file_size . "\">";
	print "\n<INPUT TYPE=\"hidden\" name=\"task\" value=\"upload\">";
	print "\n<P>Upload a file";
	print "\n<BR>NOTE: Max file size is " . ($my_max_file_size / 1024) . "KB";
 	print "\n<br><INPUT NAME=\"the_file\" TYPE=\"file\" SIZE=\"35\"><br>";
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
	
	} else { # check if we are allowed to upload this file_type
		
		if (!in_array($the_file_type,$allowed_types)) {
			$error .= "\n<li>The file that you uploaded was of a type that is not allowed, you are only 
						allowed to upload files of the type:\n<ul>";
			while ($type = current($allowed_types)) {
				$error .= "\n<li>" . $registered_types[$type] . " (" . $type . ")</li>";
				next($allowed_types);
			}
			$error .= "\n</ul>";
		}
	
		if (ereg("image",$the_file_type) && (in_array($the_file_type,$allowed_types))) {
		
			$size = GetImageSize($the_file);
			list($foo,$width,$bar,$height) = explode("\"",$size[3]);
	
			if ($width > $image_max_width) {
				$error .= "\n<li>Your image should be no wider than " . $image_max_width . " Pixels</li>";
			}
			
			if ($height > $image_max_height) {
				$error .= "\n<li>Your image should be no higher than " . $image_max_height . " Pixels</li>";
			}
		
		}
		
		if ($error) {
			$error = $start_error . $error . "\n</ul>";
			return $error;
		} else {
			return false;
		}
	}
} # END validate_upload

# --


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

# --

function upload($the_file) {

global $the_path,$the_file_name;
	
	$error = validate_upload($the_file);
	if ($error) {
		form($error);
	} else { # cool, we can continue
		if (!@copy($the_file, $the_path . "/" . $the_file_name)) {
			form("\n<b>Something barfed, check the path to and the permissions for the upload directory</b>");
		} else {
			list_files();
			form();
		}
	}
} # END upload

# --

############ Start page

print "<html>\n<head>\n<title>Upload example</title>\n</head>\n<body>";

switch($task) {
	case 'upload':
		upload($the_file);
	break;
	default:
		form();
}

print "\n</body>\n</html>";

?>