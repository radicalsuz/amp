<?php

/*****
 *
 * udm_amp_email_user ()
 *
 * Email a given user with information about how to update
 * their submitted info.
 *
 *****/

require_once('AMP/UserData/Plugin/Email.inc.php');

class UserDataPlugin_EmailUser_AMP extends UserDataPlugin_Email {

    var $short_name  = 'EmailUser_AMP';
    var $long_name   = 'Email User';
    var $description = 'Notifies the user that their record has been created.';

    var $option_defs = array('subject' => array( 'default' => 'Update Your Posting' ));

    var $available = true;

    function UserDataPlugin_EmailUser_AMP ( &$udm, $plugin_instance=null ) {
        $this->init($udm, $plugin_instance);
    }

    function prepareMessage ( $options = null ) {

        $udm =& $this->udm;

        //Update Link
        $message = "\n\nPlease go to " . $GLOBALS['Web_url'] .
                   "modinput4.php?modin=" . $udm->instance .
                   "&uid=" . $udm->uid . " to update your information.\n\n";

        // Output the form data - default is 'text' as defined in
        // UserDataPlugin_Email.
        $message .= $udm->output( $options['format'] );

        return $message;

    }

    function preProcess () {

        $this->options['mailto'] = $this->getData('Email');

        // We *must* return true here, or the whole thing will stop.
        return true;

    }
}

?>
