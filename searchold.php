<?php

/*
** DGS Search
** search.php written by James M. Sella
** Copyright (c) 2000-2001 Digital Genesis Software, LLC. All Rights Reserved.
** Released under the GPL Version 2 License.
** http://www.digitalgenesis.com
*/
 
 $mod_id = 40;
include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php"); 
require('search/config/config.php');
require('search/libs/utils.php');

function search($q, $r, $o, $s, $timer) {
	global $config;
	$modules = $config['searchModules'];
	$installBase = $config['installBase'];
	$retVal = array();

	if ($config['debug'])
		printf(">Debug: search()<BR>\n");

	reset($modules);
	while (list(, $module) = each($modules)) {
		$retVal = $module($retVal, $q, $r, $o, $s, $timer);
		if (is_string($retVal)) {
			printf("Error: Search module '%s' had a fatal error. Details below:<BR>\n<BR>\n%s<BR>\n", $module, $retVal);
			exit();
		}
	}

	return $retVal;
}

function display($retVal, $q, $r, $o, $s, $timer) {
	global $config;
	$modules = $config['displayModules'];
	$installBase = $config['installBase'];
	$fileSeparator = $config['fileSeparator'];
	$header = $config['header'];
	$footer = $config['footer'];

	if ($config['debug'])
		printf(">Debug: display()<BR>\n");

	/* Display header if set. */
	if ($header) {
		if ($header[0] != $fileSeparator && $header[0] != '.')
			$header = $installBase . $fileSeparator . 'config' . $fileSeparator . $header;
		if (is_readable($header)) {
			include($header);
		} else {
			printf("Error: Unable to access header '%s'.<BR>\n", $header);
		}
	}

	reset($modules);
	while (list(, $module) = each($modules)) {
		$error = $module($retVal, $q, $r, $o, $s, $timer);
		if (is_string($error)) {
			printf("Error: Display module '%s' had a fatal error. Details below:<BR>\n<BR>\n%s<BR>\n", $module, $error);
			break;
		}
	}

	if (!$config['hideCredits']) {
		printf("\t<DIV ALIGN=\"right\">\n\t\t<FONT FACE=\"%s\" SIZE=\"-1\"><A TARGET=\"_top\" HREF=\"http://www.digitalgenesis.com\">DGS Search %s</A></FONT>\n\t</DIV>\n", $fonts, $config["version"]);
	}

	/* Display footer if set. */
	if ($footer) {
		if ($footer[0] != $fileSeparator && $footer[0] != '.')
			$footer = $installBase . $fileSeparator . 'config' . $fileSeparator . $footer;
		if (is_readable($footer)) {
			include($footer);
		} else {
			printf("Error: Unable to access footer '%s'.<BR>\n", $footer);
		}
	}
}

/* Start of Main */

$data = getFormData();

if ($data['debug']) {
	if (!remoteDebug($debug, $option))
		return;
}

$timer = getTime();

if ($config['debug'])
	printf("Debug: main() - Calling processConfig()<BR>\n");

$status = processConfig();
if ($status) {
	/* Clean up and localize passed values. */
	$r = (!isset($data['r'])) ? (($config['results']) ? 10 : $config['results']) : $data['r']; //Set default for results per page.
	$r = ($r < 1) ? $config['maxResults'] : $r;
	$o = (!isset($data['o']) || $data['o'] < 1) ? 0 : $data['o']; //Set default for offset.
	$s = (!isset($data['s']) || $data['s'] < 1) ? 0 : $data['s']; //Set result set to 0 if we don't have a cached one.
	$q = (isset($data['q']) && $data['q'] != '') ? $data['q'] : '';

	/* Search for results. */
	if ($q) {
		if (function_exists('get_html_translation_table')) {
			if ($config['debug'])
				printf("Debug: main() - Calling get_html_translation_table()<BR>\n");
			$q = strtr($q, get_html_translation_table(HTML_ENTITIES));
		}
		if ($config['debug'])
			printf("Debug: main() - Calling search(%s, %d, %d, %d, %d)<BR>\n", $q, $r, $o, $s, $timer);
		$retVal = search($q, $r, $o, $s, $timer);
	}

	/* Display our results. */
	if ($config['debug'])
		printf("Debug: main() - Calling display(%s, %d, %d, %d, %d)<BR>\n", $q, $r, $o, $s, $timer);
	display($retVal, $q, $r, $o, $s, $timer);
}

if ($config['debug'])
	printf("Debug: main() - Total Time: %.1f<BR>\n", getTime() - $timer);


/* End of Main */

?>
<?php include ("AMP/BaseFooter.php");?>