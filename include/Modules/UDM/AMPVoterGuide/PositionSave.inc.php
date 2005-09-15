<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php' );
require_once( 'AMP/Form/ElementCopierScript.inc.php' );
require_once( 'Modules/VoterGuide/Position/Form.inc.php' );

class UserDataPlugin_PositionSave_AMPVoterGuide extends UserDataPlugin_Save {

    var $_field_prefix = 'plugin_AMPVoterGuidePosition';
    var $_positionForm;
    var $_copierName = 'voterguidePositions';
    var $_coreField = 'item';

    var $options = array(
        'voterguide_id' => array(
            'type' => 'text',
            'default' => null,
            'available' => false )
        );

    function UserDataPlugin_PositionSave_AMPVoterGuide ( &$udm, $plugin_instance=null ) {
        $this->init ( $udm, $plugin_instance );
    }

    function _register_fields_dynamic() {

        $this->_showAddFields();
    }

    function _showAddFields() {
        $this->_positionForm = &new VoterGuidePosition_Form();
        $this->_copier = &ElementCopierScript::instance();
        $this->_copier->addCopier( $this->_copierName, $this->_positionForm->getFields(), $this->udm->name );
        $this->_copier->setPrefix( $this->_copierName, $this->_field_prefix );
        $this->_copier->setCoreField( $this->_copierName, $this->_coreField );

        if (!empty($_POST)) {
            $this->_copier->addSets( $this->_copierName, $_POST );
        }

        $this->_register_javascript( $this->_copier->output() );
        $this->fields = $this->_copier->getAddButton( $this->_copierName );
        
    }
    
    function getSaveFields() {
        return $this->getAllDataFields();
    }

    function _makeSets( $data ) {
        $sets = array();
        foreach( $data[ $this->_coreField ] as $set_index => $data_item ) {
            foreach( $this->fields as $fieldName => $fieldDef ) {
                if ( !isset( $data[ $fieldName ] )) continue;
                $sets [ $set_index ][ $fieldName ] = $data[ $fieldname ][ $set_index ] ;
            }
        }
        return $sets;
    }


    function save( $data ) {
        $options = $this->getOptions();
        if (!isset( $options['voterguide_id'] )) {
            $this->udm->errorMessage( 'No Voter Guide Specified' );
            return false;
        }

        $datasets = $this->_copier->returnSets( $this->_copierName );
        $position_count = 0; 
        if (empty( $datasets )) return true;

        foreach( $datasets as $dataSet ) {
            $position_count++;
            $dataSet['voterguide_id'] = $options['voterguide_id'];
            if (!$dataSet['textorder']) $dataSet['textorder'] = $position_count;

            $vgPosition = &new VoterGuide_Position( $this->udm->dbcon );
            $vgPosition->setData( $dataSet );
            if ( ! $vgPosition->save() ) {
                $this->udm->errorMessage( "Position Save failed for ".$dataSet['item']);
                return false;
            }
        }
        
        return true;
    }

}
?>
