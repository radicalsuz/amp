<?php

require_once( 'Modules/diaRequest.inc.php' );
require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Save_DIA extends UserDataPlugin_Save {
    var $options = array(
        'orgCode' => array(
            'type'=>'text',
            'size'=>'5',
            'available'=>true,
            'label'=>'DIA Organization Code'
            )
        );

    function UserDataPlugin_Save_DIA(&$udm, $plugin_instance) {
        $this->init($udm, $plugin_instance);
    }

    function getSaveFields() {
        $db_fields   = $this->udm->dbcon->MetaColumnNames('userdata');
        $qf_fields   = array_keys( $this->udm->form->exportValues() );
        $this->_field_prefix="";

        return array_intersect( $db_fields, $qf_fields );
    }



    function save ( $data ) {
        $options=$this->getOptions();

        $diaRequest = new diaRequest( $options[ 'orgCode' ] );
        $result = $diaRequest->addSupporter( $data[ 'Email' ], $data);

        return $result;

    }
}

?>
