<?php

/*
** DGS Search
** stats.php written by James Sella
** Copyright (c) 2000-2001 Digital Genesis Software, LLC. All Rights Reserved.
** Released under the GPL Version 2 License.
** http://www.digitalgenesis.com
*/

function stats($retVal, $q, $r, $o, $s, $timer) {
	global $config, $lang;

	$size = ($s < 1) ? count($retVal) : $s;
	$fonts = $config['fonts'];
	$target = $config['target'];
	$lowerBound = $o + 1;
	$upperBound = ($r + $o < $size) ? $o + $r : $size;
	$searchTime = sprintf('%.1f', getTime() - $timer);
	$searchTime = ($searchTime > 0.0) ? $searchTime : 0.1;

	if ($size > 0) {
		$lang['stats'] = str_replace('@QUERY@', $q, $lang['stats']);
		$lang['stats'] = str_replace('@SEARCHTIME@', $searchTime, $lang['stats']);
		$lang['stats'] = str_replace('@LOWERBOUND@', $lowerBound, $lang['stats']);
		$lang['stats'] = str_replace('@UPPERBOUND@', $upperBound, $lang['stats']);
		$lang['stats'] = str_replace('@TOTALRESULTS@', $size, $lang['stats']);
		printf("\t<DIV> <!-- Display Module: stats -->\n\t\t<FONT FACE=\"%s\">%s", $fonts, $lang['stats']);
	} else if (!$q) {
		$lang['noQuery'] = str_replace('@QUERY@', $q, $lang['noQuery']);
		$lang['noQuery'] = str_replace('@SEARCHTIME@', $searchTime, $lang['noQuery']);
		printf("\t\t<FONT FACE=\"%s\">%s", $fonts, $lang['noQuery']);
	} else {
		$lang['noResults'] = str_replace('@QUERY@', $q, $lang['noResults']);
		$lang['noResults'] = str_replace('@SEARCHTIME@', $searchTime, $lang['noResults']);
		printf("\t\t<FONT FACE=\"%s\">%s", $fonts, $lang['noResults']);
	}
	if ($config['timed'] && $q) {

		$lang['searchTime'] = str_replace('@QUERY@', $q, $lang['searchTime']);
		$lang['searchTime'] = str_replace('@TOTALRESULTS@', $size, $lang['searchTime']);
		$lang['searchTime'] = str_replace('@SEARCHTIME@', $searchTime, $lang['searchTime']);
		printf(' %s', $lang['searchTime']);
	}
	printf("\t</FONT>\n\t</DIV>\n");

	return true;
}

?>
