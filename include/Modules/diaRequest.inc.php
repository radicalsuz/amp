<?php

class diaRequest {

    var $org_id;
    var $api_url;

    function diaRequest ( $org_id, $url = null ) {

        // Set the access code / organizational ID.
        $this->org_id = $org_id;

        // Set a default api URL in the absence of one from the creator.
        $this->api_url = ( $url ) ? $url : "http://www.demaction.org/dia/api/process.jsp";

    }

    function addSupporter ( $email, $info = null ) {

        $info[ 'Email' ] = $email;

        return $this->process( "supporter", $info );

    }

    function process ( $table, $data ) {

        foreach ( $data as $key => $val ) {
            $req_str .= "&" . urlencode($key) . "=" . urlencode($val);
        }

        $req_url = $this->api_url . "?org=" . $this->org_id  . "&table=" . $table  . $req_str;

        $req = fopen( $req_url, "rb" );

        while (!feof($req)) {
            $out .= fread($req, 8192);
        }

        return $out;
    }

}

?>
