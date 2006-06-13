<?php
/**
* Receive uploaded file and clean up temp files
*/
function tes_receive($sid) {
	global $tes_tmp_dir,$tes_upload_dir;
	$sid = ereg_replace("[^a-zA-Z0-9]","",$sid);
	$file = $tes_tmp_dir.'/'.$sid.'_qstring';
	if(!file_exists($file)) {
		return false;
	}
	$qstr = join("",file($file));
	unlink("$tes_tmp_dir/{$sid}_qstring");

	$q = array();
	parse_str($qstr,$q);
	
	$fn = $q['file']['name'][0];
	$b_pos = strrpos($fn, '\\');$f_pos = strrpos($fn, '/');
	if($b_pos == false and $f_pos == false) {
		$file_name = $fn;
	} else {
		$file_name = substr($fn, max($b_pos,$f_pos)+1);
	}
	/*****
	Before moving the file to its final destination, you might want to check that the file
	is what you expect it to be, for example check that it really is an image file if your
	building an image uploader.
	******/
	rename($q['file']['tmp_name'][0], "$tes_upload_dir/$fn");
	tes_cleanup($sid);
	return $file_name;
}

/**
* Clean up temporary files
*/
function tes_cleanup($sid) {
	global $tes_tmp_dir;
	$files = array("_flength","_postdata","_err","_signal","_qstring");
	foreach($files as $file) {
		if(file_exists("$tes_tmp_dir/$sid$file")) {
			unlink("$tes_tmp_dir/$sid$file");
		}
	}
}
?>
