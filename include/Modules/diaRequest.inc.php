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

        $supporter_id = $this->process( "supporter", $info );

        // nasty-ass hack. See DIAlist/save.inc.php.
        $GLOBALS['diaSupporter'] = $supporter_id;
        return $supporter_id;

    }

    function linkSupporter ( $list, $supporter_id ) {
        
        $data[ 'linkKey' ] = $list;
        $data[ 'supporter_Key' ] = $supporter_id;

        return $this->process( "link", $data );

    }

/*    function unlinkSupporter ( $list, $supporter_id ) {

        $data[ 'linkKey' ] = $list;
        $data[ 'supporter_Key' ] = $supporter_id;

        return $this->process( "delete" );

    } */

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
