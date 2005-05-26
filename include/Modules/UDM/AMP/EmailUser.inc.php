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

    /*
    var $options = array(
        'subject' => array( 
            'default' => 'Update Your Posting',
            'available'=>true,
            'type'=>'text'
            'label'=>'Email Subject Line'),
        'format' => array( 
            'default' => 'Text' )
        );
    */
    var $available = true;

    function UserDataPlugin_EmailUser_AMP ( &$udm, $plugin_instance=null ) {
        $this->init($udm, $plugin_instance);
    }

    function _register_options_dynamic() {
        $this->options['subject']['default']='Update Your Posting';
        $this->options['mailto']['default'] = current($this->getData( array('Email') ));
    }

    function prepareMessage ( $options = null ) {

        $udm =& $this->udm;
        $options = array_merge($this->getOptions(), $options);

        //Update Link
        $message = "\n\nPlease go to " . $_SERVER['SERVER_NAME'] .
                   $options['update_page']."?modin=" . $udm->instance .
                   "&uid=" . $udm->uid . " to update your information.\n\n";

		$udm->disable_javascript();
        // Output the form data - default is 'text' as defined in
        // UserDataPlugin_Email.
        $message .= $udm->output( $options['format'] );
		$udm->enable_javascript();

        return $message;

    }

    /*
    function preProcess () {


		$mailto = $this->udm->form->exportValue('Email');
		$subject = $this->option_defs['subject']['default'];

        $this->options['mailto'] = $mailto;
		$this->options['subject'] = $subject;

        // We *must* return true here, or the whole thing will stop.
        return true;

    }
    */
}

?>
