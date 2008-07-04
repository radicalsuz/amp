<?php 
/* Disabled due to disuse ap 2008-07
 */
/*

$mod_id = 20;
$modid=9;
include("AMP/BaseDB.php"); 

require_once( 'AMP/Content/Redirect/Redirect.php');
$redirect = new AMP_Content_Redirect( AMP_Registry::getDbcon( ));
$target_set = $redirect->find( array( 'alias' => 'email'));
if ( $target_set ) {
    $source = current( $target_set  );
    $new_url = $source->getTarget( );
} else {
    $new_url = AMP_url_add_vars( AMP_CONTENT_URL_FORM, array( 'modin=3')) ;
}

ampredirect( $new_url );

*/
?>
