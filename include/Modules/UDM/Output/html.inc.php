<?php

function udm_output_html ( $udm, $options = null ) {

    if ( !isset( $udm->showForm ) || $udm->showForm && !isset( $options['showForm'] ) ) {

        // Ensure we have a form built before proceeding.
        if ( !isset( $udm->form ) )
            $udm->doPlugin( 'QuickForm', 'build' );

        return $udm->form->toHtml();

    }

}

?>
