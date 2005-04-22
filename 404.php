<?php    

/*************** Redirection Page
Displayed when a user queries an AMP website
and no resulting page is returned from the database.
Searches redirect table for matching pages, then sends 
the user to search page if no matches are found.*/

include_once("AMP/BaseDB.php");

// Check for a custom handler.
$uri = $_SERVER['REQUEST_URI'];
$pos = strpos( $uri, '?' );
$PHP_SELF = $_SERVER['PHP_SELF'] = substr( $uri, 1, ($pos) ? $pos - 1 : strlen( $uri ) - 1 );

if (isset($_SERVER['REDIRECT_QUERY_STRING'])) {
    parse_str( $_SERVER['REDIRECT_QUERY_STRING'], $_GET );
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

    // We haven't found what we're looking for, so flag a 404 error, and try to
    // redirect.
    header( 'Status: ' . $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found' );

    // Search for something to redirect to, based on our redirect table.
    $myURI = $dbcon->qstr(substr($_SERVER['REQUEST_URI'], 1));
    $R=$dbcon->Execute("SELECT * FROM redirect WHERE publish=1 AND old=$myURI OR conditional=1")
        or die('404 query error: '.$dbcon->ErrorMsg());

    $redirected = false;
    
    // It feels like this loop is totally unnecessary and could be handled in
    // the SQL, but we'll loop through it anyhow since I don't know what the
    // intent of this is...
    while (!$R->EOF && !$go) {

        $old_uri = $R->Fields("old");
        $new_uri = $R->Fields("new");
        $num     = ($R->Fields("conditional")) ? $R->Fields("num") : null;

        $redirected = error_redirect($old_uri, $new_uri, $num);

        $R->MoveNext();
    }

    // If we still ... haven't found ... what we're looking for, then scale
    // some city walls. Bizarre, twisted, surrealist city walls. It appears as
    // though we're doing a starts-with search, which is a bit crazy, but
    // whatever.
    
    if (!$redirected) {

        $sql = "SELECT * FROM redirect WHERE publish=1 AND $myURI LIKE CONCAT(old, '%')";
        $R=$dbcon->Execute($sql) or die('404 query error: ' . $dbcon->ErrorMsg());
    
        while (!$R->EOF && !$redirected) {

            $old_uri = $R->Fields("old");
            $new_uri = $R->Fields("new");
            $num     = ($R->Fields("conditional")) ? $R->Fields("num") : null;

            $redirected = error_redirect($old_uri, $new_uri, $num);

            $R->MoveNext();
        }
    }
    
    if (!$redirected) {

        // Redirect to the search page, since we still couldn't find whatever it was
        // that they were looking for. Even after kissing honey lips.
        ampredirect ($Web_url . "search.php");

    }
}

function error_redirect( $requested_uri, $target_uri, $num = null ) {

    global $Web_url;

    // This is very confusing, and I'm not entirely clear on what it's supposed
    // to do or accomplish. Oh well...
    $fetch_str = strstr($_SERVER['REQUEST_URI'], $requested_uri);
    $fetch_val = ( $num ) ? substr( $fetch_str, $num ) : "";

    // Check to see if our REQUEST_URI matches in any way the $requested_uri
    if ($fetch_str) {

        // Add the appropriate junk if $target isn't a real URI.
        if (substr($target, 0, 4) == "http") {
            $redirect_uri = $target_uri . $fetch_val;
        } else {
            $redirect_uri = $Web_url . $target_uri . $fetch_val;
        }

        // Everything worked out OK. Redirect and report success.
        ampredirect( $redirect_uri );
        return true;
    }

    // We didn't redirect, report failure.
    return false;
}

?>
