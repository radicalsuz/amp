<?php

/*
** DGS Search
** nav.php written by James Sella
** Copyright (c) 2000-2001 Digital Genesis Software, LLC. All Rights Reserved.
** Released under the GPL Version 2 License.
** http://www.digitalgenesis.com
*/

function nav($retVal, $q, $r, $o, $s, $timer) {
	global $config, $lang;

	$size = ($s < 1) ? count($retVal) : $s;
	$fonts = $config['fonts'];
	$navColor = $config['navColor'];
	$target = $config['target'];
	$installBase = $config['installBase'];
	$urlBase = $config['urlBase'];
	$siteBase = $config['siteBase'];

	$searchURL = explode($siteBase, $installBase);
	$searchURL = $SiteName."/search.php";
	//$searchURL = $urlBase . str_replace('\\', '/', $searchURL[1]) . '/search.php';

	if ($size > 0) {
		printf("\t<DIV> <!-- Display Module: nav -->\n\t\t<DL>\n\t\t\t<DT>\n\t\t\t\t<FONT FACE=\"%s\" COLOR=\"%s\">%s: </FONT><FONT FACE=\"%s\">", $fonts, $navColor, $lang['resultPages'], $fonts);
		$dispR = ($r == $config["maxResults"]) ? 0 : $r;
		$dispQ = str_replace(' ', '+', $q);
		$currPage = ($o + $r) / $r;
		if ($currPage > 1)
			printf("<A HREF=\"%s?q=%s&r=%d&o=%d&s=%d\" TARGET=\"%s\">%s</A> ", $searchURL, $dispQ, $r, $o - $r, $size, $target, $lang['prev']);
		for ($i = 0; $i < $size / $r; $i++) {
			if ($currPage == $i + 1)
				printf("<B>%d</B> ", $i + 1);
			else
				printf("<A HREF=\"%s?q=%s&r=%d&o=%d&s=%d\" TARGET=\"%s\">%d</A> ", $searchURL, $dispQ, $dispR, $i * $dispR, $size, $target, $i + 1);
		}
		if ($currPage < $size / $r)
			printf("<A HREF=\"%s?q=%s&r=%d&o=%d&s=%d\" TARGET=\"%s\">%s</A> ", $searchURL, $dispQ, $r, $o + $r, $size, $target, $lang['next']);
		printf("</FONT>\n\t\t\t</DT>\n\t\t</DL>\n\t</DIV>\n");
	}
	return true;
}

?>
