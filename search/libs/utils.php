<?php

/*
** DGS Search
** utils.php written by James Sella and William Sella
** Copyright (c) 2000-2001 Digital Genesis Software, LLC. All Rights Reserved.
** Released under the GPL Version 2 License.
** http://www.digitalgenesis.com
*/

function findext($rootDir, $fileTypes) {
	global $config;

	$debug = $config['debug'];
	$warn = $config['warn'];
	$followLinks = $config['followLinks'];
	$cacheFile = $config['cacheFile'];
	$cacheTTL = $config['cacheTTL'];
	$thisDir = $config['thisDir'];
	$parentDir = $config['parentDir'];
	$fsExcludes = $config['fsExclude'];
	$extSeparator = $config['extSeparator'];
	$fileSeparator = $config['fileSeparator'];
	$tm = getTime();

	if ($debug)
		printf("-->Debug: findext()<BR>\n");

	if ($cacheTTL > 0 && filemtime($cacheFile) + $cacheTTL > (int) $tm) {
		if ($debug)
			printf("-->Debug: findext() - Filesystem cache file being used. Expires in %d seconds.<BR>\n", filemtime($cacheFile) + $cacheTTL - (int) $tm);
		$matches = readCacheFile();
		if (count($matches) > 0) {
			if ($debug)
				printf("-->Debug: findext() - Filesystem cache containted %d matches.<BR>\n", count($matches));
			return $matches;
		}
	}

	$fileTypeCount = is_array($fileTypes) ? count($fileTypes) : 0;
	$excludeCount = count($fsExcludes);
	$rootDir = ereg_replace('(^.*)[/\\]$', '\\1', $rootDir); //Make sure there is no trailing slash.
	$matchCount = 0;
	$sp = 0;

	if ($warn)
		$handle = opendir($rootDir);
	else
		$handle = @opendir($rootDir);

	if (!$handle) { //We don't have access to rootDir or file error.
		if ($debug)
			printf("-->Debug: findext() - Error: Unable to open root directory '%s'.<BR>\nVerify \$config['rootDir'] is correct and readable by your web server.<BR>\n", $rootDir);
		return;
	}

	$dirStack[$sp] = $handle;
	$currentDir = $rootDir . $fileSeparator;
	chdir($currentDir);

	reset($fileTypes);
	while (list($i, $fileType) = each($fileTypes)) {
		$fileType = ereg_replace('^\^?(.*)$', '^\\' . $extSeparator . '\\1$', trim($fileType));
		$fileTypes[$i] = strtolower(str_replace('$$', '$', $fileType));
	}

	$running = true;
	while ($running) {
		if ($file = readdir($dirStack[$sp])) {
			if (!$followLinks && is_link($file))
				continue;

			if (!is_dir($file)) {
				$fullFile = $currentDir . $file;

				if ($debug)
					printf("-->Debug: findext() - File Queued: %s", $fullFile);

				if ($fileTypeCount > 0) {
					$isMatch = false;
					$ext = strtolower(strrchr($file, $extSeparator));
					reset($fileTypes);
					while (list(, $fileType) = each($fileTypes)) {
						if (ereg($fileType, $ext)) { 
							$isMatch = true;
							break;
						}
					}
					if (!$isMatch) {
						if ($debug)
							printf(" <B>[Miss Ext]</B><BR>\n");
						continue;
					}
				}

				reset($fsExcludes);
				while(list(, $fsExclude) = each($fsExcludes)) {
					if (ereg('^[/\\].*$', $fsExclude))
						$cmpName = $fullFile;
					else
						$cmpName = $file;
					if (ereg($fsExclude, $cmpName)) {
						$isMatch = false;
						break;
					}
				}

				if (!$isMatch) {
					if ($debug)
						printf(" <B>[Excluded: %s]</B><BR>\n", $fsExclude);
					continue;
				}

				if ($warn) //Make sure we can access this file.
					$handle = fopen($fullFile, 'r');
				else
					$handle = @fopen($fullFile, 'r');

				if (!$handle) {
					if ($debug)
						printf(" <B>[Read Failed]</B><BR>\n");
					continue;
				}
				fclose($handle);

				if ($debug)
					printf(" <B>[Match: %s]</B><BR>\n", $fileType);

				$matches[$matchCount++] = $fullFile;
			} else {
				if ($file == $thisDir || $file == $parentDir)
					continue;

				$queued = $currentDir . $file . $fileSeparator;

				if ($debug)
					printf("-->Debug: findext() - Dir Queued: %s", $queued);

				$isMatch = true;
				reset($fsExcludes);
				while(list(, $fsExclude) = each($fsExcludes)) {
					if (ereg('^[/\\].*$', $fsExclude))
						$cmpName = $queued;
					else
						$cmpName = basename($queued);
					if (ereg($fsExclude, $cmpName)) {
						$isMatch = false;
						if ($debug)
							printf(" <B>[Excluded: %s]</B><BR>\n", $fsExclude);
						break;
					}
				}

				if ($debug)
					printf("<BR>\n");

				if (!$isMatch)
					continue;

				if ($warn)
					$handle = opendir($file);
				else
					$handle = @opendir($file);

				if (!$handle) //Make sure we can access this directory.
					continue;

				$dirStack[++$sp] = $handle;
				chdir($file);

				if ($debug)
					printf("<BR>\n-->Debug: findext() - ==> %s<BR>\n", $queued);

				$currentDir = $queued;
			}
		} else {
			closedir($dirStack[$sp--]);

			if ($sp < 0) {
				$running = false;
			} else {
				if ($debug)
					printf("-->Debug: findext() - <== %s<BR>\n", $currentDir);
				$currentDir = dirname($currentDir) . $fileSeparator;
				chdir($currentDir);
			}
		}
	}
	
	if ($debug) //Time taken to locate all files.
		printf("-->Debug: findext() - Search Time: %.1f sec<BR><BR>\n", getTime() - $tm);

	if ($cacheTTL > 0) //If we are using cache, write it. We wouldn't be this far if it wasn't expired or missing.
		writeCacheFile($matches);

	return $matches;
}

