<?php

/*
** DGS Search
** query.php written by James Sella
** Copyright (c) 2000-2001 Digital Genesis Software, LLC. All Rights Reserved.
** Released under the GPL Version 2 License.
** http://www.digitalgenesis.com
*/

function query($retVal, $q, $r, $o, $s, $timer) {
	global $config, $lang;

	$installBase = $config['installBase'];
	$urlBase = $config['urlBase'];
	$siteBase = $config['siteBase'];

	$fonts = $config['fonts'];
	$target = $config['target'];
	$dispR = ($r == $config['maxResults']) ? 0 : $r;

	$searchURL = explode($siteBase, $installBase);
	//$searchURL = $urlBase . str_replace('\\', '/', $searchURL[1]) . '/search.php';     
$searchURL = "search.php";
	printf("\t<DIV> <!-- Display Module: query -->\n\t\t<FORM METHOD=\"get\" ACTION=\"%s\" TARGET=\"%s\">\n\t\t\t<FONT FACE=\"%s\">%s</FONT>&nbsp;\n\t\t\t<INPUT TYPE=\"text\" NAME=\"q\" VALUE=\"%s\" SIZE=\"30\" TABINDEX=\"1\">\n\t\t\t<INPUT TYPE=\"hidden\" NAME=\"r\" VALUE=\"%d\">\n\t\t\t<INPUT TYPE=\"submit\" VALUE=\"%s\">\n\t\t</FORM>\n\t</DIV>\n", $searchURL, $target, $fonts, $lang['query'], $q, $dispR, $lang['submit']);

	return true;
}

?>
