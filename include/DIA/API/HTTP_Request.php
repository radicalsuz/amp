<?php

require_once('DIA/dia_config.php');

require_once('DIA/API.php');
require_once('HTTP/Request.php');

class DIA_API_HTTP_Request extends DIA_API {

	function DIA_API_HTTP_Request() {
		$this->init();
	}

	function getAuthString() {
		global $diaOrgKey, $diaUser, $diaPassword;
		$diaOrgKey = 'pLxGeID1N0t4mAsoHTRA3CqPsfU/EsU8EuvTaUFa/wwDzkADR5zl1g==';

//		if($orgKey = dia_config_get('orgKey')) {
		if(isset($diaOrgKey)) {
			return 'orgKey='.$diaOrgKey;
		}

//		if(($user = dia_config_get('user')) && ($password = dia_config_get('password'))) {
		if(isset($diaUser) && isset($diaPassword)) {
//			return 'user='.$auth['user'].'&password='.$auth['password'];
			return 'user='.$diaUser.'&password='.$diaPassword;
		}

		return false;
	}

	//options are key, debug
	function process( $table, $data ) {
		$api_url = "http://api.demaction.org/dia/api/process.jsp";
		$data['simple'] = true;

        $req =& new HTTP_Request( $api_url );
        $req->setMethod( HTTP_REQUEST_METHOD_GET );
//        $req->setMethod( HTTP_REQUEST_METHOD_POST );
//		  $req->addPostData( $key, $val );

		if(!$auth_string = $this->getAuthString()) return false;
        $req->addRawQueryString( $auth_string );

        $req->addQueryString( 'table', $table );

        foreach ( $data as $key => $val ) {
            $req->addQueryString( $key, $val );
        }

        if ( !PEAR::isError( $req->sendRequest() ) ) {
            $out = $req->getResponseBody();
        } else {
            $out = null;
        }

        return $out;
    }

	//options are key, column, order, limit, where, desc
	function get( $table, $options = null ) {
		$api_url = "http://api.demaction.org/dia/api/get.jsp"; 

        $req =& new HTTP_Request( $api_url );
        $req->setMethod( HTTP_REQUEST_METHOD_GET );
//        $req->setMethod( HTTP_REQUEST_METHOD_POST );

		if(!$auth_string = $this->getAuthString()) return false;
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

        if ( !PEAR::isError( $req->sendRequest() ) ) {
            $out = $req->getResponseBody();
        } else {
            $out = null;
        }
	
        return $out;
	}

}

?>
