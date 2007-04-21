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

    function _register_options_dynamic() {
    	parent::_register_options_dynamic();
        $this->options['mailto']['default'] = $this->udm->_module_def['mailto'];
        $this->options['subject']['default'] = $this->udm->_module_def['subject'];
        $this->options['update_page']['default'] = "system/modinput4_view.php";
    }

    function prepareMessage ( $options = array( )) {

        $udm   =& $this->udm;
        $options = array_merge($this->getOptions(), $options);

        $message = '';


		$this->udm->disable_javascript();
        // Output the form data (default format is text, defined above)
        $message .= $udm->output( $options['format'] );
		$this->udm->enable_javascript();

        // Append Edit/Publish Link
        $message .= "\n\nPlease visit http://" . $_SERVER['SERVER_NAME'] . 
                    "/".$options['update_page']."?modin=" . $udm->instance .
                    "&uid=" . $udm->uid . " to publish or edit this record.\n\n";

		$html = isset($options['format']) && ('html' == strtolower($options['format']));
		if($html) {
			$this->containsHTML($html);
		} elseif($this->containsHTML()) {
			$message = nl2br($message);
		}

        return $message;

    }

}

?>
