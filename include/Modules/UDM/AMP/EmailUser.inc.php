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

    var $available = true;

    function UserDataPlugin_EmailUser_AMP ( &$udm, $plugin_instance=null ) {
        $this->init($udm, $plugin_instance);
    }

    function _register_options_dynamic() {
        parent::_register_options_dynamic();
        $this->options['subject']['default']='Update Your Posting';
    }

    function prepareMessage ( $options = array( )) {

        $udm =& $this->udm;
        $options = array_merge($this->getOptions(), $options);

        //Update Link
		if ( isset( $options['form_data_intro']) && $options['form_data_intro']) {
			if(!(strtolower($options['form_data_intro']) == 'none')) {
				$message = "\n\n".$options['form_data_intro']."\n\n";
			}
		} else {
			$message = "\n\nPlease go to http://" . $_SERVER['SERVER_NAME'] .
					   "/" . $options['update_page'] . "?modin=" . $udm->instance .
					   "&uid=" . $udm->uid . " to update your information.\n\n";
		}

		if(isset($options['include_form_data']) && $options['include_form_data']) {
			$udm->disable_javascript();
			// Output the form data - default is 'text' as defined in
			// UserDataPlugin_Email.
			$message .= $udm->output( $options['format'] );
			$udm->enable_javascript();
		}

		$html = isset($options['format']) && ('html' == strtolower($options['format']));
		if($html) {
			$this->containsHTML($html);
		} elseif($this->containsHTML()) {
			$message = nl2br($message);
		}

        return $message;

    }

    function preProcess () {
        //Remove the field prefix
        //this allows access to core UDM data
        $field_prefix = $this->_field_prefix;

        $this->_field_prefix = '';
        $answer = $this->getData( array('Email') );

        $this->_field_prefix = $field_prefix;

        if ( $answer['Email'] ) {
            $this->options['mailto']['default'] = $answer['Email'];
            return true;
        }

        //if no email is found, don't try to send
        return false;

        
    }
    /*

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
