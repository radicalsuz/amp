<?php

require_once( 'HTTP/Request.php' );

class diaRequest {

    var $org_id;
    var $api_url;

    function diaRequest ( $org_id, $url = null ) {

        // Set the access code / organizational ID.
        $this->org_id = $org_id;

        // Set a default api URL in the absence of one from the creator.
        $this->api_url = ( $url ) ? $url : "http://www.demaction.org/dia/api/process.jsp";

    }

    function addSupporter ( $email, $info = array() ) {

        $info[ 'Email' ] = $email;

        $supporter_id = $this->process( "supporter", $info );

        // nasty-ass hack. See DIAlist/save.inc.php.
        $GLOBALS['diaSupporter'] = trim( $supporter_id );
        return $supporter_id;

    }

    function linkSupporter ( $list, $supporter ) {

        $data = array();
        
        $data[ 'link' ] = 'groups';
        $data[ 'linkKey' ] = $list;
        $data[ 'key' ] = $supporter;
        $data[ 'updateRowValues' ] = 1;

        return $this->process( "supporter", $data );

    }

/*    function unlinkSupporter ( $list, $supporter_id ) {

        $data[ 'linkKey' ] = $list;
        $data[ 'supporter_Key' ] = $supporter_id;

        return $this->process( "delete" );

    } */

    function process ( $table, $data ) {

        $req =& new HTTP_Request( $this->api_url );
        $req->setMethod( HTTP_REQUEST_METHOD_GET );

        foreach ( $data as $key => $val ) {
            $req->addQueryString( $key, $val );
        }

        $req->addQueryString( 'org', trim( $this->org_id ) );
        $req->addQueryString( 'table', $table );

        if ( !PEAR::isError( $req->sendRequest() ) ) {
            $out = $req->getResponseBody();
        } else {
            $out = null;
        }

        return $out;
    }

}

?>
