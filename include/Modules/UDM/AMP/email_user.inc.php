<?php

/*****
 *
 * udm_amp_email_user ()
 *
 * Email a given user with information about how to update
 * their submitted info.
 *
 *****/

function udm_amp_email_user ( $udm, $options = null ) {

    $message  = "Please go to " . $GLOBALS['Web_url'];
    $message .= "modinput2.php?modin=" . $udm->instance;
    $message .= "&uid=" . $udm->userid;
    $message .= " to update your profile.";
    
    $subject = "Update Your Posting";

    $mailto  = $udm->fields[ 'EmailAddress' ][ 'value' ];
    $header  = $udm->_make_mail_header();
    
    return mail( $mailto, $subject, $message, $header );
 
}

?>
