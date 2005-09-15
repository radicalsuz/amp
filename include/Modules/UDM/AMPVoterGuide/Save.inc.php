<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php' );
require_once( 'Modules/VoterGuide/Form.inc.php' );

class UserDataPlugin_Save_AMPVoterGuide extends UserDataPlugin_Save {

    var $_field_prefix = 'plugin_AMPVoterGuide';
    var $_guideForm;

    function UserDataPlugin_Save_AMPVoterGuide ( &$udm, $plugin_instance=null ) {
        $this->init ( $udm, $plugin_instance );
    }

    function _register_fields_dynamic() {
        $this->_guideForm = &new VoterGuide_Form();
        $this->fields = $this->_guideForm->getFields();

        $fieldnames =  array_keys( $this->fields ) ;
        if ($header_key = array_search( 'guidePositionsHeader', $fieldnames )) {
            unset( $fieldnames [ $header_key ] );
        }
        $this->insertBeforeFieldOrder( $fieldnames );
        $this->insertAfterFieldOrder( array('guidePositionsHeader') );
    }

    function getSaveFields() {
        return $this->getAllDataFields();
    }


    function save( $data ) {

        $data['owner_id'] = $this->udm->uid;

        $voterGuide = &new VoterGuide( $this->udm->dbcon );
        $voterGuide->setData( $data );
        if ( ! $voterGuide->save() ) {
            $this->udm->errorMessage( 'VoterGuide Save Failed' );
            return false;
        }
        
        return $this->udm->doPlugin( 'AMPVoterGuide', 'PositionSave', array( 'voterguide_id' => $voterGuide->id ) );
    }

}
?>
