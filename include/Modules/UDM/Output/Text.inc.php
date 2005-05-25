<?php

require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Text_Output extends UserDataPlugin {

    function UserDataPlugin_Text_Output ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

    function execute ( $options = null ) {

        return udm_output_text( $this->udm );

    }

}

function udm_output_text ( &$udm, $options = null ) {

    // Ensure we have a form built before proceeding.
    if ( !isset( $udm->form ) )
        $udm->doPlugin( 'QuickForm', 'build' );

    foreach ( $udm->form->exportValues() as $field => $value ) {
		$fieldname=isset($udm->fields[$field]) ? strip_tags($udm->fields[$field]['label']) : $field;
        $out .= $fieldname . ": " . $value . "\n";

    }

    return $out;

}

?>
