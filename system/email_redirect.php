<?php
require_once("AMP/BaseDB.php");

$phplist_target = ( strtoupper( AMP_MODULE_BLAST ) == 'PHPLIST' ) ? AMP_SYSTEM_URL_PHPLIST_LOGIN : false;
$dia_target = ( strtoupper( AMP_MODULE_BLAST ) == 'DIA' )     ? AMP_DIA_URL_LOGIN : false;

if ( !( $phplist_target || $dia_target )) {
    include ("header.php");
    echo "<b>You do not have a email list program defined</b>";
    include ("footer.php");
}

if ( $phplist_target )  {
    ampredirect( $phplist_target );
    exit;
}

if ( $dia_target && !( DIA_API_USERNAME && DIA_API_PASSWORD )) {
    ampredirect( $dia_target );
    exit;
}

require_once( 'AMP/Content/Header.inc.php');

$renderer = AMP_get_renderer( );
$form_data = $renderer->input( 'email', DIA_API_USERNAME, array( 'type' => 'hidden') );
$form_data .= $renderer->input( 'password', DIA_API_PASSWORD, array( 'type' => 'hidden') );
$form_data .= $renderer->submit( 'go', ucwords( AMP_TEXT_LOGIN ));
$form_data .= $renderer->image( '/img/ajax-loader.gif');
$script = 
<<<JAVASCRIPT
window.onload=submit_dia_login;
function submit_dia_login( ) {
    document.forms['f'].submit( );
}

JAVASCRIPT;

print $renderer->form( $form_data, array( 'action' => 'https://salsa.democracyinaction.org/dia/hq/processLogin.jsp', 'name' => 'f' ));
print AMP_HTML_JAVASCRIPT_START . $script . AMP_HTML_JAVASCRIPT_END;


?>
