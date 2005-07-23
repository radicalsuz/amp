<?php

require_once ('Modules/Volunteer/Volunteer.inc.php');
require_once ('AMP/UserData/Plugin/Save.inc.php');

class UserDataPlugin_Save_AMPVolunteer extends UserDataPlugin_Save {
    var $name = 'Save Volunteer Data';
    var $description = 'Save skills, interests, and availability data into the AMP database';

    var $options = array();

    var $available = true;
    var $vol; #the Volunteer Object
    var $_field_prefix = "plugin_AMPVolunteer";

    function UserdataPlugin_Save_AMPVolunteer( &$udm ){
        $this->init( $udm );
    }


    function _register_fields_dynamic() {
        #$this->vol=new Volunteer( $this->dbcon );
        #$this->fields+=$this->vol->fields;
    }

    function getSaveFields(){
        $this->vol=new Volunteer( $this->dbcon );
        $save_fields=array();
        $types_to_avoid = array( "header", "static", "html" );
        foreach ($this->vol->fields as $fname=>$fdef) {
            if ( array_search($fdef['type'], $types_to_avoid)!==FALSE) continue; 
            $save_fields[]=$fname;
        }
        return $save_fields;
    }

    function save ( $data ) {
        if ($this->udm->uid) {
            
            $this->dbcon->StartTrans();
	        $this->removeSet ("vol_relavailability");
            $this->addSet ("vol_relavailability", "availabilityid", $this->specificSet($data, "avail"));
            $this->dbcon->CompleteTrans();
            
            $this->dbcon->StartTrans();
            $this->removeSet ("vol_relinterest");
            $this->addSet ("vol_relinterest", "interestid", $this->specificSet($data, "interest"));
            $this->dbcon->CompleteTrans();

            $this->dbcon->StartTrans();
            $this->removeSet ("vol_relskill");
            $this->addSet ("vol_relskill", "skillid", $this->specificSet($data, "skill"));
            $this->dbcon->CompleteTrans();
            
        }
	
	}

    function removeSet($table) {
        $sql="DELETE from $table where personid=".$this->udm->uid;
        $this->dbcon->execute($sql);
        
    }
    
 
    function specificSet($data,$subname) {
        foreach ($data as $key=>$value) {
            if ((!(strpos($key, $subname)===False)) && $value){
                $returnset[substr($key, strlen($subname))]=$value;
            }
        }
        if (!isset($returnset)) $returnset=false;
        return $returnset;
    }
            

    function addSet ($table, $field, $data) {
        $sql = "INSERT INTO %s ( %s, personid) VALUES (%s, %s)\n";
        if (!is_array($data)) return false;

        foreach ($data as $idnum=>$value) {
            $action_sql = sprintf($sql, $table, $field, $idnum, $this->udm->uid);
            $this->dbcon->Execute( $action_sql) or $this->udm->errorMessage("Volunteer plugin save failed:".$this->dbcon->ErrorMsg());
            
        }

        
    }
}	



?>
