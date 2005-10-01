<?php

//require_once('DIA/dia_config.php');

require_once('DIA/API.php');
require_once('HTTP/Request.php');

class DIA_API_HTTP_Request extends DIA_API {

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

		if(!defined('DIA_REST_API_RESPORTS_URL')) {
			define('DIA_REST_API_REPORTS_URL', 'http://api.demaction.org/dia/api/reports.jsp');
		}

		if(!defined('DIA_API_ORGCODE_KEY')) {
			define('DIA_API_ORGCODE_KEY', 'orgKey');
		}

		return parent::init();
	}

	function getAuthString() {
		if(!defined('DIA_API_ORGCODE') || (!defined('DIA_API_USERNAME') && !defined('DIA_API_PASSWORD'))) {
			$this->error('No orgKey or user/password defined');
		}

		if(defined('DIA_API_ORGCODE')) {
			return DIA_API_ORGCODE_KEY.'='.DIA_API_ORGCODE;
		}

		if(defined('DIA_API_USERNAME') && defined('DIA_API_PASSWORD')) {
			return 'user='.DIA_API_USERNAME.'&password='.DIA_API_PASSWORD;
		}

		return false;
	}

	//options are key, debug
	function process( $table, $data ) {
        $req =& new HTTP_Request( DIA_REST_API_PROCESS_URL );
        $req->setMethod( HTTP_REQUEST_METHOD_GET );
//        $req->setMethod( HTTP_REQUEST_METHOD_POST );
//		  $req->addPostData( $key, $val );

		if(!$auth_string = $this->getAuthString()) return false;
        $req->addRawQueryString( $auth_string, false );

		if(defined('DIA_API_ORGANIZATION_KEY')) {
			$req->addQueryString( 'organization_KEY', DIA_API_ORGANIZATION_KEY );
		}

        $req->addQueryString( 'table', $table );

        foreach ( $data as $key => $val ) {
            $req->addQueryString( $key, $val );
        }

        $req->addQueryString( 'simple', true );

		if( DIA_API_DEBUG ) {
			print "requesting ".$req->_url->getURL();
		}

        if ( !PEAR::isError( $req->sendRequest() ) ) {
            $out = trim($req->getResponseBody());
        } else {
            $out = null;
        }

        return $out;
    }

	//options are key, column, order, limit, where, desc
	function get( $table, $options = null ) {

        $req =& new HTTP_Request( DIA_REST_API_GET_URL );
        $req->setMethod( HTTP_REQUEST_METHOD_GET );
//        $req->setMethod( HTTP_REQUEST_METHOD_POST );

		if(!$auth_string = $this->getAuthString()) return $this->error('No auth string returned');
        $req->addRawQueryString( $auth_string );

        $req->addQueryString( 'table', $table );

		if(isset($options)) {
			if(isset($options['key'])) {
				$keys = $options['key'];
				if(is_array($keys)) {
					$first = true;
					foreach ( $keys as $key ) {
						if($first) {
							$keyString = $key;
							$first = false;
						} else {
							$keyString .= ', ' . $key;
						}
					}
				} else {
					$keyString = $keys;
				}
				$req->addQueryString( 'key', $keyString );
			}
		}

        $req->addQueryString( 'simple', true );

        if ( !PEAR::isError( $req->sendRequest() ) ) {
            $out = $req->getResponseBody();
        } else {
            $out = null;
        }
	
        return $out;
	}

}

?>
