<?php

# global php defines:
# define('LOG_ERR',     3);
# define('LOG_WARNING', 4);
# define('LOG_NOTICE',  5);
# define('LOG_INFO',    6);
# define('LOG_DEBUG',   7);

/**
 *
 * gum log
 *
 **/

$log_stdout  = null;
$log_level   = 3;
$log_echo    = false;
$log_file    = '';
$log_enabled = true;

function glog($str, $mode='7')
{
	global $log_file, $log_enabled, $log_stdout, $log_echo, $log_level;
	
	if (!$log_file || !$log_enabled || $mode > $log_level)
		return;
		
	if ($log_echo && !$log_stdout)
		$log_stdout = fopen('/dev/stdout', 'w');
		
	$str = strip_tags($str)."\n";
	if (strlen($str) > 75) {
		$lines = explode("\n", str_replace("\n","\n  ",wordwrap($str)));
	}
	else {
		$lines = array($str);	
	}
	
	foreach ($lines as $line) {
		error_log($line, 3, $log_file);
		if ($log_echo && $log_stdout) {
			fwrite($log_stdout, $line);
			fflush($log_stdout);
		}
	}
}

function glog_set_echo($value) {
	global $log_echo;
	$log_echo = $value;
}

function glog_set_level($value) {
	global $log_level;
	$log_level = $value;
}

function glog_set_file($value) {
	global $log_file;
	$log_file = $value;
}
	
?>
