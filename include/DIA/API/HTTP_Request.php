<?php

require_once(DIA_DIR.'API.php');
require_once(DIA_PEAR_DIR.'HTTP/Request.php');

class DIA_API_HTTP_Request extends DIA_API {

	var $_request;
	var $_links = array();

	function DIA_API_HTTP_Request() {
		$this->init();
	}

	function init() {
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

		if(!defined('DIA_API_ORGCODE_KEY')) {
			define('DIA_API_ORGCODE_KEY', 'orgKey');
		}

		return parent::init();
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
	function getAuthParams($type = 'hash') {
		if(defined('DIA_API_ORGCODE') && 'hash' == $type) {
			return array(DIA_API_ORGCODE_KEY => DIA_API_ORGCODE);
		}

		if(defined('DIA_API_USERNAME') && defined('DIA_API_PASSWORD') && 'pair' == $type) {
			return array('user' => DIA_API_USERNAME, 'password' => DIA_API_PASSWORD);
		}

		$this->error('No external password or user/password defined');
		return false;
	}

	function setAuth($type = 'hash') {
		$req =& $this->getRequestObject();
		if(HTTP_REQUEST_METHOD_POST == $req->_method) {
			if(!$authParams = $this->getAuthParams($type)) return false;
			foreach($authParams as $name => $value) {
				$req->addPostData($name, $value);
			}
		} else {
			if(!$auth_string = $this->getAuthString($type)) return false;
			$req->addRawQueryString( $auth_string, false );
		}
	}

	//options are key, column, order, limit, where, desc
	function get( $table, $options = null ) {
		if(isset($options) && !is_array($options)) {
			$options = array('key' => $options);
		}
		$xml = $this->getXML($table, $options);
		if($xml) {
			require_once('XML/Unserializer.php');
			$xmlparser =& new XML_Unserializer();
			$status = $xmlparser->unserialize($xml);
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

	function retrySendRequest() {
		$this->error('retrying send request');

		//wish they didn't make me do this
		$req =& $this->getRequestObject();
		if($req->_url->querystring[DIA_API_ORGCODE_KEY]) {
			$req->_url->removeQueryString(DIA_API_ORGCODE_KEY);
		} elseif (!empty($req->_postData) && $req->_postData[DIA_API_ORGCODE_KEY]) {
			unset($req->_postData[DIA_API_ORGCODE_KEY]);
		} else {
			return null;
		}
		$this->setAuth('pair');
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
				if(false !== strpos($errors[1],'Invalid login')) {
					$body = $this->retrySendRequest();
				} else {
					$body = null;
				}
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
            $req->addQueryString( $key, $val );
//            $req->addPostData( $key, $val );
        }

        $req->addQueryString( 'simple', true );

		$key = $this->handleSendRequest();
		return $key;
    }

	function addToLinks($links) {
		if(is_null($links) || !is_array($links)) {
			return false;
		}
		$this->_links += $links;
	}

	function getLinks() {
		return $this->_links;
	}

	//options are supporter_KEY, Email, groups_KEY
	function unsubscribe($data) {
		$req =& $this->initRequestObject( DIA_REST_API_UNSUBSCRIBE_URL );

        foreach ( $data as $key => $val ) {
            $req->addQueryString( $key, $val );
        }

		return $this->handleSendRequest();
	}

	//returns an array of supporter_groups record keys?  sure...
	function addMembers($supporters, $groups) {
		if(!is_array($groups)) {
			$groups = array($groups);
		}
		if(!is_array($supporters)) {
			$supporters = array($supporters);
		}

		foreach($groups as $group) {
			foreach($supporters as $supporter) {
				$result[] = $this->linkSupporter($group, $supporter);
			}
		}
		return $result;
	}

	function addMembersByEmail($emails, $groups) {
		if(!is_array($emails)) {
			$emails = array($emails);
		}

		foreach($emails as $email) {
			$members[] = $this->addSupporter($email);
		}
		return $this->addMembers($members, $groups);
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
