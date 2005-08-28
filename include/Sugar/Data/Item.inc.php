<?php
require_once ( 'nuSoap/nusoap.php' );

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

    var $_itemdata;
    var $_itemdata_keys;

    var $_module;
    var $_errors;
    var $_source;
    var $_session_id;

    function Sugar_Data_Item( $module = null ) {
        $this->init( $module );
    }

    function init( $module=null ) {
        if (isset($module)) $this->setSource ( $module );
        $this->_itemdata_keys = $this->getColumnNames( $this->_module );
    }

    function getData( $fieldname = null ) {
        if (! isset( $fieldname )) return $this->_itemdata;
        if ( isset(  $this->_itemdata[ $fieldname ] )) return $this->_itemdata[ $fieldname ];

        return false;
    }

    function setSource( $module ) {
        $this->_module = ucfirst( $module );
        $this->_source = &new soapclient( SUGAR_URL_SOAP, true );
        $result = $this->_source->call( 'login', $this->_getLoginArgs() );

        if (!$this->_doSoapErrorCheck( $result )) {
            $this->_session_id = $result['id'];
            return true;
        }

        return false;
    }

    function _isSoapError( $result ) {
        return ( $result['error']['number'] );
    }

    function _doSoapErrorCheck( $result ) {
        AMP_varDump( $result );
        if (!($error_no = $this->_isSoapError( $result ))) return false; 

        $this->_addSoapError( $result['error'] );
        return $error_no;
    }

    function _addSoapError( $error_args ) {
        $errormsg =  $error_args['name']. " ".$error_args['number'].":<BR>".$error_args['description'];
        return $this->_addError( $error_msg );
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

    function setData( $data ) {
        $this->_itemdata = array_combine_key( $this->_itemdata_keys, $this->_translateKeys( $data ) );
        if (method_exists( $this, '_adjustSetData' ) ) $this->_adjustSetData( $data );
        if (isset($data[$this->id_field]) && $data[$this->id_field]) $this->id = $data[$this->id_field];
    }

    function _translateKeys( $data_array ) {
        $result_data = array();
        foreach( $data_array as $dKey => $dValue ) {
            $result_data[ strtolower( $dKey ) ] = $dValue;
        }
        return $result_data;
    }

    function mergeData( $data ) {
        $this->_itemdata = array_merge( $this->_itemdata, array_combine_key( $this->_itemdata_keys, $data ));
        if (method_exists( $this, '_adjustSetData' ) ) $this->_adjustSetData( $data );
        if (isset($data[$this->id_field]) && $data[$this->id_field]) $this->id = $data[$this->id_field];
    }


    function getColumnNames( $module, $reset=false ) {
        print $module;
        if (isset($this->_itemdata_keys) && (!$reset)) return $this->_itemdata_keys;
        $result = $this->_source->call( 'get_module_fields', array( 'session' => $this->_session_id, 'module'=> ucfirst( $module ) ) );
        if (!$this->_doSoapErrorCheck( $result )) {
            return $result['module_fields'];
        }
        return false;
    }
    function _addError( $error ) {
        $this->_errors[] = $error;
    }

    function getErrors() {
        return join("<BR>" , $this->_errors);
    }

    function save() {
        $result = 
            $this->_source->call( 'set_entry', 
                array(  'session'     => $this->_session_id, 
                        'module_name' => $this->_module, 
                        'name_value_list' => $this->getData() 
                      ) );
        if (!$this->_doSoapErrorCheck( $result )) {
            $this->setData( current( $result['entry_list'] ) );
            return $this->id;
        }
        return false;
    }

}

?>
