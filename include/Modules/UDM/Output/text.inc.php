<?php

function udm_output_text ( $udm, $options = null ) {

    // Ensure we have a form built before proceeding.
    if ( !isset( $udm->form ) )
        $udm->doPlugin( 'QuickForm', 'build' );

    foreach ( $udm->form->exportValues() as $field => $value ) {

        $out .= $field . ": " . $value . "\n";

    }

    return $out;

}

?>
