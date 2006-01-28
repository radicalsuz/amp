<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php' );
require_once( 'HTTP/Request.php' );

class UserDataPlugin_Save_GetActive extends UserDataPlugin_Save {
    var $options = array(
		'hostname' => array(
			'type'=>'text',
			'size'=>'20',
			'available'=>true,
			'label'=>'the hostname for your GA-powered website'),
		'domain' => array(
            'type'=>'text',
            'size'=>'20',
            'available'=>true,
            'label'=>'the GA "domain code" for your Center'
			),
		'update' => array(
			'type'=>'checkbox',
			'available'=>true,
			'label'=>"Determines whether or not an existing  member's record will be updated with the  posted data",),
		'no_welcome' => array(
			'type'=>'checkbox',
			'available'=>true,
			'label'=>'Determines whether or not to suppress a  welcome email to the member'),
		'source' => array(
			'type'=>'text',
			'size'=>'20',
			'available'=>true,
			'label'=>'Source tracking code'),
		'field_email_pref' => array(
			'type'=>'select',
			'available'=>true,
			'label'=>'The custom field that represents email preference'),
		'field_options_email_pref_html' => array(
			'type'=>'text',
			'size'=>'20',
			'available'=>true,
			'label'=>'Email preference option value that corresponds to \'html\''),
		'field_options_email_pref_plain' => array(
			'type'=>'text',
			'size'=>'20',
			'available'=>true,
			'label'=>'Email preference option value that corresponds to \'plain\''),
		'field_birthday' => array(
			'type'=>'select',
			'available'=>true,
			'label'=>'The custom field that represents birthday'),
		'field_sex' => array(
			'type'=>'select',
			'available'=>true,
			'label'=>'The custom field that represents sex'),
		'field_options_sex_m' => array(
			'type'=>'text',
			'size'=>'20',
			'available'=>true,
			'label'=>'Sex preference option value that corresponds to \'m\' (male)'),
		'field_options_sex_f' => array(
			'type'=>'text',
			'size'=>'20',
			'available'=>true,
			'label'=>'Email preference option value that corresponds to \'f\' (female)'),
        );

	var $_field_prefix = 'GA';

	var $ERROR;

    function UserDataPlugin_Save_GetActive(&$udm, $plugin_instance) {
        $this->init($udm, $plugin_instance);
    }

	function _register_fields_dynamic() {
		$this->fields['optin'] = array(
										'label'    => 'Join our list?',
										'public'   => true,
										'enabled'  => true,
										'type'     => 'checkbox',
										'required' => false,
										'default'  => 1 );
	}

	function _register_options_dynamic() {
		foreach(array_keys($this->options) as $name) {
			$option_const = 'AMP_PLUGIN_GETACTIVE_SAVE_'.strtoupper($name);
			if(!isset($this->options[$name]['value']) && defined($option_const)) {
				$this->options[$name]['value'] = constant($option_const);
			}
		}
	}

    function getSaveFields() {
        $qf_fields   = array_keys( $this->udm->form->exportValues() );
        $this->_field_prefix="";

		return $qf_fields;
	}

    function save ( $data ) {
		if(!$data['GA_optin']) {
			trigger_error('did not opt to join');
			return false;
		}
        $options=$this->getOptions();

		$request =& new HTTP_Request($options['hostname'].'/offsite-join.tcl');
		$request->setMethod(HTTP_REQUEST_METHOD_POST);
		$request->addPostData('domain', $options['domain']);
		if($options['source']) {
			$request->addPostData('source', $options['source']);
		}
		if($options['update']) {
			$request->addPostData('update', $options['update']);
		}
		
		foreach($this->translate($data) as $name => $value) {
			$request->addPostData($name, $value);
		}

		if( !PEAR::isError( $request->sendRequest() ) )	{
			$message = trim($request->getResponseBody());
			if('OK' == $message) {
				return true;
			} else {
				$this->ERROR = $message;
				trigger_error($message);
				return false;
			}
		}
    }

	//and filter
	function translate( $data ) {
		$translation = array(
						'Email'			=>	'email',
						'Title'			=>	'prefix',
						'First_Name'	=>	'first_names',
						'Last_Name'		=>	'last_name',
						'Suffix'		=>	'suffix',
						'Street'		=>	'address_line1',
						'Street_2'		=>	'address_line2',
						'City'			=>	'city',
						'State'			=>	'state',
						'Country'		=>	'country',
						'Zip'			=>	'zip_code',
						'Phone'			=>	'phone',
						'occupation'	=>	'title',
						'Company'		=>	'organization');

		foreach($data as $key => $value) {
			if(isset($translation[$key]) && !empty($value)) {
				$return[$translation[$key]] = $value;
			}
		}

		$options = $this->getOptions();
		if($options['field_email_pref'] && $email_pref = $data[$options['field_email_pref']]) {
			if(AMP_PLUGIN_GETACTIVE_SAVE_FIELD_OPTIONS_EMAIL_PREF_HTML == $email_pref) {
				$return['email_pref'] = 'html';
			} elseif(AMP_PLUGIN_GETACTIVE_SAVE_FIELD_OPTIONS_EMAIL_PREF_PLAIN == $email_pref) {
				$return['email_pref'] = 'plain';
			} else {
				//else we'll just try our luck
				$return['email_pref'] = $email_pref;
			}
		}

		if($options['field_birthday'] && $birthday = $data[$options['field_birthday']]) {
			$return['birthday'] = $birthday;
		}

		if($options['field_sex'] && $sex = $data[$options['field_sex']]) {
			if(AMP_PLUGIN_GETACTIVE_SAVE_FIELD_OPTIONS_SEX_M == $sex) {
				$return['sex'] = 'm';
			} elseif(AMP_PLUGIN_GETACTIVE_SAVE_FIELD_OPTIONS_SEX_F== $sex) {
				$return['sex'] = 'f';
			} else {
				//else we'll just try our luck
				$return['sex'] = $sex;
			}
		}

		return $return;
	}
}

?>