function writeCacheFile($files) {
	global $config;
	$cacheFile = $config['cacheFile'];

	if ($config['debug'])
		printf("--->Debug: writeCacheFile()<BR>\n");

	$handle = fopen($cacheFile, 'w');

	if (function_exists('set_file_buffer'))
		set_file_buffer($handle, 65535);

	reset($files);
	$buffer = implode(";", $files);
	fwrite($handle, $buffer);
	fclose($handle);
}

function readCacheFile() {
	global $config;
	$installBase = $config['installBase'];
	$cacheFile = $config['cacheFile'];
	$configFile = $installBase . $config['fileSeparator'] . 'config' . $config['fileSeparator'] . 'config.php';
	$debug = $config['debug'];

	if ($debug)
		printf("--->Debug: readCacheFile()<BR>\n");

	$handle = @fopen($cacheFile, 'r');
	if (!$handle) {
		if ($debug)
			printf("--->Debug: readCacheFile() - Cache file '%s' not found. Generating a new cache file.<BR>\n");
		$files = array();
		return $files;
	}

	if (filemtime($configFile) > filemtime($cacheFile)) {
		if ($debug)
			printf("--->Debug: readCacheFile() - Expiring cache file '%s' due to configuration change.<BR>\n", $cacheFile, $configFile);
		$files = array();
		return $files;
	}

	if (function_exists('set_file_buffer'))
		set_file_buffer($handle, 65535); /* Set a 64k buffer. */

	$buffer = fread($handle, filesize($cacheFile));
	fclose($handle);

	$files = explode(';', $buffer);

	/* Verify this cache files contents are usable. */
	list(, $file) = each($files);
	if (!is_int(strpos($file, $config["fsBase"]))) {
		unset($files);
		$files = array();
		return $files;
	}

	return $files;
}

function getTitle($file) {
	global $config;

	$title = getHtmlTitle($file);
	if ($title)
		return $title;
	$endPath = explode($config['fsBase'], $file);
	$endPath = str_replace('\\', '/', $endPath[1]);

  	return ($config['urlBase'] . $endPath);
}

function getHtmlTitle($file) {
	global $config;

	$warn = $config['warn'];

	if ($warn) {
		$buffer = file($file); //Without @ will show SAFE MODE warnings.
	} else {
		$buffer = @file($file); //Hide warnings.
	}

	if (!is_array($buffer))
		return;

	$buffer = implode(' ', $buffer);
	$lowBuffer = strtolower($buffer);

	/* Locate where <TITLE> is located in html file. */
	$lBound = strpos($lowBuffer, '<title>') + 7; //7 is the lengh of <TITLE>.

	if ($lBound < 1)
		return;

	/* Locate where </TITLE> is located in html file. */
	$uBound = strpos($lowBuffer, '</title>', $lBound);

	if ($uBound < $lBound)
		return;

	/* Clean HTML and PHP tags out of $title with the madness below. */
	$title = ereg_replace("[\t\n\r]", '', substr($buffer, $lBound, $uBound - $lBound));
	$title = trim(strip_tags($title));

	if (strlen($title) < 1) //A blank title is worthless.
		return;

	return $title;
}

