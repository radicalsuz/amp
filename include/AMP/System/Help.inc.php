<?php

function helpme2($link) {
	$output = "<a href=\"javascript:void(0)\" ONCLICK=\"open('help.php?file=$link','miniwin','location=1,scrollbars=1,resizable=1,width=550,height=400')\"><img src=\"images/help.png\" width=\"15\" height=\"15\" border=\"0\" align=\"absmiddle\"></a>&nbsp;";
	return $output;
}
	 
function helpme($link) {

	global $PHP_SELF;
	$output="<table width=\"15\" border=\"0\" align=\"right\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><a href=\"javascript:void(0)\" ONCLICK=\"open('help.php?file=";
	
	$pos = strrpos($PHP_SELF, "/");
	$pos = substr($PHP_SELF, ($pos + 1), -4);
	$output.= $pos;
	$output.= "#";
	$output.= $link;
	$output.="','miniwin','location=1,scrollbars=1,resizable=1,width=550,height=400')\"><img src=\"images/help.png\" border=\"0\" align=\"absmiddle\"></a></td></tr></table>";
	return $output;

}
?>
