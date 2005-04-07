<?php
require_once ('AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Index_Output extends UserDataPlugin {
    var $available=true;
    var $description= "Index by State";

    function UserDataPlugin_Index_Output (&$udm, $plugin_instance=null) {   
        $this->init($udm, $instance);
    }

    function execute ($options=null) {

		$index['state']['name']=$this->udm->name."s By State";
		$index['state']['sql'].="SELECT count(userdata.id) as qty, userdata.State as item_key, states.statename as item_name from userdata, states WHERE userdata.State=states.state and modin=".$_REQUEST['modin']." GROUP BY userdata.State ";
		foreach ($index as $index_key=>$this_index) {
			$index_set=$this->dbcon->CacheGetAll($this_index['sql']);
			$output.='<P><B>'.$this_index['name'].'</B><BR>';
			foreach ($index_set as $index_item) {
				$output.='<a href="'.$_SERVER['PHP_SELF'].'?'.$index_key.'='.$index_item['item_key'].'&modin='.$_REQUEST['modin'].'">'.$index_item['item_name'].'</a> ('.$index_item['qty'].')<BR>';
			}
		}
		return $output;
    }
        
    
    
	
    
}    
    
?>
