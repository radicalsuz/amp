<?php

require_once( 'Modules/diaRequest.inc.php' );
require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Save_DIA extends UserDataPlugin {

    function UserDataPlugin_Save_DIA(&$udm, $plugin_instance) {
        $this->init($udm, $plugin_instance);
    }


    function execute ( $options = null ) {

        $db_fields   = $this->udm->dbcon->MetaColumnNames('userdata');
        $qf_fields   = array_keys( $this->udm->form->exportValues() );

        $save_fields = array_intersect( $db_fields, $qf_fields );

        $frmFieldValues = $this->udm->form->exportValues($save_fields);

        $diaRequest = new diaRequest( $options[ 'orgCode' ] );
        $result = $diaRequest->addSupporter( $frmFieldValues[ 'Email' ], $frmFieldValues );

        return $result;

    }
}

?>
