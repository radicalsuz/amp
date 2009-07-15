#!/usr/bin/php
<?php

define('DIA_API_DEBUG', true);
define('DIA_API_USERNAME', 'test');
define('DIA_API_PASSWORD', 'test');
define('DIA_API_ORGANIZATION_KEY', 962);
define('DIA_PEAR_DIR', '/Users/seth/development/amp-3.5/include');
require_once('../API.php');

if(2 == $argc && $lowest_group = $argv[1]) {

	$api =& DIA_API::create('HTTP_Request');
	$groups = $api->getGroupNamesAssoc();
	foreach($groups as $key => $group) {
		if($key >= $lowest_group) {
			$delete[] = $key;
		}
	}
	$api->delete('groups', array('key' => $delete));
}

?>
