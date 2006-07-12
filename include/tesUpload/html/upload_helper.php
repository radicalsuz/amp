<?php
require_once("tesUpload/html/read_settings.php");

if(!is_writable($tes_tmp_dir)) {
	echo "Warning: PHP can't write to temp dir ($tes_tmp_dir).<br />";
}
if(!file_exists($tes_upload_dir)) {
	mkdir($tes_upload_dir);
}
if(!is_writable($tes_upload_dir)) {
	echo "Warning: PHP can't write to upload dir ($tes_upload_dir).<br />";
}

function tes_upload_form($name,$title) {
	global $tes_cgi_dir;
	$sid = md5(uniqid(rand()));
	?>
	<form enctype="multipart/form-data" action="<?php echo $tes_cgi_dir; ?>/upload.cgi?sid=<?php echo $sid; ?>" method="post" target="iframe_<?php echo $name; ?>" />
		<div class="inputhead"><?php echo $title; ?></div>
		<input class="input" type="file" name="<?php echo $name; ?>" onchange="beginUpload(this,'<?php echo $sid; ?>');" />
		<div class="progresscontainer" style="display: none;"><div class="progressbar" id="<?php echo $name ?>_progress"></div></div>
	</form>
	<iframe name="iframe_<?php echo $name; ?>" style="border: 0;width: 0px;height: 0px;"></iframe>
	<?php
}

function tes_upload_value($name) {
	?>
	<input id="<?php echo $name; ?>" type="hidden" name="<?php echo $name; ?>" value="" />
	<?php
}
?>
