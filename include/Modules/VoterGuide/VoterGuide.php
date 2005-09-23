<?php

require_once( 'AMP/System/Data/Item.inc.php' );
require_once( 'Modules/VoterGuide/Position/Set.inc.php');

class VoterGuide extends AMPSystem_Data_Item {

    var $datatable = 'voterguides';
    var $_positions;
    var $_positions_key = 'voterguidePositions';

    function VoterGuide ( &$dbcon, $id=null ) {
        $this->init( $dbcon, $id );
        $this->_addAllowedKey( $this->_positions_key );
        $this->_positionSet = &new VoterGuidePositionSet( $this->dbcon );
    }

    function readData( $guide_id ) {
        if ( !( $result = PARENT::readData( $guide_id ))) return $result;
        $this->_positionSet->readGuide( $guide_id );
        if ( !$this->_positionSet->hasData()) return $result;
        $this->mergeData( array( $this->_positions_key => $this->_positionSet->getArray()));
        return $result;
    }

    function save() {
        if ( !( $result=PARENT::save())) return $result;
        if ( $result = $this->_positionSet->reviseGuide( $this->getData( $this->_positions_key ), $this->id ) ) return $result;

        $this->addError( $this->_positionSet->getErrors( ));
        return false;

    }

}
?>
