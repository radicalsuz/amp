<?php

 /***********************
 * Volunteer plugin for UDM system
 *
 *
 * Author: Austin Putman
 * 02-10-2005
 *
 * Appends a set of checkboxes to the Volunteer input page based on
 * database values for the Volunteer Module
 *
 *****/

require_once( 'HTML/QuickForm.php' );

//udm_QuickForm_volunteer_objects()

function _register_plugin_fields(&$udm, $options=null) {
		$vol_fields=array();
		$vol_fields['vol_id']=array('type'=>'hidden', 'label'=>'', 'required'=>false, 'public'=>true,  'values'=>null, 'size'=>null);
		
		// VOLUNTEER AVAILABILITY FIELDS
		$vol_fields['vol_availability']=array('type'=>'header', 'values'=>'<P>Availability', 'public'=>true);
		$sql="SELECT * from vol_availability order by orderby, id";
		if ($avail_set=$udm->dbcon->GetAssoc($sql)){
			foreach ($avail_set as $avail_id=>$avail) {
				$vol_fields['vol_avail'.$avail_id] = array('type'=>'checkbox', 'label'=>ucwords(str_replace("_", " ", $avail['availability'])), 'required'=>false, 'public'=>true,  'values'=>null);
			}
		}

		// VOLUNTEER INTEREST FIELDS
		$vol_fields['vol_interest_header']=array('type'=>'header', 'values'=>'<P>Select Interests', 'public'=>true);
		$sql="SELECT * from vol_interest order by orderby, id";
		if($interest_set=$udm->dbcon->GetAssoc($sql)) {
			foreach ($interest_set as $int_id=>$interest) {
				$vol_fields['vol_interest'.$int_id] = array('type'=>'checkbox', 'label'=>$interest['interest'], 'required'=>false, 'public'=>true,  'values'=>null);
			}
		}
		
		// VOLUNTEER SKILL FIELDS
		$vol_fields['vol_skills_header']=array('type'=>'header', 'values'=>'<P>Select Skills', 'public'=>true);
		$sql="SELECT * from vol_skill order by orderby, id";
		if($skill_set=$udm->dbcon->GetAssoc($sql)){
			foreach ($skill_set as $skill_id=>$skill) {
				$vol_fields['vol_skill'.$skill_id] = array('type'=>'checkbox', 'label'=>$skill['skill'], 'required'=>false, 'public'=>true,  'values'=>null);
			}
		}

		if (!$udm->fields[key($vol_fields['vol_id'])]){
			if (!isset($udm->fields)) {
				$udm->fields=$vol_fields;
			} else {
				$udm->fields+=$vol_fields;
			}
		}
		
		return $vol_fields;
}		
	
	
?>
