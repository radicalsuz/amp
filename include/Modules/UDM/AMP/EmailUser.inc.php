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

    var $option_defs = array('subject' => array( 'default' => 'Update Your Posting' ),
							 'format' => array( 'default' => 'text' ));
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

		$udm->disable_javascript();
        // Output the form data - default is 'text' as defined in
        // UserDataPlugin_Email.
#FIXME:option_defs should be options
        $message .= $udm->output( $this->option_defs['format']['default'] );
		$udm->enable_javascript();

        return $message;

    }

    function preProcess () {


#XXX: should this be done through getData?  wasn't really working out for me.
#		$mailto = $this->getData(array('Email'));
		$mailto = $this->udm->form->exportValue('Email');
		$subject = $this->option_defs['subject']['default'];

        $this->options['mailto'] = $mailto;
		$this->options['subject'] = $subject;

        // We *must* return true here, or the whole thing will stop.
        return true;

    }
}

?>
