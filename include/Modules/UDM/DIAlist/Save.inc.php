<?php

require_once( 'Modules/diaRequest.inc.php' );

function udm_DIAlist_save ( &$udm, $options ) {

    if ( $udm->uselists ) {

        $diaRequest = new diaRequest( $options[ 'orgCode' ] );

        foreach ( array_keys( $udm->lists ) as $list_id ) {
            $list_fields[] = 'list_' . $list_id;
        }

        $listValues = $udm->form->exportValues( $list_fields );

        foreach ( $listValues as $listField => $value ) {

            $listid = substr( $listField, 5 );

            if ( $value ) {
                // nasty-ass hack. change this by adding a "result data" object
                // or something to the UDM object.
                $result = $diaRequest->linkSupporter( $listid, $GLOBALS['diaSupporter'] );
            }
        }

    }

	return $result;

}

?>
