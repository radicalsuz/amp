<?php

require_once('Modules/UDM/AMP/read.inc.php');

function udm_volunteer_read ( &$udm, $options = null ) {
	udm_AMP_read($udm, $options);
	
    $uid = $udm->uid;
	if ( !isset( $uid ) ) return false;
	//Read Interests

    $sql  = "SELECT * FROM vol_relinterest WHERE "; //modinid='";
//    $sql .= $udm->instance . "' AND ";
    $sql .= "personid='" . $uid . "'";      
    
	if ($volData = $udm->dbcon->GetAssoc( $sql )) {
			
		foreach ($volData as $int_id=>$interest) {
			$fieldname='vol_interest'.$interest['interestid'];
			if ($udm->fields[$fieldname]) {
				$udm->fields[ $fieldname ][ 'value' ] = 1;
			}
		}	
	}

	//Read Skills

    $sql  = "SELECT * FROM vol_relskill WHERE "; //modinid='";
    $sql .= "personid='" . $uid . "'";
    if ($volData = $udm->dbcon->GetAssoc( $sql )) {
		foreach ($volData as $int_id=>$interest) {
			$fieldname='vol_skill'.$interest['skillid'];
			if ($udm->fields[$fieldname]) {
				$udm->fields[ $fieldname ][ 'value' ] = 1;
			}
		}	
	}

	//Read Availability

    $sql  = "SELECT * FROM vol_relavailability WHERE "; //modinid='";
    $sql .= "personid='" . $uid . "'";
    if ($volData = $udm->dbcon->GetAssoc( $sql )) {
		foreach ($volData as $int_id=>$interest) {
			$fieldname='vol_avail'.$interest['availabilityid'];
			if ($udm->fields[$fieldname]) {
				$udm->fields[ $fieldname ][ 'value' ] = 1;
			}
		}	
	}



}


 
?>
