<?php

// Unset the cookie, and redirect to the home page.

setcookie( 'AMPContentLoginCredentials', 'logout' );
header( 'Location: index.php' );

?>
