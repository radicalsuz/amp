<?php

require_once ('Modules/UDM/AMP/save.inc.php');

function udm_volunteer_save (&$udm, $options = null) {
	$vol_fields=_register_plugin_fields($udm, $options);
	if (!$uid) {
		//check for existing records w/ same email
		$uid=$udm->findDuplicates();
	}
	if (udm_amp_save ($udm, $options)) {
		$uid=$udm->uid;
		//ADMIN is allowed to see all fields
		if ( isset( $options['admin'] ) && $options['admin'] ) {

			$submitValues = $udm->form->exportValues();
			foreach ( array_keys( $vol_fields ) as $fname ) {
				if ($cal_fields[$fname]['type']=='date') { $date_items[]=$fname; }
					if ( isset( $submitValues[ $fname ] ) )
						$frmFieldValues[ $fname ] = $submitValues[ $fname ];
			}
		} else {
			//FRONT END SAVE & NON-ADMIN SAVE
			foreach ( $vol_fields as $fname => $fdef ) {
				if ($vol_fields[$fname]['type']=='date') { $date_items[]=$fname; }
				if ($vol_fields[$fname]['type']!='header') {
					if ( isset( $fdef['public'] ) && $fdef['public'] ) {
						$publicFields[] = $fname;
					}
				}
			}

			$frmFieldValues = $udm->form->exportValues( $publicFields );
				
		}

/*	
			foreach ($frmFieldValues as $value_key=>$value_value) {
				print "$value_key : $value_value <BR>";
			}
*/
		//Convert fields & values
		//turn date values into standard strings for insert
		foreach ($date_items as $this_date_item) {
			$formdate_value=$frmFieldValues[$this_date_item];
			if (is_array($formdate_value)) {
				$insertdate_value=
					$formdate_value['Y']."-".
					(strlen($formdate_value['M'])==1?"0":"").$formdate_value['M']."-".
					(strlen($formdate_value['d'])==1?"0":"").$formdate_value['d'];
				$frmFieldValues[$this_date_item]=$insertdate_value;
				#$udm->form->_submitValues[$this_date_item]=$insertdate_value;
				$item=$udm->form->getElement($this_date_item);
				$item->value=$insertdate_value;
			}
		}

		$dbcon = $udm->dbcon;

		if ($uid) {
	// First Delete Previous Sets
			$sql="DELETE from vol_relinterest where personid=$uid";
			$dbcon->execute($sql);
			$sql="DELETE from vol_relskill where personid=$uid";
			$dbcon->execute($sql);
			$sql="DELETE from vol_relavailability where personid=$uid";
			$dbcon->execute($sql);
			
	
	
			//Then insert the new settings
			$sql = "INSERT INTO %s ( %s, personid) VALUES (%s, %s);\n";

			foreach ( $frmFieldValues as $field => $value ) {
				if (substr($field, 0, 9)=="vol_avail"&&$value==1) {
					$action_sql.=sprintf($sql, "vol_relavailability", "availabilityid", substr($field, 9), $uid);
				}
				if (substr($field, 0, 12)=="vol_interest"&&$value==1) {
					$action_sql.=sprintf($sql, "vol_relinterest", "interestid", substr($field, 12), $uid);
				}
				if (substr($field, 0, 9)=="vol_skill"&&$value==1) {
					$action_sql.=sprintf($sql, "vol_relskill", "skillid", substr($field, 9), $uid);
				}
				
				if ($action_sql!='') $rs = $dbcon->Execute( $action_sql ) or
				die( "There was an error completing the volunteer request: " . $dbcon->ErrorMsg());
				$action_sql='';
				
			}	
	
		
			if ( $rs ) {

				$udm->showForm = false;

			} else {

				$udm->errorMessage( "There was an error processing the volunteer request." );

			}

			return true;
		} 
	} else {
		//amp user save failed
		return false;
	}

}




?>