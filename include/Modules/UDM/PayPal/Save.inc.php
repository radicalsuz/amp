<?php

require_once('AMP/UserData/Plugin/Save.inc.php');

class UserDataPlugin_Save_PayPal extends UserDataPlugin_Save {

	var $short_name = "PayPal";
	var $available = true;

	var $options = array(
		'api_username' => array(
			'label' => 'API Username',
			'type' => 'text',
			'available' => true,
			'default' => ''),
		'api_password' => array(
			'label' => 'API Password',
			'type' => 'text',
			'available' => true,
			'default' => ''),
		'cert_file' => array(
			'label' => 'SSL certificate location',
			'type' => 'text',
			'available' => true,
			'default' => ''),
		'amount' => array(
			'label' => 'Amount (comma separated or none for user specified)',
			'type' => 'text',
			'available' => true,
			'default' => ''),
		'environment' => array(
			'label' => 'Environment',
			'type' => 'select',
			'available' => true,
			'values' => array('sandbox' => 'sandbox','live' => 'live'),
			'default' => 'sandbox'),
		'response' => array(
			'label' => 'Save response details to',
			'type' => 'select',
			'available' => true,
			'default' => 'none')
		);


	var $_field_prefix = 'plugin_PayPal';

	function UserDataPlugin_Save_PayPal(&$udm, $plugin_instance=null) {
		$this->init($udm, $plugin_instance);
	}

	function getSaveFields() {
		return $this->getAllDataFields();
	}

	function save($data) {
		$data = $this->udm->getData();
		foreach($data as $key => $value) {
			if($field = $this->checkPrefix($key)) {
				$plugin[$field] = $value;
			}
		}

		$options = $this->getOptions();

		require_once('PayPal.php');
		require_once('PayPal/Profile/API.php');
		require_once('PayPal/Profile/Handler.php');
		require_once('PayPal/Profile/Handler/Array.php');
		require_once('PayPal/Type/DoDirectPaymentRequestType.php');
		require_once 'PayPal/Type/DoDirectPaymentRequestDetailsType.php';
		require_once 'PayPal/Type/DoDirectPaymentResponseType.php';
		require_once 'PayPal/Type/BasicAmountType.php';
		require_once 'PayPal/Type/PaymentDetailsType.php';
		require_once 'PayPal/Type/AddressType.php';
		require_once 'PayPal/Type/CreditCardDetailsType.php';
		require_once 'PayPal/Type/PayerInfoType.php';
		require_once 'PayPal/Type/PersonNameType.php';

		$pid =& ProfileHandler::generateID();
		$handler =& ProfileHandler_Array::getInstance(array(
			'username' => $options['api_username'],
			'password' => $options['api_password'],
			'certificateFile' => $options['cert_file'],
			'subject' => null,
			'environment' => $options['environment']));
		$profile =& APIProfile::getInstance($pid, $handler);

		$dp_request =& PayPal::getType('DoDirectPaymentRequestType');
		$firstName = $data['First_Name'];
		$lastName = $data['Last_Name'];
		$creditCardType = $plugin['cc_type'];
		$creditCardNumber = $plugin['cc_number'];
		$expDateMonth = $plugin['cc_expiration']['m'];
		// Month must be padded with leading zero
		$padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);

		$expDateYear = $plugin['cc_expiration']['Y'];
		$cvv2Number = $plugin['cc_cvv2'];
		$address1 = $data['Street'];
		$address2 = isset($data['Street_1'])?$data['Street_1']:null;
		$city = $data['City'];
		$state = $data['State'];
		$zip = $data['Zip'];
		$amount = $plugin['amount'];

		$OrderTotal =& PayPal::getType('BasicAmountType');
		$OrderTotal->setattr('currencyID', 'USD');
		$OrderTotal->setval($amount, 'iso-8859-1');

		$PaymentDetails =& PayPal::getType('PaymentDetailsType');
		$PaymentDetails->setOrderTotal($OrderTotal);

		$shipTo =& PayPal::getType('AddressType');
		$shipTo->setName($firstName.' '.$lastName);
		$shipTo->setStreet1($address1);
		$shipTo->setStreet2($address2);
		$shipTo->setCityName($city);
		$shipTo->setStateOrProvince($state);
		$shipTo->setCountry('US');
		$shipTo->setPostalCode($zip);
		$PaymentDetails->setShipToAddress($shipTo);

		$dp_details =& PayPal::getType('DoDirectPaymentRequestDetailsType');
		$dp_details->setPaymentDetails($PaymentDetails);

		// Credit Card info
		$card_details =& PayPal::getType('CreditCardDetailsType');
		$card_details->setCreditCardType($creditCardType);
		$card_details->setCreditCardNumber($creditCardNumber);
		$card_details->setExpMonth($padDateMonth);
		$card_details->setExpYear($expDateYear);
		$card_details->setCVV2($cvv2Number);

