<?php

require_once ('Modules/Volunteer/Volunteer.inc.php');
require_once ('AMP/UserData/Plugin/Save.inc.php');

class UserDataPlugin_Save_Volunteer extends UserDataPlugin_Save {
    var $name = 'Save Volunteer Data';
    var $description = 'Save skills, interests, and availability data into the AMP database';

    var $options = array();

    var $available = true;
    var $vol; #the Volunteer Object

    function UserdataPlugin_Save_Volunteer( &$udm ){
        $this->init( $udm );
    }


    function _register_fields_dynamic() {
        #$this->vol=new Volunteer( $this->dbcon );
        #$this->fields+=$this->vol->fields;
    }

    function getSaveFields(){
        $this->vol=new Volunteer( $this->dbcon );
        foreach ($this->vol->fields as $fname=>$fdef) {
            if ($fdef['type']!="header" && $fdef['type']!='static') {
                $this->save_fields[$fname]=$fdef;
            }
        }
    }

    function save ( $data ) {
        if ($this->udm->uid) {
	        removeSet ("vol_relavailability");
            addSet ("vol_relavailability", "availabilityid", specificSet($data, "vol_avail"));
            removeSet ("vol_relinterest");
            addSet ("vol_relinterest", "interestid", specificSet($data, "vol_interest"));
            removeSet ("vol_relskill");
            addSet ("vol_relskill", "skillid", specificSet($data, "vol_skill"));
        }
	
	}

    function removeSet($table) {
        $sql="DELETE from $table where personid=".$this->udm->uid;
        $this->dbcon->execute($sql);
    }
    
 
    function specificSet($data,$subname) {
        foreach ($data as $key=>$value) {
            if ((!(strpos($key, $subname)===False)) && $value){
                $returnset[substr($key, len($subname))]=$value;
            }
        }
        if (!is_array($returnset)) $returnset=$data;
        return $returnset;
    }
            

    function addSet ($table, $field, $data) {
        $sql = "INSERT INTO %s ( %s, personid) VALUES (%s, %s);\n";
        foreach ($data as $idnum=>$value) {
            $action_sql.=sprintf($sql, $table, $field, $idnum, $this->udm->uid);
            $this->dbcon->Execute( $action_sql);
        }
    }
}	



?>
