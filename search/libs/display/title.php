<?php

/*
** DGS Search
** title.php written by James Sella
** Copyright (c) 2000-2001 Digital Genesis Software, LLC. All Rights Reserved.
** Released under the GPL Version 2 License.
** http://www.digitalgenesis.com
*/

function title($retVal, $q, $r, $o, $s, $timer) {
	global $config, $lang;

	printf("\t<DIV> <!-- Display Module: title -->\n\t\t<CENTER>\n\t\t\t<H2><FONT FACE=\"%s\" COLOR=\"%s\">%s</FONT></H2>\n\t\t</CENTER>\n\t</DIV>\n", $config['fonts'], $config['headerColor'], $lang['header']);

	return true;
}

?>
