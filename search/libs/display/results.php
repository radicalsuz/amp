<?php

/*
** DGS Search
** results.php written by James Sella
** Copyright (c) 2000-2001 Digital Genesis Software, LLC. All Rights Reserved.
** Released under the GPL Version 2 License.
** http://www.digitalgenesis.com
*/

function results($retVal, $q, $r, $o, $s, $timer) {
	global $config, $lang;

	$descHeight = $config['descHeight'];
	$size = ($s < 1) ? count($retVal) : $s;
	$fonts = $config['fonts'];
	$infoBarColor = $config['infoBarColor'];
	$target = $config['target'];
	$headerColor = $config['headerColor'];
	$infoBar = $config['infoBar'];
	$infoBarFormat = $config['infoBarFormat'];
	$translate = $config['translate'];
	$translateFrom = $config['translateFrom'];
	$lowerBound = $o + 1;
	$upperBound = ($r + $o < $size) ? $o + $r : $size;
	$dispR = ($r == $config['maxResults']) ? 0 : $r;

	if ($size > 0) {
		printf("\t<DIV> <!-- Display Module: results -->\n");
		$i = 0;
		reset($retVal);
		while (list(, $match) = each($retVal)) {
			$i++;
			if ($i <= $o)
				continue;
			if ($i > $o + $r)
				break;
			$link = $match['link'];
			$url = str_replace(' ', '%20', $match['url']);
			$fileSize = $match['fileSize'] / 1024;
			$lastMod = $match['lastMod'];
			$descArray = $match['description'];
			$source = $match['source'];
			printf("\t\t<DL>\n\t\t\t<DT>\n\t\t\t\t<FONT FACE=\"%s\" SIZE=\"-1\">\n\t\t\t\t\t<B>%d. <A HREF=\"%s\" TARGET=\"%s\">%s</A></B>", $fonts, $i, $url, $target, $link);
			if ($translate) {
				$translateURL = 'http://babel.altavista.com/translate.dyn?url=' . str_replace(':', '%3A', str_replace('/', '%2F', $url));
				if (eregi("^[a-z][a-z]_[a-z][a-z]$", $translateFrom)) {
					$translateURL .= '&lp=' . $translateFrom;
				}
				printf(" - <A HREF=\"%s\" TARGET=\"%s\">%s</A>", $translateURL, $target, $lang['translate']);
			}
			printf("\n\t\t\t\t</FONT>\n\t\t\t</DT>\n");
			while (list(, $desc) = each($descArray)) {
				printf("\t\t\t<DD>\n\t\t\t\t<FONT FACE=\"%s\" SIZE=\"-1\">\n\t\t\t\t\t%s\n\t\t\t\t</FONT>\n\t\t\t</DD>\n", $fonts, $desc);
			}
			if ($infoBar) {
				$buffer = $infoBarFormat;
				if ($url) {
					$buffer = str_replace('@DASH-URL@', '-', $buffer);
					$buffer = str_replace('@FULLURL@', $url, $buffer);
					$buffer = str_replace('@URL@', eregi_replace('^[a-z]+://', '', $url), $buffer);
					$buffer = str_replace('@HOST@', eregi_replace('^[a-z]+://([^/]+)..*$', '\\1', $url), $buffer);
				}
				if ($fileSize) {
					$buffer = str_replace('@DASH-SIZE@', '-', $buffer);
					$buffer = str_replace('@SIZE@', sprintf('%dk', $fileSize), $buffer);
					$buffer = str_replace('@SIZE1@', sprintf('%0.1fk', $fileSize), $buffer);
					$buffer = str_replace('@SIZE2@', sprintf('%0.2fk', $fileSize), $buffer);
				}
				if ($lastMod) {
					$buffer = str_replace('@DASH-LASTMOD@', '-', $buffer);
					$buffer = str_replace('@LASTMOD@', $lastMod, $buffer);
				}
				if ($target) {
					$buffer = str_replace('@DASH-TARGET@', '-', $buffer);
					$buffer = str_replace('@TARGET@', $target, $buffer);
				}
				/* Eat up any extra variables. */
				$buffer = ereg_replace('(@[A-Z-]+@)', '<!-- Unused: \\1 -->', $buffer);
				if (strlen(trim($buffer)) > 0) {
					printf("\t\t\t<DD>\n\t\t\t\t<FONT FACE=\"%s\" COLOR=\"%s\" SIZE=\"-1\">\n\t\t\t\t\t%s\n\t\t\t\t</FONT>\n\t\t\t</DD>\n", $fonts, $infoBarColor, $buffer);
				}
			}
			printf("\t\t</DL>\n");
		}
	}
	printf("\t</DIV>\n");

	return true;
}

?>
