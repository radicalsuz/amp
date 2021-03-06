<?php    

$uri = $_SERVER['REQUEST_URI'];
$query_start = strpos( $uri, '?' );
$_SERVER['PHP_SELF'] = ( $query_start ) ? substr( $uri, 1, $query_start -1 ) : substr( $uri, 1 );

if (isset($_SERVER['REDIRECT_QUERY_STRING'])) {
    parse_str( $_SERVER['REDIRECT_QUERY_STRING'], $_GET );
    $_REQUEST = array_merge($_REQUEST, $_GET);
}
require_once( 'AMP/BaseDB.php');
require_once( 'AMP/Content/Redirect/Handler.php' );
$handler = &new AMP_Content_Redirect_Handler( );

$handler->execute( );

/*************** Redirection Page
Displayed when a user queries an AMP website
and no resulting page is returned from the database.
Searches redirect table for matching pages, then sends 
the user to search page if no matches are found.*/

/* 
$no_search_extensions = array( 'jpeg', 'jpg', 'gif', 'png' );
	 
$extension_start = strrpos( $_SERVER['PHP_SELF'], '.' );
$extension = substr( $_SERVER['PHP_SELF'], $extension_start+1 );

if (array_search($extension, $no_search_extensions) !== FALSE ) {
    header( 'Status: ' . $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found' );
		exit;
}


// Check for a custom handler.
$uri = $_SERVER['REQUEST_URI'];
$pos = strpos( $uri, '?' );
$PHP_SELF = $_SERVER['PHP_SELF'] = substr( $uri, 1, ($pos) ? $pos - 1 : strlen( $uri ) - 1 );
require_once("AMP/BaseDB.php");
require_once( 'AMP/Content/Page/Urls.inc.php');

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

    // We haven't found what we're looking for, so flag a 404 error, and try to
    // redirect.
    header( 'Status: ' . $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found' );
    $redirected = false;

    // Search for something to redirect to, based on our redirect table.
    $myURI = $dbcon->qstr(substr($_SERVER['REQUEST_URI'], 1));
    
    if ($exactmatches=$dbcon->GetRow("SELECT * FROM redirect WHERE publish=1 AND old=$myURI")) {
        $redirected = error_redirect($exactmatches['old'], $exactmatches['new']);
    }

    if ($conditional_matches = $dbcon->GetRow("SELECT * FROM redirect WHERE publish=1 and $myURI LIKE CONCAT(old, '%') and conditional=1")) {
        if (!$redirected) {
            $redirected = error_redirect($conditional_matches['old'], $conditional_matches['new'], $conditional_matches['num']);
        }
    }

    $extension_start = strrpos( $_SERVER['PHP_SELF'], '.' );
    $extension = substr( $_SERVER['PHP_SELF'], $extension_start+1 );

    if (array_search($extension, $no_search_extensions) === FALSE ) {
        if (!$redirected) {
            if ( strpos( AMP_CONTENT_URL_404, 'http' ) !== false ) {
                ampredirect(  AMP_CONTENT_URL_404 );
            } else {
                ampredirect (AMP_SITE_URL . AMP_CONTENT_URL_404 );
            }
        }
    }
    trigger_error( 'Requested resource missing: '.$_SERVER['REQUEST_URI'] );

}

function error_redirect( $requested_uri, $target_uri, $num = null ) {


    // This is very confusing, and I'm not entirely clear on what it's supposed
    // to do or accomplish. Oh well...
    $fetch_str = stristr($_SERVER['REQUEST_URI'], $requested_uri);
    $fetch_val = ( isset($num) && $num ) ? substr( $fetch_str, $num ) : "";

    // Check to see if our REQUEST_URI matches in any way the $requested_uri
    if ($fetch_str) {

        // Add the appropriate junk if $target isn't a real URI.
        if (substr($target_uri, 0, 4) == "http") {
            $redirect_uri = $target_uri . $fetch_val;
        } else {
            $redirect_uri = $GLOBALS['Web_url'] . $target_uri . $fetch_val;
        }

        // Everything worked out OK. Redirect and report success.
        ampredirect( $redirect_uri );
        return true;
    }

    // We didn't redirect, report failure.
    return false;
}
*/
?>
