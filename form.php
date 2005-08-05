<?php
require_once( 'utility.functions.inc.php' );
$urlvalues = AMP_URL_Read();
$url_var_set = AMP_URL_Values();
unset( $url_var_set['id'] );
if (isset($urlvalues['id']) && $urlvalues['id']) {
    $url_var_set['modin'] = "modin=".$urlvalues['id'];
}
$new_url = AMP_URL_AddVars( "modinput4.php", $url_var_set);
ampredirect( $new_url );

?>
