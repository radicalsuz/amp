<?php

/*****
 *
 * udm_amp_email_admin ()
 *
 * if administrator notification is enabled, this function
 * will notify the administrator that a record has been added,
 * along with a text rendering of the data and a link for approval.
 *
 *****/

function udm_amp_email_admin ( $udm, $options = null ) {

    if ( !isset( $udm->mailto ) ) return false;

    $message  = $udm->output( 'text' );
        
    $message .= "\n\nPlease visit " . $GLOBALS['Web_url'];
    $message .= "system/moddata2.php?modin=" . $udm->instance;
    $message .= "&id=" . $udm->userid;
    $message .= " to publish.";
        
    $header = $udm->_make_mail_header();
        
    return mail( $udm->mailto, $udm->subject, $message, $header );

}

?>