function getMetaDesc(&$content) {
	global $config;

	$lowBuffer = strtolower($content);

	/* Locate where <META is located in html file. */
	$lBound = strpos($lowBuffer, '<meta');

	if ($lBound < 1)
		return false;

	/* Locate where </HEAD is located in html file. */
	$uBound = strpos($lowBuffer, '</head', $lBound);

	if ($uBound < $lBound)
		return false;

	/* Clean HTML and PHP tags out of $desc with the madness below. */
	$desc = ereg_replace("[\t\r\n]", '', substr($content, $lBound, $uBound - $lBound));
	$desc = eregi_replace('^.*<META[[:space:]]+NAME[[:space:]]*=[[:space:]]*\"?description\"?[[:space:]]+CONTENT[[:space:]]*=[[:space:]]*\"?([^\">]*).*$', '\\1', $desc);
	$desc = trim(strip_tags($desc));

	if (strlen($desc) < 1) //A blank desc is worthless.
		return false;

	return $desc;
}

function strimatch($string1, $string2) {
	for($i = 0; $i < strlen($string1) && $i < strlen($string2); $i++) {
		if (strtolower($string1[$i]) != strtolower($string2[$i])) {
			break;
		}
	}

	return substr($string1, 0, $i);
}

function strmatch($string1, $string2) {
	for($i = 0; $i < strlen($string1) && $i < strlen($string2); $i++) {
		if ($string1[$i] != $string2[$i]) {
			break;
		}
	}

	return substr($string1, 0, $i);
}

function getFormData() {
	global $HTTP_GET_VARS;
	global $HTTP_POST_VARS;

	$requestType = getEnvVar('REQUEST_METHOD');

	switch (strtolower($requestType)) {
		case 'get':
			$data = $HTTP_GET_VARS;
			break;
		case 'post':
			$data = $HTTP_POST_VARS;
			break;
		default:
			settype($data, 'array');
	}

	if (!$data && function_exists('getSpecificFormData'))
		$data = getSpecificFormData();

	return $data;
}

function getSpecificFormData() {
	/* The content of this function is specific to DGS Search. */
	global $r, $o, $s, $q, $debug, $option;

	$data['r'] = $r;
	$data['o'] = $o;
	$data['s'] = $s;
	$data['q'] = $q;
	$data['debug'] = $debug;
	$data['option'] = $option;

	return $data;
}
 
function getEnvVar($envVar) {
	global $config;
	global $HTTP_ENV_VARS;
	global $HTTP_SERVER_VARS;
 
	if ($config['debug'] > 1) {
		printf("<BR>->Debug: getEnvVar() - getenv(\"%s\"): %s<BR>\n", $envVar, getenv($envVar));
		printf("->Debug: getEnvVar() - \$GLOBALS[\"%s\"]: %s<BR>\n", $envVar, $GLOBALS[$envVar]);
		printf("->Debug: getEnvVar() - \$HTTP_ENV_VAR[\"%s\"]: %s<BR>\n", $envVar, $HTTP_ENV_VARS[$envVar]);
		printf("->Debug: getEnvVar() - \$HTTP_SERVER_VAR[\"%s\"]: %s<BR>\n", $envVar, $HTTP_SERVER_VARS[$envVar]);
	}
 
	$retVal = $GLOBALS[$envVar];
	if (strlen($retVal) < 1) {
		$retVal = getenv($envVar);
		if (strlen($retVal) < 1) {
			$retVal = $HTTP_ENV_VARS[$envVar];
			if (strlen($retVal) < 1) {
				$retVal = $HTTP_SERVER_VARS[$envVar];
			}
		}
	}
 
	return $retVal;
} 

function getTime() {
	if (function_exists('microtime')) {
		$tm = microtime();
		$tm = explode(' ', $tm);
		return (float) sprintf('%f', $tm[1] + $tm[0]);
	}

	return time();
}

