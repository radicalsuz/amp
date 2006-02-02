<?php

require_once('AMP/BaseDB.php');
$GLOBALS['admin_auth'] = &AMP_Registry::instance( );

$db_type = 'mysql';
$db_host = AMP_DB_HOST;
$db_name = AMP_DB_NAME;
$db_username = AMP_DB_USER;
$db_password = AMP_DB_PASS;
$db_prefix = 'punbb_';
$p_connect = false;

$cookie_name = 'punbb_cookie';
$cookie_domain = '';
$cookie_path = '/';
$cookie_secure = 0;
$cookie_seed = '744c5f12';

if(defined('AMP_LOCAL_PATH')) {
	define('PUN_CACHE_DIR', AMP_LOCAL_PATH.'/cache/punbb');
} else {
	define('PUN_CACHE_DIR', PUN_ROOT.'cache/');
}

define('PUN', 1);

?>
