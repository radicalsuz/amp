<?php

class UserDataPlugin_Search_AMP extends UserDataPlugin {
	var $criteria;
	var $total_qty;
	var $sortby;
    var $options = array (
        'global_criteria'=> array (
            'available'=>true,
            'description'=>'Required criteria in all searches'
            ));
    var $alias = array(
            'Name'=>array(
                'f_alias'=>'Name',
                'f_orderby'=>'Last_Name,First_Name',
                'f_type'=>'text',
                'f_sqlname'=>"Concat(First_Name, ' ', Last_Name)"
             ),
             'Status'=>array(
                'f_alias'=>'Status',
                'f_orderby'=>'publish',
                'f_type'=>'text',
                'f_sqlname'=>'if(publish=1,"Live","Draft")'
              ));
    
    function UserDataPlugin_Search_AMP (&$udm, $plugin_instance=null) {
        
        $this->init ($udm, $plugin_instance);
		if (($this->options['global_criteria']['value'])) { 
            $this->criteria[]=$this->options['global_criteria']['value']; 
        }
		if (!$this->udm->admin) { 
            $this->criteria[]="publish=1"; 
        }
        $this->criteria[]="modin=".$this->udm->instance;
        if (isset($this->udm->plugins['SearchForm'])) { 
            $this->criteria=array_merge($this->criteria, $this->udm->plugins['SearchForm']['Output']->sql_criteria);
        }
	}

    function setSort() {
        $this->sortby=$this->udm->doAction('Sort');
            
    }


	function execute ($options=null) {
		//combine init criteria with passed criteria 
		if(is_array($options['criteria'])) {
			$this->criteria = array_merge($this->criteria, $options['criteria']);
		}
        $options=array_merge($this->getOptions(), $options);
		
		//count total records in search AND get Jump Index
		$this->total_qty=$this->count_items();
		
        //Setup the fieldset for the SQL query
        foreach ($this->alias as $fname=>$fdef) {
            if (isset($fdef['f_sqlname'])) $fieldset.=$fdef['f_sqlname'].(isset($fdef['f_alias'])?" AS ".$fdef['f_alias']:"").", ";
        }
        $fieldset .= "userdata.*";
		
        //get data Records for current page
        if ($dataset=$this->return_items($fieldset, $this->criteria, $this->sortby['orderby'])) {
            $this->udm->setData($dataset);
            return $this->udm;
        } else {
            $this->udm->errorMessage("No items found to match your search");
            return false;
        }
	}

    function count_items ($criteria=null) {		
		//combine init criteria with passed criteria 
		if(is_array($this->criteria)) {
			$criteria = array_merge($this->criteria, $criteria);
		} 

        if ($indexset=$this->get_index($criteria, "id")) {
		    $total_qty=count($indexset);
            return $total_qty;
        } else {
            return false;
        }
            
    }

    //Create an index of records based on the sort criteria
    //or supplied values
    function get_index ($criteria=null, $index_col=null, $orderby=null) {		
        if (!isset($criteria)) $criteria=$this->criteria; 
        if (!isset($this->sortby)) $this->setSort();
        if (!isset($index_col)) $index_col=$this->sortby['select'];
        if (!isset($orderby)) $orderby=$this->sortby['orderby'];
		$index_sql="SELECT ".$index_col." from userdata where ".join(" AND ", $criteria);
        if (isset($orderby)) $index_sql.= " ORDER BY ".$orderby;
        if ($_REQUEST['debug']) print "index:<BR>".$index_sql."<P>";
		if($indexset=&$this->dbcon->CacheGetAll($index_sql))  return $indexset;
        else return false;
    }

    function return_items($fieldset, $criteria, $orderby=null, $return_qty="*", $offset=0) {
		$sql="SELECT $fieldset from userdata where ".join(" AND ", $criteria);
		$sql.=(isset($orderby))?" ORDER BY ".$orderby:"";
        if ($pager=&$this->udm->plugins['Pager']['Output']) {
            $sql.=($pager->return_qty!="*")?" LIMIT ".strval($pager->offset). ", ".strval($pager->return_qty):"";
        } else {
            $sql.=($return_qty!="*")?" LIMIT ".strval($offset). ", ".strval($return_qty):"";
        } 
		if ($_GET['debug']) print $sql;
		if ($dataset=$this->dbcon->CacheGetAll($sql)) {
            return $dataset;
        } else {
            return false;
        }
        
    }
}


?>