function processConfig() {
	global $config;

	if ($config['debug'])
		printf(">Debug: processConfig()<BR>\n");

	/* Put the config into a known state. */
	normalizeConfig();	

	/* Load Language */
	$errors = loadLanguage();

	/* Verify config */
	if (!$errors)
		$errors = verifyConfig();

	/* Handle Errors */
	if ($errors) {
		printf("<B>Error: Configuration error(s) in config.php. Details below:<BR>\n<BR>\n");
		reset($errors);
		while (list(, $error) = each($errors)) {
			printf("&nbsp;&nbsp;%s<BR>\n", $error);
		}
		printf('</B><BR>\n');
		return false;
	}

	return true;
}

function loadLanguage() {
	global $config;
	global $lang;

	$debug = $config['debug'];

	$langInclude = $config['installBase'] . $config['fileSeparator'] . 'libs' . $config['fileSeparator'] . 'language' . $config['fileSeparator'] . $config['language'] . '.php';
	if ($debug)
		printf('->Debug: loadLanguage() - Checking language pack \'%s\' stored in \'%s\'. ', $config['language'], $langInclude);

	/* Verify we can read the language pack. */
	if (is_readable($langInclude)) {
		if ($debug)
			printf(" <B>[Readable]</B><BR>\n");
		include($langInclude);
	} else {
		if ($debug)
			printf(" <B>[Not Readable]</B><BR>\n");
		return array(sprintf("Unable to access language '%s' (%s).<BR>\n", $config['language'], $langInclude));
	}

	return false;
}

function normalizeConfig() {
	global $config;

	if ($config["debug"])
		printf("->Debug: normalizeConfig()<BR>\n");

	/* Change values as needed according to our rules. */
	$config['language'] = strtolower(trim($config['language']));
	$config['warn'] = ($config['debug']) ? true : $config['warn'];
	$config['verifyConfig'] = ($config['debug']) ? true : $config['verifyConfig'];
	$config['followLinks'] = (function_exists('is_link')) ? $config['followLinks'] : false;
	$config['maxSearchTime'] = ($config['maxSearchTime'] > 0) ? $config['maxSearchTime'] : 65535;

	/* Clean up trailing slashed. */
	$config['installBase'] = ereg_replace('(^.*)[/\\]$', '\\1', $config['installBase']);
	$config['urlBase'] = ereg_replace('(^.*)[/\\]$', '\\1', $config['urlBase']);
	$config['siteBase'] = ereg_replace('(^.*)[/\\]$', '\\1', $config['siteBase']);
	$config['fsBase'] = ereg_replace('(^.*)[/\\]$', '\\1', $config['fsBase']);

	return true;
}

function verifyConfig() {
	global $config;

	$installBase = $config['installBase'];
	$siteBase = $config['siteBase'];
	$fsBase = $config['fsBase'];
	$verify = $config['verifyConfig'];

	if ($config['debug'])
		printf("->Debug: verifyConfig()<BR>\n");

	/* These checks are optional. */
	if ($verify) {
		$error = verifyPHPVersion(3,0,7);
		if (is_string($error))
			$errors[] = $error;
		if (!is_array($config['searchModules']))
			$errors[] = '\$config[\'searchModules\'] is not set to an array type.';
		if (!is_array($config['displayModules']))
			$errors[] = '\$config[\'displayModules\'] is not set to an array type.';
		if (!is_string($config['urlBase']))
			$errors[] = '\$config[\'urlBase\'] is not set to a string type.';
		if (!is_string($installBase))
			$errors[] = '\$config[\'installBase\'] is not set to a string type.';
		else if (!is_dir($installBase) || (is_link($installBase) && !is_dir(readlink($installBase))))
			$errors[] = '\$config[\'installBase\'] is not set to a valid directory.';
		if (!is_string($siteBase))
			$errors[] = '\$config[\'siteBase\'] is not set to a string type.';
		else if (!is_dir($siteBase) || (is_link($siteBase) && !is_dir(readlink($siteBase))))
			$errors[] = '\$config[\'siteBase\'] is not a set to a valid directory.';
		if (!is_string($fsBase))
			$errors[] = '\$config[\'fsBase\'] is not set to a string type.';
		else if (!is_dir($fsBase) || (is_link($fsBase) && !is_dir(readlink($fsBase))))
			$errors[] = '\$config[\'fsBase\'] is not set to a valid directory.';
		if (!is_array($config['exts']))
			$errors[] = '\$config[\'exts\'] is not set to an array type.';
		if (strlen($siteBase) > 0) {
			$index = strpos($fsBase, $siteBase);
			if (!is_long($index) || $index != 0)
				$errors[] = '\$config[\'fsBase\'] must be a directory within \$config[\'siteBase\'] directory.';
			$index = strpos($installBase, $siteBase);
			if (!is_long($index) || $index != 0)
				$errors[] = '\$config[\'installBase\'] must be a directory within \$config[\'siteBase\'] directory.';
		}
	}

	/* Load modules is always required. */
	$modules['search'] = $config['searchModules'];
	$modules['display'] = $config['displayModules'];

	/* Load and Verify modules is required. */
	$error = verifyModules($modules);
	if (is_string($error))
		$errors[] = $error;

	if (!isset($errors))
		return false;
	return $errors;
}

