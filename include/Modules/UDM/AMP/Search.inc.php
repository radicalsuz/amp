<?php

class UserDataPlugin_Search_AMP extends UserDataPlugin {
	var $criteria;
	var $total_qty;
    var $available=true;
    var $count_criteria;
	var $sortby;
    var $options = array (
        'global_criteria'=> array (
            'available'=>true,
            'description'=>'Required criteria in all searches'
            ),
        'criteria'=> array (
            'available'=>false,
            'description'=>'Passed criteria'),
        'clear_criteria' => array (
            'available'=>false,
            'description'=>'clear preset criteria when search runs')
        );
    var $alias = array(
            'Name'=>array(
                'f_alias'=>'Name',
                'f_orderby'=>'Last_Name,First_Name',
                'f_type'=>'text',
                'f_sqlname'=>"Concat(First_Name, ' ', Last_Name)"
             ),
             'Location'=>array(
                'f_alias'=>'Location',
                'f_sqlname'=>"Concat( if(!isnull(Country), Concat(Country, ' - '),''), if(!isnull(State), Concat(State, ' - '),''), if(!isnull(City), City,''))",
                'f_orderby'=>'(if(Country="USA",1,if(Country="CAN",2,Country))),State,City,Company',
                'f_type'=>'text'),
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
        #$this->criteria[]="modin=".$this->udm->instance;
	}

    function setSort() {
        $this->sortby=$this->udm->doAction('Sort');
            
    }
    function getUDMCrit() {
        if (isset($this->udm->sql_criteria)) { 
            $this->criteria=array_merge($this->criteria, $this->udm->sql_criteria);
        }
    }


	function execute ($options=null) {
        $this->getUDMCrit();
		//combine init criteria with passed criteria 
        $options=array_merge($this->getOptions(), $options);
		if(is_array($options['criteria'])) {
            if ($options['clear_criteria']) $this->criteria=array();
			$this->criteria = array_merge($this->criteria, $options['criteria']);
		}
		
		//count total records in search
		$this->udm->total_qty = $this->total_qty=$this->count_items();
        if (!isset($this->sortby)) $this->setSort();
        $this->udm->index_set = $this->get_index();
		
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
		if(is_array($criteria)) {
			$criteria = array_merge($this->criteria, $criteria);
		} else {
            $criteria = $this->criteria;
        }

		$index_sql="SELECT count(id) as qty from userdata ";
        if (is_array($criteria)) $index_sql.="where ".join(" AND ", $criteria);
        if ($_REQUEST['debug']) print 'count:<BR>'.$index_sql."<BR>";
        if ($indexset=$this->dbcon->Execute($index_sql)) {
		    $total_qty=$indexset->Fields("qty");
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
		$index_sql="SELECT ".$index_col." from userdata ";
        if (is_array($criteria)) $index_sql.="where ".join(" AND ", $criteria);
        if (isset($orderby)) $index_sql.= " ORDER BY ".$orderby;
        if ($_REQUEST['debug']) print "index:<BR>".$index_sql."<P>";
		if($indexset=&$this->dbcon->CacheGetAll($index_sql))  return $indexset;
        else return false;
    }

    function return_items($fieldset, $criteria, $orderby=null, $return_qty="*", $offset=0) {
		$sql="SELECT $fieldset from userdata ";
        if (is_array($criteria)) $sql.="where ".join(" AND ", $criteria);
		$sql.=(isset($orderby))?" ORDER BY ".$orderby:"";
        if ($pager=&$this->udm->getPlugins('Pager')) {
            $pager = &$pager[key($pager)];
            $sql.=($pager->return_qty!="*")?" LIMIT ".strval($pager->offset). ", ".strval($pager->return_qty):"";
        } else {
            $sql.=($return_qty!="*")?" LIMIT ".strval($offset). ", ".strval($return_qty):"";
        } 
		if ($_GET['debug']) print $sql."<BR>";
		if ($dataset=$this->dbcon->CacheGetAll($sql)) {
            return $dataset;
        } else {
            return false;
        }
        
    }
}


?>
