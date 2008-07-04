<?php 
/*********************
07-02-2003  v3.01
Module:  email
Description:  email subscription form 
CSS: text, form
VARS: $studenton = displays the student box
			$send = sends a link to edit  the subscriptions
To Do:  declare  post vars
			   insert into contacts database

*********************/ 

 // 
 
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

?>