function verifyModules($modules) {
	global $config;

	$installBase = $config['installBase'];
	$debug = $config['debug'];

	if ($config['debug'])
		printf("-->Debug: verifyModules()<BR>\n");

	reset($modules);
	while (list($modType, $x) = each($modules)) {
		reset($modules[$modType]);
		while (list($x, $module) = each($modules[$modType])) {
			$modInclude = $installBase . $config['fileSeparator'] . 'libs' . $config['fileSeparator'] . $modType . $config['fileSeparator'] . $module . '.php';
			/* Verify function isn't already available. */
			if (!function_exists($module)) {
				if ($debug)
					printf('-->Debug: verifyModules() - Checking module \'%s\' stored in \'%s\'. ', $module, $modInclude);
				/* Verify we can read the module. */
				if (is_readable($modInclude)) {
					if ($debug)
						printf(" <B>[Readable]</B><BR>\n");
					include($modInclude);
				} else {
					if ($debug)
						printf(" <B>[Not Readable]</B><BR>\n");
					return sprintf("Unable to access %s module '%s' (%s).<BR>\n", $modType, $module, $modInclude);
				}
				/* Verify that the module contains a function of the same name. */
				if (!function_exists($module)) {
					return sprintf("Error: Module '%s' (%s) is not usable.<BR>\nThis module must contain the function '%s(\$retVal, \$value, $param)'.<BR>\n", $module, $modInclude, $module);
				} else {
					$loadedModules[$module] = true;
				}
			} else if ($loadedModules[$module] != true) {
				return sprintf("Error: The function '%s' was available before loading module '%s'.<BR>\n'%s' may be a built in PHP function or another module may have the same name. If this is the case, you will need to rename the module.<BR>\n", $module, $modInclude, $module);
			}
		}
	}

	return true;
}

function verifyPHPVersion($major, $minor, $sub) {
	global $config;

	if ($config["debug"])
		printf("->Debug: verifyPHPVersion() - Running PHP '%s' (Required version is %d.%d.%d)<BR>\n", phpversion(), $major, $minor, $sub);

	$phpVersion = explode(".", phpversion());
	/* Convert any non-integer values to -1, such as 4.0b3 */
	$phpVersion[0] = (strlen($phpVersion[0]) > 0) ? intval($phpVersion[0]) : -1;
	$phpVersion[1] = (strlen($phpVersion[1]) > 0) ? intval($phpVersion[1]) : -1;
	$phpVersion[2] = (strlen($phpVersion[2]) > 0) ? intval($phpVersion[2]) : -1;

	if ($phpVersion[0] < $major || ($phpVersion[0] == $major && $phpVersion[1] < $minor) || ($phpVersion[0] == $major && $phpVersion[1] == $minor && $phpVersion[2] < $sub)) {
		return sprintf('%s %s requires at least PHP v%d.%d.%d. You are currently running PHP %s.', $config['program'], $config['version'], $major, $minor, $sub, phpversion());
	}

	return true;
}

