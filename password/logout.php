<?PHP
if (phpversion() >= 4) {
	// phpversion = 4
	session_start();
	session_unregister("login");
	session_unregister("password");
	session_destroy();
	$sessionPath = session_get_cookie_params(); 
	setcookie(session_name(), "", 0, $sessionPath["path"], $sessionPath["domain"]); 
} else {
	// phpversion = 3
	session_destroy_php3();
   setcookie($cookieName, "", 0);
}
?>