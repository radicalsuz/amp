<?php

function udm_AMP_read ( &$udm, $options = null ) {

    $userid = $options[ '_userid' ];

    if ( !isset( $userid ) ) return false;

    $sql  = "SELECT * FROM userdata WHERE "; //modinid='";
//    $sql .= $udm->instance . "' AND ";
    $sql .= "id='" . $userid . "'";
        
    $rs = $udm->dbcon->CacheExecute( $sql )
        or trigger_error( "Unable to fetch information about user #" . $userid . ":" . $udm->dbcon->ErrorMsg() );
        
    $userData = $rs->FetchRow();
    
    if ( $userData ) {
        foreach ( $userData as $field => $value ) {
            $udm->fields[ $field ][ 'value' ] = $value;
        }
    }
    
    $udm->uid = $userid;

}
 
?>
