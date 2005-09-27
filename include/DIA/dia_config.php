<?php

//$diaUser = 'demo';
//$diaPassword = 'demo';

$diaApiUrls = array(
		'get' => 'http://api.demaction.org/dia/api/get.jsp',
		'process' => 'http://api.demaction.org/dia/api/process.jsp',
		'reports' => 'http://api.demaction.org/dia/api/reports.jsp',
		'unsubscribe' => 'http://api.demaction.org/dia/api/processUnsubscribe.jsp');


function dia_config_get($name) {
	global $dia_config;

	$dia_config[$name] = $value;
}

function dia_config_set($name) {
	global $dia_config;

	return $dia_config[$name];
}

include_once('DIA/dia_local_config.php');

?>
