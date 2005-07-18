<?php 

include_once("AMP/System/Base.php");

// Check for a custom handler.
$uri = $_SERVER['REQUEST_URI'];
$pos = strpos( $uri, '?' );
$PHP_SELF = $_SERVER['PHP_SELF'] = substr( $uri, 1, ($pos) ? $pos - 1 : strlen( $uri ) - 1 );

if (isset($_SERVER['REDIRECT_QUERY_STRING'])) {
    parse_str( $_SERVER['REDIRECT_QUERY_STRING'], $_GET );
    $_REQUEST = array_merge($_REQUEST, $_GET);
}

$customHandler = AMP_LOCAL_PATH . "/custom/" . $_SERVER['PHP_SELF'];

if (file_exists($customHandler)) { 

    ob_start();
    ob_start();
    include( $customHandler );
    $output = ob_get_clean();
    ob_end_clean();

    // Set response header to reflect the actual status of our request.
    //
    // if we made it this far, I'm going to assume that everything is just
    // fine. Custom scripts that want to redirect must exit() before reaching
    // here.
    header( 'Status: ' . $_SERVER['SERVER_PROTOCOL'] . ' 200 OK' );

    print $output;

} else {

        header( 'Status: ' . $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found' );

        include('header.php');

        ?>
        <p>Sorry, we couldn't find the page you requested.</p>

        <?php

        if (isset($_SERVER['HTTP_REFERER'])) {

        ?>    
        <p>You can <a href="<?= $_SERVER['HTTP_REFERER'] ?>">go back</a> to the previous page.</p>
        <?php

        }

        include('footer.php');
}

?>
