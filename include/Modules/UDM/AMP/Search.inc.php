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
            'description'=>'clear preset criteria when search runs'),
        'display_fields' => array(
            'available'=>false,
            'description'=>'fields to include in sql statement',
            'default' => 'userdata.*'),
        'rs_format' => array(
            'available'=>false,
            'description'=>'return data as rs or array',
            'default'=>'CacheGetAll')
        );
    
    function UserDataPlugin_Search_AMP (&$udm, $plugin_instance=null) {
        
        $this->init ($udm, $plugin_instance);
	}

    function initializeCriteria($options=null ) {
        $this->getUDMCrit();
        
        if (isset($options['clear_criteria']) && $options['clear_criteria']) $this->criteria=array();

		if (isset($options['global_criteria'])) { 
            $this->criteria[]=$options['global_criteria']; 
        }

		if (!$this->udm->admin) { 
            $this->criteria[]="publish=1"; 
        }

		if(is_array($options['criteria'])) {
			$this->criteria = array_merge($this->criteria, $options['criteria']);
		}

        $this->udm->setSQLCriteria( $this->criteria );
    }

    function getUDMCrit() {
        if (isset($this->udm->sql_criteria)) { 
            $this->criteria=array_merge($this->criteria, $this->udm->sql_criteria);
        }
    }

    function SQLAliases() {
        if (!isset($this->udm->alias)) return false;
        $fieldset = "";
        foreach ($this->udm->alias as $fname=>$fdef) {
            if (isset($fdef['f_sqlname'])) $fieldset.=", ".$fdef['f_sqlname'].(isset($fdef['f_alias'])?" AS ".$fdef['f_alias']:"");
        }
        return $fieldset;
    }


	function execute ($options=null) {
		//combine init criteria with passed criteria 
        $options=array_merge($this->getOptions(), $options);
        $this->initializeCriteria($options);
		
		//count total records in search
		$this->udm->total_qty = $this->total_qty=$this->count_items();
        if (!isset($this->udm->sortby)) $this->udm->setSort();
		
        //Setup the fieldset for the SQL query
        $fieldset = "userdata.*";
        $fieldset .= $this->SQLAliases();
		
        //get data Records for current page
        if ($dataset=$this->return_items($fieldset, $this->criteria, $this->udm->sortby['orderby'])) {
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
        if ($indexset=$this->dbcon->CacheExecute($index_sql)) {
		    $total_qty=$indexset->Fields("qty");
            return $total_qty;
        } else {
            return false;
        }
            
    }

    function &return_items($fieldset, $criteria, $orderby=null, $return_qty="*", $offset=0) {
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

		if ($dataset=$this->dbcon->CacheExecute($sql)) {
            return $dataset;
        } else {
            return false;
        }
        
    }
}


?>
