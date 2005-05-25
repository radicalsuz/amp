<?php

/*****
 *
 * UserDataPlugin_EmailAdmin_AMP
 *
 * if administrator notification is enabled, this function
 * will notify the administrator that a record has been added,
 * along with a text rendering of the data and a link for approval.
 *
 *****/

require_once( 'AMP/UserData/Plugin/Email.inc.php' );

class UserDataPlugin_EmailAdmin_AMP extends UserDataPlugin_Email {

    var $short_name  = 'EmailAdmin_AMP';
    var $long_name   = 'Email Administrator';
    var $description = 'Notifies an administrator whenever a new record is created.';

    var $available = true;

    function UserDataPlugin_EmailAdmin_AMP (&$udm, $plugin_instance=null) {
        $this->init($udm, $plugin_instance);
    }

    function prepareMessage ( $options = null ) {

        $udm   =& $this->udm;

        $message = '';

        $this->options['mailto'] = $udm->_module_def['mailto'];
        $this->options['subject'] = $udm->_module_def['subject'];

		$this->udm->disable_javascript();
        // Output the form data (default format is text, defined above)
#FIXME:options_defs should be options
        $message .= $udm->output( $this->option_defs['format']['default'] );
		$this->udm->enable_javascript();

        // Append Edit/Publish Link
        $message .= "\n\nPlease visit " . $GLOBALS['Web_url'] . 
                    "system/modinput4_edit.php?modin=" . $udm->instance .
                    "&uid=" . $udm->uid . " to publish or edit this record.\n\n";

        return $message;

    }

}

?>
