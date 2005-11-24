#!/usr/bin/php
<?php

if(!defined('PEAR_INCLUDE_PATH')) {
	define('PEAR_INCLUDE_PATH', '../../');
}

$current_path = ini_get("include_path");
ini_set("include_path", "$current_path:".PEAR_INCLUDE_PATH);

if(2 == $argc && $table = $argv[1]) {

	define('DIA_API_USERNAME', 'demo');
	define('DIA_API_PASSWORD', 'demo');

	require_once('../API.php');
	require_once('XML/Unserializer.php');

	$api =& DIA_API::create('HTTP_Request');
	$xml = $api->describe($table);

	$xmlparser =& new XML_Unserializer();
	$status = $xmlparser->unserialize($xml);
	$desc = $xmlparser->getUnserializedData();

	
$file =
"<?php

require_once('DIA/Object.php');

class DIA_".ucfirst($table)." extends DIA_Object {

    var \$_table = '$table';

";

foreach($desc[$table]['Field'] as $field) {
	$function = join('', array_map('ucfirst', split('_', strtolower($field))));
	$parameter = '$'.join('_', split('_', strtolower($field)));

	$file .=
"	function get".$function."() {
		return \$this->getProperty('".$field."');
	}

	function set".$function."($parameter) {
		return \$this->setProperty('$field', $parameter);
	}

";
}

$file .=
"}

?>";

$handle = fopen(ucfirst($table).'.php', 'w');

fwrite($handle, $file);

fclose($handle);

}
?>
