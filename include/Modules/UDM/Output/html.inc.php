<?php

function udm_output_html ( $udm, $options = null ) {

    $out = '';

    $results = $udm->getResults(); 

    foreach ( $results as $result ) {
        $out .= '<p class="' . $result['type'] . '">';
        $out .= $result[ 'result' ] . '</p>';
    }

    if ( !isset( $udm->showForm ) || $udm->showForm && !isset( $options['showForm'] ) ) {

        // Ensure we have a form built before proceeding.
        if ( !isset( $udm->form ) )
            $udm->doPlugin( 'QuickForm', 'build' );

        $out .= $udm->form->toHtml();

    }

    return $out;

}

?>
