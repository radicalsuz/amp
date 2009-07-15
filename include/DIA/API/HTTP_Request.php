<?php

require_once(DIA_DIR.'API.php');

require_once(DIA_EXTLIB_PEAR.'HTTP/Request.php');

class DIA_API_HTTP_Request extends DIA_API {

	var $_request;
    var $_bad_xml = array( 'email_trigger_KEYS' => '$email_trigger_KEYS' );

	function DIA_API_HTTP_Request($options = null) {
		$this->init( $options );
	}

	function init( $options = null ) {
        $this->_initDIAUrls( ) ;

		if(isset($options['user'])) {
			$this->_username = $options['user'];
		}

		if(isset($options['password'])) {
			$this->_password = $options['password'];
		}

		if(isset($options['organization_key'])) {
			$this->_organization_key = $options['organization_key'];
		}


		return parent::init();
	}

    function _initDIAUrls( ){
		if(defined('DIA_API_HTTP_REQUEST_INITIALIZED')) {
			return;
		}
        
		if(!defined('DIA_REST_API_GET_URL')) {
			define('DIA_REST_API_GET_URL', 'http://api.demaction.org/dia/api/get.jsp');
		}

		if(!defined('DIA_REST_API_PROCESS_URL')) {
			define('DIA_REST_API_PROCESS_URL', 'http://api.demaction.org/dia/api/process.jsp');
		}

		if(!defined('DIA_REST_API_UNSUBSCRIBE_URL')) {
			define('DIA_REST_API_UNSUBSCRIBE_URL', 'http://api.demaction.org/dia/api/processUnsubscribe.jsp');
		}

		if(!defined('DIA_REST_API_REPORTS_URL')) {
			define('DIA_REST_API_REPORTS_URL', 'http://api.demaction.org/dia/api/reports.jsp');
		}

		if(!defined('DIA_REST_API_DELETE_URL')) {
			define('DIA_REST_API_DELETE_URL', 'http://api.demaction.org/dia/api/delete.jsp');
		}

		define('DIA_API_HTTP_REQUEST_INITIALIZED', true);

    }

	function setUsername($username) {
		$this->_username = $username;
	}

	function setPassword($password) {
		$this->_password = $password;
	}

	function setOrganizationKey($key) {
		$this->_organization_key = $key;
	}

	function &getRequestObject() {
		if(!isset($this->_request)) {
			$this->_request =& new HTTP_Request();
		}
		return $this->_request;
	}

	function setRequestObject(&$request) {
		$this->_request =& $request;
	}

	function &resetRequestObject() {
		unset($this->_request);
		return $this->getRequestObject();
	}

	function setRequestURL( $url ) {
		$req =& $this->getRequestObject();
		$req->setURL($url);
	}

	function setRequestMethod( $method ) {
		$req =& $this->getRequestObject();
		$req->setMethod($method);
	}

	function setRequestUserAgent( $ua ) {
		$req =& $this->getRequestObject();
		$req->addHeader('User-Agent', $ua);
	}
		
	function &initRequestObject( $url ) {
		$this->resetRequestObject();
		$this->setRequestURL($url);
        $this->setRequestMethod( HTTP_REQUEST_METHOD_POST );
		$this->setRequestUserAgent('Radical Designs DIA API class ( http://radicaldesigns.org )');
		$this->setAuth();

		if(defined('DIA_API_ORGANIZATION_KEY')) {
			$req =& $this->getRequestObject();
			$req->addQueryString( 'organization_KEY', DIA_API_ORGANIZATION_KEY );
		}

		return $this->getRequestObject();
	}

	function getAuthString() {
		$authParams = $this->getAuthParams();
		foreach($authParams as $name => $value) {
			$queryStrings[] = $name.'='.$value;
		}

		return join('&', $queryStrings);
	}

	//options are 'hash' or 'pair'
	function getAuthParams() {
		if(($username = $this->getUsername())  && ($password = $this->getPassword())) {
			return array('user' => $username, 'password' => $password);
		}

		$this->error('No external password or user/password defined');
		return false;
	}

	function getUsername() {
		if(isset($this->_username) && $this->_username ) {
			return $this->_username;
		}
		if(defined('DIA_API_USERNAME')) {
			return DIA_API_USERNAME;
		}
	}

	function getPassword() {
		if(isset($this->_password) && $this->_password ) {
			return $this->_password;
		}
		if(defined('DIA_API_PASSWORD')) {
			return DIA_API_PASSWORD;
		}
	}

	function setAuth() {
		$req =& $this->getRequestObject();
		if(HTTP_REQUEST_METHOD_POST == $req->_method) {
			if(!$authParams = $this->getAuthParams()) return false;
			foreach($authParams as $name => $value) {
				$req->addPostData($name, $value);
			}
		} else {
			if(!$auth_string = $this->getAuthString()) return false;
			$req->addRawQueryString( $auth_string, false );
		}
	}

