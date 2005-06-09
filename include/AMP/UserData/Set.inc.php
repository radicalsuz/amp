<?php

require_once( 'AMP/UserData.php' );

class UserDataSet extends UserData {
	var $set_sql;
	var $results;

    var $users;
    var $users_Recordset;
    
    var $sql_criteria;
    var $url_criteria;
    var $total_qty;
    var $index_set;
    var $sortby;

    var $alias;

    function UserDataSet( &$dbcon, $instance, $admin = false ) {

        $this->UserData( $dbcon, $instance, $admin );
		$this->set_sql=array('where'=>'modin='.$this->instance, 'from'=>'userdata', 'select'=>'*', 'orderby'=>'', 'query'=>'');
    }

    function _register_default_plugins () {

        // No plugins were attached to this module, but we can't very well
        // get along without display functions. Register default
        // Set plugins.

        
        $r = $this->registerPlugin( 'Output', 'SearchForm' ) or $r;
        $r = $this->registerPlugin( 'Output', 'Pager' ) or $r;
        $r = $this->registerPlugin( 'AMP',    'Search' ) or $r;
        $r = $this->registerPlugin( 'Output', 'DisplayHTML' ) or $r;
        if ($this->admin) {
            $r = $this->registerPlugin('Output', 'Actions');
            $r = $this->registerPlugin('Output', 'TableHTML');
        }
        $r = $this->registerPlugin( 'AMP', 'Sort' ) or $r;
        return $r;

    }

    function output_list ( $format='DisplayHTML', $options = null, $order = null, $search_options=null) {

        //block unpublished data from appearing online
        if ((!$this->_module_def['publish'])&&(!$this->admin)) {
            ampredirect("index.php");
            return false;
        }

        //Specify a default output order
        if (!isset($order)) {
            $order = array('SearchForm','Pager','Actions',$format,'Pager','Index');
        }
        if ($this->uid) $order = array($format);

        //get the registered plugins
        $plugin_set = &$this->getPlugins(); 

        // check for any error messages, display them
        if (isset($this->errors)) {
            $output_html = '<P>'.join('<BR>',$this->errors)."<BR>";
            
            //Show only the search form and the index so the user can create a
            //new search
            $order = array('SearchForm','Index');
            
        }
        // check for any result messages, display them
        if ($list_results = $this->getResults()) {
            $output_html = '<P>'.join('<BR>',$list_results)."<BR>";
        }


        //set the header text
        if (method_exists($plugin_set[$format]['Output'], 'header_text_id')) {
            $this->modTemplateID = $plugin_set[$format]['Output']->header_text_id();
        } else {
            $this->modTemplateID = 1;
        }
        //set alias values
        if (method_exists($plugin_set[$format]['Output'], 'setAliases')) {
            $plugin_set[$format]['Output']->setAliases();
        }

        //run the search
        if (!isset($this->dataset)) {
            if ($this->uid) {
                $this->getUser($this->uid);
            } else {
                $this->doAction('Search', $search_options); 
            }
        }
        

        //adjust the order to only include valid actions
        $actions = array_keys($plugin_set);
        $order = array_intersect($order, $actions);

        // render each component into html
        foreach ($order as $output_component) {
            $component = &$plugin_set[$output_component];
            foreach ($component as $namespace=>$plugin) {
                $output_html .= $component[$namespace]->execute($options);
            }
        }

        return $output_html;
    }

    function setData(&$dataset) {
        if (is_object($dataset)) {
            $this->users_Recordset =& $dataset;
            $this->users_Recordset->MoveFirst();
        } else {
            // set array dataset ** now deprecated ** ap
            $this->users=$dataset;
        }
    }


    function getData ( $id = null ) {
        if (isset($this->users_Recordset)) {
            return $this->users_Recordset->GetArray();
        }
        //check for array dataset ** now deprecated ** ap
        if (isset($id)&&is_array($this->users)) {
            foreach ($this->users as $user_def) {
                if ($user_def['id']==$id) return array($user_def);
            }
            return false;
        }
        return $this->users;
    }

    function getData_Recordset( ) {
        if (isset($this->users_Recordset)) {
            return $this->users_Recordset;
        }
        return false;
    }

    /*****
     *
     * getUser ( [ int userid ] )
     *
     * fetches user data for a given userid. If userid is not present,
     * the object should be populated with sufficient data to allow
     * plugins to perform a Query-By-Example.
     *
     * See specific plugin documentation for more information.
     *
     *****/

    function getUser ( $userid = null ) {

        if (!isset($userid)) return false; 
        
        if ($result = $this->getData($userid)) return $result;
        
        $search_options = array (   'criteria'  =>  array( 'value'=>array("id = ".$userid)),
                                    'clear_criteria'    => array('value'=> true) );
        if ($this->doAction( 'Search', $search_options )) {
            return $this->getData();
        }
        return false;

    }

    function getURLCriteria() {
        if (isset($this->url_criteria)) return $this->url_criteria;
        else return $this->parse_URL_crit();
    }

    function parse_URL_crit () {
        parse_str($_SERVER['QUERY_STRING'], $parsed_criteria);
        foreach ($parsed_criteria as $pkey=>$pvalue) {

            if (isset($pvalue)&&($pvalue||$pvalue==='0')) {

                if ($pkey!='offset'&&$pkey!='qty') {
                    $this->url_criteria[]=$pkey.'='.$pvalue;
                }
            }
        }
        return $this->url_criteria;
    }

    function setSQLCriteria( $criteria=null ) {
        $this->sql_criteria = $criteria;
    }
    function setSort() {
        if ($this->sortby=$this->doAction('Sort')) {
            return true;
        } else {
            return ($this->sortby = $this->doPlugin('AMP', 'Sort'));
        }
            
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
