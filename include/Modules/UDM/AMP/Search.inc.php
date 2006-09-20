<?php
require_once ('AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Search_AMP extends UserDataPlugin {
	var $criteria = array( );
	var $total_qty;
    var $available=true;
    var $count_criteria;
	var $sortby;
    var $options = array (
        'global_criteria'=> array (
            'available'=>true,
            'type' => 'text',
            'default' => '',
            'label'=>'Required SQL criteria in all searches'
            ),
        'criteria'=> array (
            'available'=>false,
            'label'=>'Passed criteria' ),
        'clear_criteria' => array (
            'available'=>false,
            'label'=>'clear preset criteria when search runs' ),
        'multimodin' => array (
            'available'=>false,
            'default'=>false,
            'label'=>'search across UDM instances' ),
        'display_fields' => array(
            'available'=>false,
            'description'=>'fields to include in sql statement',
            'label' => 'userdata.*')
        );
    
    function UserDataPlugin_Search_AMP (&$udm, $plugin_instance=null) {
        
        $this->init ($udm, $plugin_instance);
	}

    function initializeCriteria($options=array( )) {
        $this->getUDMCrit();
        
        if (isset($options['clear_criteria']) && $options['clear_criteria']) $this->criteria=array();

		if (isset($options['global_criteria']) && $options['global_criteria']) { 
            $new_criteria[]=$options['global_criteria']; 
        }

		if (!$this->udm->admin) { 
            $new_criteria[]="publish=1"; 
        }
		if (!($options['multimodin'])) { 
            $new_criteria[]="modin=".$this->udm->instance; 
        }
		if(isset( $options['criteria']) && is_array($options['criteria'])) {
			$new_criteria = array_merge($new_criteria, $options['criteria']);
		}
        if (isset($new_criteria)) {
            if (is_array($this->criteria) && count($this->criteria)>0) {
                foreach($new_criteria as $crit_item) {
                    if (array_search($crit_item, $this->criteria)===FALSE) {
                        $this->criteria[] = $crit_item;
                    }
                }
            } else {
                $this->criteria = $new_criteria;
            }
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
        if (isset($this->udm->sortby['select']) && $this->udm->sortby['select']) $fieldset .= ", " . $this->udm->sortby['select'];
        return $fieldset;
    }


	function execute ($options = array( )) {
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
        if (is_array($criteria) && !empty( $criteria )) $index_sql.="where ".join(" AND ", $criteria);
        if (AMP_DISPLAYMODE_DEBUG)  AMP_DebugSQL( $index_sql,  "udmcount");
        if ($indexset=$this->dbcon->CacheExecute($index_sql)) {
		    $total_qty=$indexset->Fields("qty");
            return $total_qty;
        } else {
            return false;
        }
            
    }

    function &return_items($fieldset, $criteria, $orderby=null, $return_qty="*", $offset=0) {
        $empty_value = false;
		$sql="SELECT $fieldset from userdata ";
        if (is_array($criteria) && !empty( $criteria )) $sql.="where ".join(" AND ", $criteria);
		$sql.=(isset($orderby))?" ORDER BY ".$orderby:"";
        
        if ($pager = $this->udm->getPlugins('Pager')) {
            $pager = &$pager[key($pager)];
            $sql.=($pager->return_qty!="*")?" LIMIT ".strval($pager->offset). ", ".strval($pager->return_qty):"";
        } else {
            $sql.=($return_qty!="*")?" LIMIT ".strval($offset). ", ".strval($return_qty):"";
        } 

        if (AMP_DISPLAYMODE_DEBUG)  AMP_DebugSQL( $sql,  "udmsearch");

		if ($dataset=$this->dbcon->CacheExecute($sql)) {
            return $dataset;
        } else {
            return $empty_value;
        }
        
    }
}


?>
