<?php

 /***********************
 * Volunteer module Object
 *
 *
 * Author: Austin Putman
 * 02-10-2005
 *
 *
 *****/

class Volunteer {

    
    var $fields;
    var $interests;
    var $skills;
    var $availability;
    var $dbcon;


    function Volunteer (&$dbcon, $uid=null) {
        $this->init($dbcon, $uid);
    }

    function init(&$dbcon, $uid) {
        $this->dbcon=&$dbcon;
        $this->_register_fields();
    }

    function _register_fields(){
        
		$vol_fields=array();
		$dbcon= &$this->dbcon;
		// VOLUNTEER AVAILABILITY FIELDS
		$sql="SELECT * from vol_availability order by orderby, id";
		if ($avail_set=$dbcon->GetAssoc($sql)){
            $vol_fields['vol_availability_header']=array('type'=>'header', 'values'=>'<P>Availability', 'public'=>true, 'enabled'=>true);
			foreach ($avail_set as $avail_id=>$avail) {
				$vol_fields['vol_avail'.$avail_id] = array('type'=>'checkbox', 'label'=>ucwords(str_replace("_", " ", $avail['availability'])), 'required'=>false, 'public'=>true,  'values'=>null, 'enabled'=>true);
			}
		}

		// VOLUNTEER INTEREST FIELDS
		$sql="SELECT * from vol_interest order by orderby, id";
		if($interest_set=$dbcon->GetAssoc($sql)) {
            $vol_fields['vol_interest_header']=array('type'=>'header', 'values'=>'<P>Select Interests', 'public'=>true, 'enabled'=>true);
			foreach ($interest_set as $int_id=>$interest) {
				$vol_fields['vol_interest'.$int_id] = array('type'=>'checkbox', 'label'=>$interest['interest'], 'required'=>false, 'public'=>true,  'values'=>null, 'enabled'=>true);
			}
		}
		
		// VOLUNTEER SKILL FIELDS
		$sql="SELECT * from vol_skill order by orderby, id";
		if($skill_set=$dbcon->GetAssoc($sql)){
            $vol_fields['vol_skills_header']=array('type'=>'header', 'values'=>'<P>Select Skills', 'public'=>true, 'enabled'=>true);
			foreach ($skill_set as $skill_id=>$skill) {
				$vol_fields['vol_skill'.$skill_id] = array('type'=>'checkbox', 'label'=>$skill['skill'], 'required'=>false, 'public'=>true,  'values'=>null, 'enabled'=>true);
			}
		}
    
        $this->fields=$vol_fields;
    }
}		
	
	
?>
