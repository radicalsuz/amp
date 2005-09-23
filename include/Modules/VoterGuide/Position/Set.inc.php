<?php

require_once ('AMP/System/Data/Set.inc.php' );

class VoterGuidePositionSet extends AMPSystem_Data_Set {

	var $datatable = "voterguide_positions";
    var $sort = array( "voterguide_id", "textorder");

	function VoterGuidePositionSet ( &$dbcon, $guide_id =null ) {
		$this->init ($dbcon );
        if ( isset( $guide_id )) $this->readGuide( $guide_id );
	}

    function readGuide( $guide_id ) {
        $this->addCriteria( 'voterguide_id='.$guide_id );
        $this->readData( );
    }

    function reviseGuide ( $positions, $guide_id ) {
        $this->dbcon->StartTrans();
        $this->deleteGuide( $guide_id );
        if ( !$this->createGuide( $positions, $guide_id ) ) $this->dbcon->FailTrans();
        return $this->dbcon->CompleteTrans();
    }

    function deleteGuide ( $guide_id ) {
        $this->deleteData( "voterguide_id=" . $guide_id );
    }

    function createGuide( $positions, $guide_id ) {
        require_once( 'Modules/VoterGuide/Position.php');

        $position_count = 0; 
        foreach ( $positions as $position ) {
            ++$position_count;
            $position['voterguide_id'] = $guide_id;
            $position['textorder'] = $position_count;

            $vgPosition = &new VoterGuide_Position( $this->dbcon );
            $vgPosition->setData( $position );
            if ( ! $vgPosition->save() ) {
                $this->addError( "Position Save failed for ".$position['item']);
                return false;
            }

        }
        return true;
    }
}
?>
