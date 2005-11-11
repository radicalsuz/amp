<?php
require_once ( 'nuSoap/nusoap.php' );
require_once ( 'Sugar/Data/Translator.inc.php' );

/* * * * * * * * * * *
 *  Sugar Data Item
 *
 *  a base class for AMP
 *  to interact with SugarCRM
 *
 *  AMP 3.5.4
 *  2005-08-22
 *
 *  author: austin@radicaldesigns.org
 *
 * * * * */
 if (!defined( 'AMP_SUGAR_LOGIN_USERNAME_ADMIN')) define( 'AMP_SUGAR_LOGIN_USERNAME_ADMIN', 'admin' );
 if (!defined( 'AMP_SUGAR_LOGIN_PASSWORD_ADMIN')) define( 'AMP_SUGAR_LOGIN_PASSWORD_ADMIN', 'changeme' );
 if (!defined( 'SUGAR_URL_SOAP' )) define( 'SUGAR_URL_SOAP', "http://local_sugar.org/soap.php");

class Sugar_Data_Item {

    var $id;
    var $_itemdata;
    var $_itemdata_keys;
    var $_id_field = 'id';

    var $_module;
    var $_errors;
    var $_source;
    var $_session_id;


    function Sugar_Data_Item( $module = null ) {
        $this->init( $module );
    }

    function init( $module=null ) {
        if (isset($module)) {
			if(!$this->setSource ( $module )) return false;
			$this->_itemdata_keys = $this->getColumnNames( $this->_module );
		}
		return true;
    }

	function doLogin() {
		$source =& $this->getSource();
		if($this->getSession()) {
			return true;
		}
        $result = $source->call( 'login', $this->_getLoginArgs() );

//var_dump($this->_getLoginArgs());
//var_dump($result);
        if ($this->_doSoapErrorCheck( $result )) return false; 

        $this->setSession( $result['id'] );
		return true;
	}

	function doLogout() {
		if(!$this->getSession()) return true;

		$source =& $this->getSource();
		
		$result = $source->call( 'logout', $this->getSession() );
        if ($this->_doSoapErrorCheck( $result )) return false; 
		return true;
	}

    function getData( $fieldname = null ) {
        if (! isset( $fieldname )) return $this->_itemdata;
        if ( isset(  $this->_itemdata[ $fieldname ] )) return $this->_itemdata[ $fieldname ];

        return false;
    }

	function &getSource() {
		if(!isset($this->_source)) {
			$this->_source = &new soapclient( SUGAR_URL_SOAP, true );
		}
		return $this->_source;
	}


    function setSource( $module ) {
        $this->_module = ucfirst( $module );
        $this->_source = &new soapclient( SUGAR_URL_SOAP, true );
		if( !$this->doLogin() ) return false;

        $this->_setTranslator();
        return true;
    }

    function _setTranslator() {
        $translator = &new SugarData_Translator();
        if (!$translator->hasTranslation( $this->_module )) return false;
        $this->_translator = &$translator;
    }

    function setSession( $session_id ) {
        $this->_session_id = $session_id;
    }
    
	function getSession() {
		if(isset($this->_session_id)) return $this->_session_id;
		return false;
	}

    function _getSaveArgs() {
        return 
            array ( 'session'     => $this->_session_id, 
                    'module_name' => $this->_module, 
                    'name_value_list' => $this->_makeSoapHash( $this->getData() )
                  );
    }

    function _makeSoapHash( $data ) {
        $result = array();
        foreach( $data as $fieldname => $fieldvalue ) {
            $result[] = array( 'name' => $fieldname, 'value' => $fieldvalue );
        }
        return $result;
    }

    function save() {
        $result = $this->_source->call( 'set_entry', $this->_getSaveArgs() );

        if ($this->_doSoapErrorCheck( $result )) return false; 

        #AMP_varDump( $result );
        #$this->setData( current( $result['entry_list'] ) );
        $this->mergeData( array( $this->_id_field => $result[ 'id' ] ) );
        return $this->id ;
    }

	function read($fields) {
		$source =& $this->getSource();
		$result = $source->call( 'get_entry', $this->_getReadArgs($fields) );
		
        if ($this->_doSoapErrorCheck( $result )) return false; 

		return true;
	}

	function _getReadArgs($fields) {
        return 
            array ( 'session'     => $this->_session_id, 
                    'module_name' => $this->_module, 
					'id'		  => $this->id,
                    'select_fields' => $fields );
	}

    function _getLoginArgs() {
       return   array( 'user_auth' => 
                    array(  'user_name' =>  AMP_SUGAR_LOGIN_USERNAME_ADMIN,
                            'password'  =>  md5( AMP_SUGAR_LOGIN_PASSWORD_ADMIN ), 
                            'version'   =>  '.01'
                          ), 
                     'application_name' =>  'AMP'.get_class( $this ) 
                ); 
    }

    function sugar_encrypt_password($user_name, $user_password)
    {   
        // encrypt the password.
        $salt = substr($user_name, 0, 2);
        $encrypted_password = crypt($user_password, $salt);

        return $encrypted_password;
    }

    function setData( $data ) {
        $this->_itemdata = $this->_filterKeys( $data );
        if (method_exists( $this, '_adjustSetData' ) ) $this->_adjustSetData( $data );
        if (isset($data[$this->_id_field]) && $data[$this->_id_field]) $this->id = $data[$this->_id_field];
    }

    function mergeData( $data ) {
        $this->_itemdata = array_merge( $this->_itemdata, $this->_filterKeys( $data ));
        if (method_exists( $this, '_adjustSetData' ) ) $this->_adjustSetData( $data );
        if (isset($data[$this->_id_field]) && $data[$this->_id_field]) $this->id = $data[$this->_id_field];
    }

    function _filterKeys( $data ) {
        $translated_data = $data;
        if (isset($this->_translator)) {
            $translated_data = $this->_translator->translateKeys( $data, $this->_module ) ;
        }
        return array_combine_key(   $this->_itemdata_keys, $translated_data  );
    }


    function getColumnNames( $module, $reset=false ) {
        if (isset($this->_itemdata_keys) && (!$reset)) return $this->_itemdata_keys;
        $result = $this->_source->call( 'get_module_fields', 
                                array(    'session' =>  $this->_session_id, 
                                          'module'  =>  ucfirst( $module ) 
                                        ) 
                                    );

        if ($this->_doSoapErrorCheck( $result )) return false;

        return $this->_returnFieldNames( $result['module_fields'] );
    }

    function _returnFieldNames( $fieldDefs ) {
        $names = array();

        foreach( $fieldDefs as $fieldDef ) {
            $names[] = $fieldDef[ 'name' ];
        }

        return $names;
    }

    function _isSoapError( $result ) {
        if (!isset($result['error']['number'])) return false;
        return ( $result['error']['number'] );
    }

    function _doSoapErrorCheck( $result ) {
        if (!($error_no = $this->_isSoapError( $result ))) return false; 
//print "error_no: $error_no";

        $this->_addSoapError( $result['error'] );
        return $error_no;
    }

    function _addSoapError( $error_args ) {
        $errormsg =  $error_args['name']. " ".$error_args['number'].":<BR>".$error_args['description'];
        return $this->_addError( $error_msg );
    }

    function _addError( $error ) {
        $this->_errors[] = $error;
    }

    function getErrors() {
		if(empty($this->_errors)) return false;
        return join("<BR>" , $this->_errors);
    }

}

?>
