<?php

/*****
 *
 * udm_amp_email_user ()
 *
 * Email a given user with information about how to update
 * their submitted info.
 *
 *****/

function udm_AMP_email_user ( &$udm, $options = null ) {

    $message  = "Please go to " . $GLOBALS['Web_url'];
    $message .= "modinput4.php?modin=" . $udm->instance;
    $message .= "&uid=" . $udm->uid;
    $message .= " to update your profile.\n\n";
    $message .= $udm->output( 'text' );
    
    $subject = "Update Your Posting";

    $mailto  = $udm->form->exportValue( 'Email' );
    $header  = $udm->_make_mail_header();
    
    return mail( $mailto, $subject, $message, $header );
 
}

?>
