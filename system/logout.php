<?php

// Unset the cookie, and redirect to the login page.

setcookie( 'AMPLoginCredentials', 'logout' );
header( 'Location: index.php' );

?>
