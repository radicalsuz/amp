<?php

// Unset the cookie, and redirect to the login page.

setcookie( 'AMPLoginCredentials', 'logout' );
header( 'Location: index.php' );

/* Obsolete.

	session_start();
	session_unregister("login");
	session_unregister("password");
	session_destroy();
	$sessionPath = session_get_cookie_params(); 
	setcookie(session_name(), "", 0, $sessionPath["path"], $sessionPath["domain"]); 

header ("Location: index.php"); */

?>