		$payer =& PayPal::getType('PayerInfoType');
		$person_name =& PayPal::getType('PersonNameType');
		$person_name->setFirstName($firstName);
		$person_name->setLastName($lastName);
		$payer->setPayerName($person_name);

		$payer->setPayerCountry('US');
		$payer->setAddress($shipTo);

		if(isset($data['Email'])) $payer->setPayer($data['Email']);

		$card_details->setCardOwner($payer);

		$dp_details->setCreditCard($card_details);
		$dp_details->setIPAddress($_SERVER['SERVER_ADDR']);
		$dp_details->setPaymentAction('Sale');

		$dp_request->setDoDirectPaymentRequestDetails($dp_details);

		$caller =& PayPal::getCallerServices($profile);

		if(PEAR::isError($caller)) {
			trigger_error($caller->getMessage());
			return false;
		}


		$response = $caller->DoDirectPayment($dp_request);

		$ack = $response->getAck();

		define('ACK_SUCCESS', 'Success');
		define('ACK_SUCCESS_WITH_WARNING', 'SuccessWithWarning');
		switch($ack) {
			case ACK_SUCCESS:
			case ACK_SUCCESS_WITH_WARNING:
				if(isset($options['response']) && $options['response']) {
					$response_code = 'Transaction ID: '.$response->getTransactionID()."\n"
								.'Completed AVS Code: '.$response->getAVSCode()."\n"
								.'CVV2 Code: '.$response->getCVV2Code();

                    require_once('AMP/System/User/Profile/Profile.php');
                    $profile =& new AMP_System_User_Profile($this->dbcon, $this->udm->uid);
                    if($profile->hasData()) {
                        $update = array($options['response'] => $response_code);
                        $profile->mergeData($update);
                        $profile->save();
                    } else {
                        trigger_error('cannot update profile with paypal response: '.$response_code);
                    }
				}
				return true;
			default:
				require_once('PayPal/Type/AbstractResponseType.php');
				require_once('PayPal/Type/ErrorType.php');
				require_once 'PayPal/Type/DoDirectPaymentResponseType.php';
				$errors =& $response->getErrors();
				if(!is_array($errors)) $errors = array($errors);
				foreach($errors as $error) {
					trigger_error($error->getShortMessage(). ' : '.$error->getLongMessage());
					$flash =& AMP_System_Flash::instance();
					$flash->add_error($error->getLongMessage());
				}
		}
		return false;
	}

	function _register_options_dynamic() {
        $options[''] = 'none';
        foreach(array_keys($this->udm->fields) as $field) {
            $options[$field] = $field;
        }
		$this->options['response']['values'] = $options;
	}

	function _register_fields_dynamic() {
		//cc#, exp date, cvv2?
        $this_year = date('Y');
        $date_options = array("format"=>"mY","minYear"=>$this_year,"maxYear"=>($this_year+10));
		$this->fields = array(
			'cc_number' => array(
				'label' => 'Card Number',
				'type' => 'text',
				'public' => true,
				'enabled' => true,
				'required' => true
			),
			'cc_type' => array(
				'label' => 'Card Type',
				'type' => 'select',
				'public' => true,
				'enabled' => true,
				'required' => true,
				'values' => array('Visa' => 'Visa','MasterCard' => 'MasterCard','Discover' => 'Discover','Amex' => 'American Express')
			),
			'cc_expiration' => array(
				'label' => 'Expiration Date',
				'type' => 'date',
				'public' => true,
				'enabled' => true,
				'required' => true,
				'options' => $date_options
			),
			'cc_cvv2' => array(
				'label' => 'Card Verification Number',
				'type' => 'text',
				'public' => true,
				'enabled' => true,
				'required' => true,
				'size' => 3
			),
			'amount' => array(
				'label' => 'Amount',
				'public' => true,
				'enabled' => true,
				'required' => true)
		);
		$options = $this->getOptions();
		$amount = $options['amount'];
		if(!$amount) {
			$this->fields['amount']['type'] = 'text';
			$this->fields['amount']['size'] = 8;
		} elseif(false !== strpos($amount,',')) {
			$this->fields['amount']['type'] = 'select';
			$this->fields['amount']['values'] = explode(',',$amount);
		} else {
			$this->fields['amount']['type'] = 'hidden';
			$this->fields['amount']['value'] = $amount;
			$this->fields['amount_static'] = array(
				'type' => 'static',
				'public' => true,
				'enabled' => true,
				'value' => "Amount: $$amount");
		}


	}

    //these are the states paypal uses in the sample code.  we don't currently use them.
	function state_options() {
		return array('AK','AL','AR','AZ','CA','CO','CT','DC','DE','FL','GA','HI','IA','ID','IL','IN','KS','KY','LA','MA','MD','ME','MI','MN','MO','MS','MT','NC','ND','NE','NH','NJ','NM','NV','NY','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VA','VT','WA','WI','WV','WY','AA','AE','AP','AS','FM','GU','MH','MP','PR','PW','VI');
	}
}

?>
