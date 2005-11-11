<?php

require_once('AMP/BaseDB.php');

$filelist = AMPfile_list('img/thumb/'); 
unset($filelist['']);
$script = '
<SCRIPT TYPE="text/javascript">
<!--
function setImage( imgname ) {
		url_item = parent.document.getElementById("f_url");

		url_item.value = imgname;
		//window.document.forms["Insert_Image"].elements["url"].value = imgname;
		parent.onPreview();
}
-->
</SCRIPT>';

print "<HTML><HEAD>$script</HEAD><BODY>";
foreach ($filelist as $picfile) {
		print "<A href='javascript:setImage(\"/img/pic/$picfile\");'><IMG
		SRC=\"/img/thumb/$picfile\"><BR>$picfile</A><P>\n";
}
print "</BODY></HTML>";
?>
