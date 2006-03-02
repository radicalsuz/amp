<?php  
//This file has been left alive for legacy compatibility
if (!defined( 'AMP_FORM_ID_EMAIL' )) define( 'AMP_FORM_ID_EMAIL', 3 );
require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/Content/Page/Urls.inc.php' );
ampredirect( AMP_URL_AddVars( AMP_CONTENT_URL_FORM, "modin=".AMP_FORM_ID_EMAIL ));
?>
