#This file will be used directly by both php and perl, so it must be valid in both languages.
$tes_max_upload = 26214400; #Max size allowed for uploaded files (25MB)
$tes_tmp_dir="/tmp"; #temporary directory, must be writable by both cgi-script and php scripts
$tes_upload_dir="/tmp/uploads"; #Where to permanently store the uploaded files, when the upload is completed
							#This directory must be writable by php
$tes_cgi_dir = "/cgi-bin/tesUpload"; #Webpath to folder that contains upload.cgi (can be entire url)
