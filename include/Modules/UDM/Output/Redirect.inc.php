<?php

function udm_output_redirect ( $udm, $options = array( )) {

    if ( isset( $udm->redirectURL ) ) {

        $redirURL = $udm->redirectURL;

    } elseif ( isset( $options[ 'redirectURL' ] ) ) {

        $redirURL = $options[ 'redirectURL' ];

    }

    header( 'Location: ' . $redirURL );

}
?>