function processWindow(&$content, $width, $height, $q, $matchCount) {
	global $config;

	$lowQ = strtolower($q);
	$qSize = strlen($q);
	$window = $width / 2;
	$descEnd = $config['descEnd'];
	$boldQuery = $config['boldQuery'];
	$stripTags = $config['stripTags'];
	$desc = array();

	$content = str_replace('<br', ' <br', $content);
	$content = str_replace('<Br', ' <Br', $content);
	$content = str_replace('<BR', ' <BR', $content);
	$content = str_replace('<p', ' <p', $content);
	$content = str_replace('<P', ' <P', $content);
	$content = str_replace('&nbsp;', ' ', $content); //'&nbsp' parts show up if they are broken, so convert to spaces.

	if ($stripTags)
		$content = strip_tags($content);

	$content = ereg_replace('[[:space:]]{2,}', ' ', $content); //Consume extra whitespace from content.
	$content = trim($content);

	$lowContent = strtolower($content);
	$contentSize = strlen($content);

	$queryIdx = 0;
	for ($i = 0; $i < $height; $i++) {
		$queryIdx = strpos($lowContent, $lowQ, $queryIdx);
		if (!is_int($queryIdx) || $queryIdx >= $contentSize) //If we don't find $q, then there are no more matches.
			break;

		/* Grab the section of text around the matching keyword. */
		$lBound = ($queryIdx - $window < 0) ? 0 : $queryIdx - $window;
		$uBound = ($lBound + $width > $contentSize) ? $contentSize : $lBound + $width;
		$lBound = ($uBound < $contentSize) ? $lBound : (($uBound - $width < 1) ? 0 : $uBound - $width);

		/* Slide our window to avoid cutting words. */
		$descAdj = $uBound - ($queryIdx + $qSize);
		for ($j = 0; $j < $descAdj; $j++) {
			if ($lBound - $j <= 0 || $content[$lBound - $j - 1] == ' ') {
				$lBound -= $j;
				$uBound -= $j;
				break;
			}
		}

		/* Shrink the uBound to avoid cutting words. */
		$descAdj = $uBound - ($queryIdx + $qSize);
		if ($uBound < $contentSize && $content[$uBound] != ' ') {
			for ($j = 1; $j < $descAdj; $j++) {
				if ($content[$uBound - $j] == ' ' && $uBound - $j > $lBound) {
					$uBound -= $j;
					break;
				}
			}
		}

		/* Cut the desc out of content and add descEnd. */
		$descBuf = '';
		if ($lBound > 0)
			$descBuf = $descEnd;
		$descBuf .= trim(substr($content, $lBound, $uBound - $lBound));
		if ($uBound < $contentSize)
			$descBuf .= $descEnd;

		if (!$boldQuery)
			$desc[$i] = $descBuf;
		else
			$desc[$i] = eregi_replace("($q)","<B>\\1</B>", $descBuf);

		$queryIdx = $uBound; //Jump the queryIdx to the end of the desc.
	}
	
	return $desc;
}

function remoteDebug($password, $option) {
	global $config;
		
	if (!$config['remoteDebug'] || (is_string($config['remoteDebug']) && strcmp($config['remoteDebug'], $password))) {
		printf("<!-- Remote Debug is not available. -->\n");
		flush();
		return true;
	}

	switch (strtolower($option)) {
		case 'all':
			break;
		case 'phpinfo':
			phpInfo();
			break;
		case 'config':
			$content = file('config/config.php');
			$content = implode(' ', $content);

			/* Cleanup a bit - Generic */
			$content = ereg_replace("/\*[^\*]+\*/", "", $content);

			/* Hide usernames and passwords. */
			$content = eregi_replace("(config\['remoteDebug'\])[^;]+", "\\1 = [Hidden]", $content);
			$content = eregi_replace("(database\[[0-9]+\]\[['\"]username['\"]\])[^;]+", "\\1 = [Hidden]", $content);
			$content = eregi_replace("(database\[[0-9]+\]\[['\"]password['\"]\])[^;]+", "\\1 = [Hidden]", $content);

			/* Final touches */
			if (function_exists('highlight_string')) {
				$func = 'highlight_string'; /* I know this looks stupid. */
				$content = $func($content);
			} else {
				/* Cleanup a bit - Specific */
				$content = ereg_replace("[\n\r]", "<BR>\n", $content);
				$content = ereg_replace("[[:space:]]*<BR>\n", "<BR>\n", $content);
				$content = ereg_replace("(<BR>\n){2,}", "<BR>\n<BR>\n", $content);
				$content = str_replace('<?', '<B>', $content);
				$content = str_replace('?>', '</B>', $content);
			}

			/* Display config.php */
			printf("%s", $content);
			break;
		case 'ash':
			srand ((double) microtime() * 1000000);
			switch (rand() % 5) {
				case 0:
					printf("Its a trick, get an axe.<BR>\n");
					break;
				case 1:
					printf("Lady, I'm afraid I'm going to have to ask you to leave the store.<BR>\n");
					break;
				case 2:
					printf("Good. Bad. I'm the guy with the gun.<BR>\n");
					break;
				case 3:
					printf("Come get some.<BR>\n");
					break;
				case 4:
					printf("Swallow this.<BR>\n");
					break;
			}
			printf("<P>\n-Bruce 'Ash' Campbell<BR>\n");
			break;
		default:
			$config['debug'] = true;
			return true;
	}

	return false;
}

?>
