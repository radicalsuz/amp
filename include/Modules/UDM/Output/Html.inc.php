<?php

require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_HTML_Output extends UserDataPlugin {

    function UserDataPlugin_HTML_Output ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

    function execute ( $options = null ) {

        return udm_output_html( $this->udm );

    }

}

function udm_output_html ( &$udm, $options = null ) {

    $out = '';

    $results = $udm->getResults(); 

    foreach ( $results as $result ) {
        $out .= '<p class="' . $result['type'] . '">';
        $out .= $result[ 'result' ] . '</p>';
    }

    $out .= $udm->outputErrors();

    if ( !isset( $udm->showForm ) || $udm->showForm && !isset( $options['showForm'] ) ) {

        // Ensure we have a form built before proceeding.
        if ( !isset( $udm->form ) ) $udm->doPlugin( 'QuickForm', 'Build', $options );

        $out .= $udm->form->display();

    }

	if ( $javascript = $udm->get_plugin_javascript()) {
		$out .= $javascript;
	}

    return $out;

}

?>