	function cleanXML($xml) {
		//escape invalid tokens, probably the first of many
		return addcslashes($xml,"\x0b");
	}

	//options are key, column, order, limit, where, desc
	function get( $table, $options = null ) {
		if(isset($options) && !is_array($options)) {
			$options = array('key' => $options);
		}
		$xml = $this->getXML($table, $options);
        $bad_xml_replace = array_keys( $this->_bad_xml );
        $xml = str_replace( $this->_bad_xml, $bad_xml_replace, $xml );

		if($xml) {
			require_once(DIA_EXTLIB_PEAR.'XML/Unserializer.php');
			$xmlparser =& new XML_Unserializer();
			$status = $xmlparser->unserialize($xml);
			if(is_a($status, 'XML_Parser_Error')) {
				//escape invalid tokens, probably the first of many
				$xml = $this->cleanXML($xml);
				$xmlparser =& new XML_Unserializer();
				$status = $xmlparser->unserialize($xml);
				if(is_a($status, 'XML_Parser_Error')) return null;
			}

			$unserialized = $xmlparser->getUnserializedData();

			$data = $unserialized[$table]['item'];
			if(1 == $unserialized[$table]['count']) {
//				$data = array($data);
			}
			return $data;
		}
		return false;
	}

	function getXML( $table, $options = null ) {

		$req =& $this->initRequestObject( DIA_REST_API_GET_URL );

        $req->addQueryString( 'table', $table );

		if(isset($options)) {
			foreach ( $options as $option => $value ) {
				if('key' == $option) {
					$keys = $value;
					if(is_array($keys)) {
						$first = true;
						foreach ( $keys as $key ) {
							if($first) {
								$value = $key;
								$first = false;
							} else {
								$value .= ', ' . $key;
							}
						}
					} else {
						$value = $keys;
					}
					$req->addPostData( $option, $value );
					continue;
				}
				$req->addQueryString( $option, $value );
			}
		}

        $req->addQueryString( 'simple', true );

		return $this->handleSendRequest();
	}

	function handleSendRequest() {
		$req =& $this->getRequestObject();
		if(!$req || !isset($req)) {
			$this->error('trying to send request with null request object');
			return false;
		}

		if( defined('DIA_API_DEBUG') && DIA_API_DEBUG ) {
			$this->error("requesting URL: ".$req->_url->getURL());
			$this->error("with post data: ".var_export($req->_postData, true));
		}

        if ( !PEAR::isError( $req->sendRequest() ) ) {
            $body = trim($req->getResponseBody());
			if(preg_match("/<error>(.*)<\/error>/",$body,$errors)) {
				$this->error("DIA returned error: " . $errors[1]);
				$body = null;
			}
        } else {
			if( defined('DIA_API_DEBUG') && DIA_API_DEBUG ) {
				$this->error($body);
			}
            $body = null;
        }
	
        return $body;
	}

	//options are key, debug
	function process( $table, $data ) {

		$req =& $this->initRequestObject( DIA_REST_API_PROCESS_URL );

        $req->addQueryString( 'table', $table );

        foreach ( $data as $key => $val ) {
			if('link' == $key) {
				$links = $this->linkArrayToQueryStringArray($val);
				foreach($links as $link) {
					$req->addQueryString('link', $link);
				}
				continue;
			}
//            $req->addQueryString( $key, $val );
            $req->addPostData( $key, $val );
        }

        $req->addQueryString( 'simple', true );

		$key = $this->handleSendRequest();
		return $key;
    }

	function delete($table, $criteria) {
		if(isset($criteria['key']) && is_array($criteria['key'])) {
			foreach($criteria['key'] as $key) {
				$single_key_criteria = $criteria;
				$single_key_criteria['key'] = $key;
				$results[] = $this->delete($table, $single_key_criteria);
			}
			return $results;
		}

		$req =& $this->initRequestObject( DIA_REST_API_DELETE_URL );

        $req->addQueryString( 'table', $table );

        foreach ( $criteria as $key => $val ) {
            $req->addQueryString( $key, $val );
        }

        $req->addQueryString( 'debug', true );

		$key = $this->handleSendRequest();
		return $key;
	}
		
	//options are supporter_KEY, Email, groups_KEY
	function unsubscribe($data) {
		$req =& $this->initRequestObject( DIA_REST_API_UNSUBSCRIBE_URL );

        foreach ( $data as $key => $val ) {
            $req->addQueryString( $key, $val );
        }

		return $this->handleSendRequest();
	}

	function linkArrayToQueryStringArray($links) {
		if(!is_array($links)) {
			$this->error('linkArrayToString passed a non-array');
			return false;
		}
		foreach($links as $table => $keys) {
			if(is_array($keys)) {
				foreach($keys as $key) {
					$strings[] = $table.'|'.$key;
				}
			} else {
				$strings[] = $table.'|'.$keys;
			}
		}
		return $strings;
	}

}

?>
