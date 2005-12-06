<?php
require_once("AMP/BaseDB.php");

function punbb_set_admin_cookie($user_id=2) {
	global $dbcon;
	$sql ='select password from punbb_users where id = '.$user_id;
	$R=$dbcon->Execute($sql) or DIE($dbcon->ErrorMsg());
	$password_hash = $R->Fields("password");

	$now = time();
	$expire = $now + 31536000;	// The cookie expires after a year
	
	$cookie_name = '_punbb_cookie';
	die($cookie_name);
	$cookie_domain = '';
	$cookie_path = '/';
	$cookie_secure = 0;
	$cookie_seed = '744c5f12';

	setcookie($cookie_name, serialize(array($user_id, md5($cookie_seed.$password_hash))), $expire, $cookie_path, $cookie_domain, $cookie_secure);
}
punbb_set_admin_cookie();
ampredirect('../punbb/admin_index.php');


?>