<?php

require_once( 'AMP/UserData.php' );

class UserDataSet extends UserData {
	var $set_sql;
	var $results;
    var $users;

    function UserDataSet( &$dbcon, $instance, $admin = false ) {

        $this->UserData( $dbcon, $instance, $admin );
		$this->set_sql=array('where'=>'modin='.$this->instance, 'from'=>'userdata', 'select'=>'*', 'orderby'=>'', 'query'=>'');
    }

    function _register_default_plugins () {

        // No plugins were attached to this module, but we can't very well
        // get along without data access functions. Register the default
        // AMP plugins.

        #$r = $this->registerPlugin( 'Output', 'UserlistHTML' ) or $r;

    }

    function setData($dataset) {
        $this->users=$dataset;
    }


    function getData () {
        return $this->users;
    }
    //DB functions -- these are irrelevant but will be left in 
    //for backward compatibility till the new plugins
    //are confirmed-working
	function getSet($options) {
		$list_sql=$this->_render_sql();
		if ($_REQUEST['debug']==1) print $list_sql;
		$this->results=$this->dbcon->CacheGetAll($list_sql);
		return (is_array($this->results));
	}
	
	function returnRS() {
		$list_sql=$this->_render_sql();
		return ($this->dbcon->CacheExecute($list_sql));
	}
	
	function _render_sql($save_it=true) {
		$parts=&$this->set_sql;
		$query="SELECT ".$parts['select']." FROM ".$parts['from']." WHERE ".$parts['where']." ORDER BY ".$parts['orderby'];
		if ($save_it) $this->set_sql['query']=$query;
		return $query;
	}

	
	//Function to see whether a list field is enabled and public to the user
	function _check_fields($options) {
		//Name hack again
		$display_fields =str_replace("Concat(First_Name, \" \", Last_Name) as Name,", "Name,", $options['display_fields']);
		
		
		$display_fieldset=split(",", $display_fields);
		foreach ($display_fieldset as $current_field) {
			$current_field=trim($current_field);
			if (isset($this->fields[$current_field])) {
				if (!($this->fields[$current_field]['public']==false&&$this->admin==false)) {
					$return_fieldset[]=$current_field;
				}
			} else {
				switch ($current_field) {
					case "Name":
					if ($this->admin) { $return_fieldset[]="Name";}
						elseif ($this->fields['Last_Name']['public']&&$this->fields['First_Name']['public']) {
							$return_fieldset[]="Name";
						} 
						break;
					case "id":
						if ($this->admin) { $return_fieldset[]="id";}
						break;
					default:
						if (isset($options['Lookups'][$current_field])) {
							$return_fieldset[]=$current_field;
					}
				}
			}
		}
		foreach ($return_fieldset as $key=>$current_field) {
			if ($current_field=='Name') {
				$return_fields.="Concat(First_Name, \" \", Last_Name) as Name, ";
			} else {
				$return_fields.=$current_field.", ";
			}
		}
		$options['display_fields']=substr($return_fields, 0, strlen($return_fields)-2);
		return $options;
	}

}


?>
