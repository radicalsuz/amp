<?php

function udm_amp_fixup_db ( &$udm, $options = null ) {

    $dbcon =& $udm->dbcon;

    // Fetch DB Structure
    $fields = $dbcon->MetaColumnNames( 'userdata_fields' );

    if ( !array_search( 'publish', $fields ) ) {

        $sql = 'ALTER TABLE userdata_fields ADD COLUMN publish INT(1) DEFAULT NULL';
        $dbcon->Execute( $sql ) or
            die( "Couldn't fixup database structure: " . $dbcon->ErrorMsg() );

    }

}

?>
