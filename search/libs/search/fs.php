<?php

/*
** DGS Search
** fs.php written by James M. Sella
** Copyright (c) 2000-2001 Digital Genesis Software, LLC. All Rights Reserved.
** Released under the GPL Version 2 License.
** http://www.digitalgenesis.com
*/

function fs($retVal, $q, $r, $o, $s, $timer) {
	global $config;

	$lowQ = strtolower($q);
	$qSize = strlen($q);
	$metaDesc = $config['metaDesc'];
	$descWidth = ($config['descWidth'] > 0) ? $config['descWidth'] : $qSize;
	$descWindow = $descWidth / 2;
	$descHeight = $config['descHeight'];
	$maxFileSize = $config['maxFileSize'];
	$maxSearchTime = $config['maxSearchTime'];
	$urlBase = $config['urlBase'];
	$fsBase = $config['fsBase'];
	$siteBase = $config['siteBase'];
	$frameSet = $config['frameSet'];
	$dateFormat = $config['dateFormat'];
	$docExts = $config['docExts'];
	$extSeparator = $config['extSeparator'];
	$boldQuery = $config['boldQuery'];
	$descEnd = $config['descEnd'];
	$debug = $config['debug'];
	$warn = $config['warn'];
	$matchCount = count($retVal);
	$tm = getTime();

	if ($debug)
		printf("->Debug: fs() [Module]<BR>\n");

	if ($s > 0 && $matchCount >= $r + $o) //If we know the size already and have enough matches, return.
		return $retVal;

	$files = findext($fsBase, $config['exts']);

	/* Locate Matching Files and Inject into retVal. */
	/* NOTE: This loop a large portion of the programs execution time. More optimization is needed. */
	$lastDoc = '';
	reset($files);
	while(list(, $file) = each($files)) {
		if (getTime() - $timer > $maxSearchTime) {
			if ($debug)
				printf("->Debug: fs() - <B>Exceeded Max Search Time of %.1f secs. Currently %.1f secs. - Exiting fs module early.</B><BR>\n", $maxSearchTime, getTime() - $timer);
			break;
		}

		if ($debug)
			printf('->Debug: fs() - Searching: %s', $file);

		if (!is_readable($file) || filesize($file) > $maxFileSize) {
			if ($debug)
				printf(" <B>[Skipping (%dk > %dk)]</B><BR>\n", $file, filesize($file) / 1024, $maxFileSize / 1024);
			continue;
		}

		if ($warn) {
			$content = file($file); //Without @ will show SAFE MODE warnings.
		} else {
			$content = @file($file); //Hide warnings.
		}

		if (!is_array($content)) {
			if ($debug)
				printf(" <B>[Skipping (Failed Read)]</B><BR>\n", $file);
			continue;
		}

		$content = implode(' ', $content);

		if (stristr(strip_tags($content), $lowQ) == false) { /* Scan for matches. */
			if ($debug)
				printf("<BR>\n");
			continue;
		} else if ($debug) {
			printf(" <B>[Match]</B><BR>\n", $file);
		}

		$matchCount++;

		if ($matchCount >= $o && $matchCount <= $r + $o) { //We only need the desc for those we will display.
			if ($metaDesc) {
				/* The windowing needs more work. This is hack. */
				$buffer = getMetaDesc($content);
				if (strlen($buffer) > 0) {
					$desc[0] = substr($buffer, 0, $descWidth);
					if ($boldQuery)
						$desc[0] = boldQuery($desc[0], $lowQ);
					if (strlen($content) > $descWidth) {
						$desc[0] .= $descEnd;
					}
				}
			}
			if (!isset($desc)) {
				$desc = processWindow($content, $descWidth, $descHeight, $q, $matchCount);
			}
		}

		/* Start - Frame Set Patch */
		if ($frameSet) {
				/* Check xy-1.html -> xy.html scheme. */
				$frameFile = eregi_replace('-[0-9]+(\.s?html?)$', '\\1', $file);
				if (($frameFile != $file) && file_exists($frameFile)) {
						if ($debug)
								printf("->Debug: fs() - Found: %s for %s [Frame Set Scheme 1]<BR>\n", $frameFile, $file);
						$file = $frameFile;
				}
				/* Check xy/1.html -> xy/xy.html scheme. */
				$frameFile = eregi_replace('([^\\/]+)([\\/])[0-9]+(\.s?html?)$', '\\1\\2\\1\\3', $file);
				if (($frameFile != $file) && file_exists($frameFile)) {
						if ($debug)
								printf("->Debug: fs() - Found: %s for %s [Frame Set Scheme 2]<BR>\n", $frameFile, $file);
						$file = $frameFile;
				}
				if ($file == $lastDoc) { /* Frame was shown before */
						continue;
				} else {
						$lastDoc = $file;
				}
		}
		/* End - Frame Set Patch */

		/* Start - Document Extension Patch */
		$title = getTitle($file);
		if (is_array($docExts)) {
			$fileExt = ereg_replace(sprintf('^.*\\%s([^\\%s]+$)', $extSeparator, $extSeparator), '\\1$', basename($file));
			reset($docExts);
			while (list(, $ext) = each($docExts)) {
				$finalFile = ereg_replace($fileExt, $ext, $file);
				if (file_exists($finalFile) && is_readable($finalFile)) {
					if ($debug)
						printf("->Debug: fs() - Found: %s for %s<BR>\n", basename($finalFile), $file);
					$title = ereg_replace($fileExt, $ext, $title);
					break;
				}
				$finalFile = $file;
			}
		} else {
			$finalFile = $file;
		}

		$endPath = explode($siteBase, $finalFile);
		$endPath = str_replace('\\', '/', $endPath[1]);
	       
		if (filemtime($finalFile) > 0) {
			if (!isset($desc)) {
				$desc = '';
			}
			$retVal[] = array('link' => $title, 'url' => $urlBase . $endPath, 'description' => $desc, 'fileSize' => filesize($finalFile), 'lastMod' => date($dateFormat, filemtime($finalFile)), 'source' => 'fs');
		} else {
			$retVal[] = array('link' => $title, 'url' => $urlBase . $endPath, 'description' => $desc, 'fileSize' => filesize($finalFile), 'source' => 'fs');
		}
		/* End - Document Extension Patch */

		if ($s > 0 && $matchCount > $r + $o) //If we know the size already and have enough matches, break.
			break;
		unset($desc); //Start with a clean slate.
	}

	if ($debug) { //Time taken to locate all files.
		printf("->Debug: fs() - Module Search Time: %.1f sec<BR>\n", getTime() - $tm);
		flush();
	}

	return $retVal;
}

?>
