<?php
require_once ('AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Pager_Output extends UserDataPlugin {

	var $options = array (
        'control_class'=>array(
            'available'=>true,
            'label'=>'CSS class for pager controls',
            'type' => 'text',
            'default'=>'sidelist'), 
        'max_qty'=>array (
            'available'=>true,
            'label'=>'Most results allowed on one page from front end',
            'type' => 'text',
            'default'=>200), 
        'show_jumplist'=>array (
            'available'=>true,
            'default'   => 1,
            'label'=>'Show Jump Listing',
            'type' => 'checkbox'),
        'form_name'=>array (
            'available'=>true,
            'label'=>'Name of Pager form',
            'type' => 'text',
            'default'=>'udm_list_pager',
            ) );
	var $criteria = array();
    var $return_qty;
    var $total_qty;
    var $offset;
    var $stored_dataset;
    var $available=true;
    var $_index_set;
			
	
	function UserDataPlugin_Pager_Output (&$udm, $plugin_instance) {
        $this->init ($udm, $plugin_instance);
		$this->read_request();	
	}

    function setOptions( $options ){
        $result = PARENT::setOptions( $options );
        $this->read_request( );
    }

	function read_request() {
        $options=$this->getOptions();
		if(is_numeric($_REQUEST['offset'])&&$_REQUEST['offset']) {
			$this->offset=$_REQUEST['offset'];
		} else {
			$this->offset='0';
		}
		if(is_numeric($_REQUEST['qty'])&&$_REQUEST['qty']) {
			$this->return_qty = $_REQUEST['qty'];
		} else {
			$this->return_qty = $options['max_qty'];
		}
		//block frontend users from making large requests
		if (($this->return_qty>$options['max_qty']||$this->return_qty=='*')&&(!$this->udm->admin)) {
			$this->return_qty=$options['max_qty'];
		}
	}
	
	function execute($options=null) {
        $options=array_merge($this->getOptions(), $options);

        if ($this->udm->total_qty) $this->total_qty = $this->udm->total_qty;
        else $this->total_qty = count($this->udm->users);

        if (!$search_plugin=$this->udm->getPlugins('Search')) {
            //Otherwise slice the current page out of the udm dataset
            $this->total_qty=count($this->udm->users);
            $this->stored_dataset=$this->udm->getData();
            if ($this->total_qty>$this->return_qty) {
                $this->udm->setData(array_slice($this->udm->users, $this->offset, $this->return_qty));
            }
        }

        if (!isset($this->udm->url_criteria)) $this->criteria = $this->udm->parse_URL_crit();
        else $this->criteria=$this->udm->url_criteria;
        
        if ($this->udm->admin) $options['control_class']="list_controls";

		$output ="<div class=".$options['control_class']." style=\"width:100%;text-align:center;padding-bottom:5px;padding-top:2px;background-color:#E5E5E5;\">";
        if (!$this->executed) $output .="<Form name=\"".$options['form_name']."\" ACTION=\"".$_SERVER['PHP_SELF']."\" METHOD=\"GET\">";
		
		#if ($this->return_qty=="*") {$this->return_qty=$this->total_qty;}
		
		
		//Current Location

		$output.="&nbsp;Showing ".$this->offset."-";
		if ($this->total_qty < ($this->offset+$this->return_qty)) {
			$output .= $this->total_qty;
		} else {
			$output .= ($this->offset+$this->return_qty);
		}
		$output.=" of ".$this->total_qty;

		if ($this->total_qty>$this->return_qty) {
			//Go: Jumpto box
			$output.="&nbsp;".$this->jumpto_box($options);
		} 

		//Display Qty choice - convert the qty back to a * for listbox
		#if ($this->total_qty==$this->return_qty) {$this->return_qty="*";}

		if (!$this->executed) $output.="&nbsp;&nbsp;".$this->qty_choice($options);

		
        if ($this->return_qty<$this->total_qty) {


            $output .="</div><div class=".$options['control_class']." style=\"width:100%;text-align:right;padding-bottom:5px;padding-top:2px;background-color:#E5E5E5;\">";
			//PREV button
			if ($this->offset>0) {
				$output .= "&nbsp;<a href=\"javascript: if (document.forms['".$options['form_name']."'].elements['qty_selector[]'].value>=".$this->offset.") { var newoffset=0; } else { var newoffset=(".$this->offset."- document.forms['".$options['form_name']."'].elements['qty_selector[]'].value);}  window.location.href='".$_SERVER['PHP_SELF']."?".join("&", $this->criteria)."&qty='+document.forms['".$options['form_name']."'].elements['qty_selector[]'].value + '&offset='+newoffset;";
                /*
				if ($this->offset>$this->return_qty) {
                    $output .= "' + (".$this->offset."-
					#$output.= $this->offset-$this->return_qty; 
				} else {
					$output.="0";
				}*/
				$output.="\"><B><< Back</B></a>&nbsp;";	
			}
			//NEXT button
			if ($this->total_qty > ($this->offset+$this->return_qty)){
            $output .= "|&nbsp;<a href=\"javascript: window.location.href='".$_SERVER['PHP_SELF']."?".join("&", $this->criteria)."&qty='+document.forms['".$options['form_name']."'].elements['qty_selector[]'].value+'&offset=".($this->offset+$this->return_qty)."';\"><B>Next Page >></B></a>&nbsp; ";
			}
        }

        if (!$this->executed) $output.="</form>";
        $output.="</div>";
        $this->executed=true;
        
		return $output;
	}

////Creates the HTML for the Go Box on Paginated Lists
	function jumpto_box ($options=null) {

		$jumpto_set=$this->get_index();
        if (!$jumpto_set) return false;
        
		$output=" Go:&nbsp;<SELECT name=\"List_offset\" class=\"".$options['control_class']
            ."\" onchange=\"window.location.href='".$_SERVER['PHP_SELF']."?".join("&", $this->criteria)
            ."&qty='+document.forms['".$options['form_name']."'].elements['qty_selector[]'].value + '&offset='+this.value;\">";

		$mysort_alias=$this->udm->sortby['name'];
		for ($n=0;$n<count($jumpto_set); $n=$n+$this->return_qty) {
			$output.="<option value=\"$n\"";
			if ($n==$this->offset) {
				$output.=" selected";
			}
			$output.=">".$mysort_alias.": ";
			$output.=(isset($jumpto_set[$n][$mysort_alias])?$jumpto_set[$n][$mysort_alias]:$jumpto_set[$n][key($jumpto_set[$n])]);
			$output.="</option>";
		}
		$output.="</select>";
		return $output;
	}

	///// CREATES the Display Quantity Select Box
	function qty_choice($options=null) {

        //The Javascript activates a page refresh if the Display All option is
        //selected, or if the current page is in Display All and the user
        //selects a smaller quantity
		$output="Qty:&nbsp;<SELECT name=\"qty_selector[]\" class=".$options['control_class'] 
            ." onchange=\" if (this.value".($this->total_qty==$this->return_qty?"!=":"==").$this->total_qty.") window.location.href='".$_SERVER['PHP_SELF']."?".join("&", $this->criteria)."&qty='+this.value;\">";
        #  onchange=\"window.location.href='".$_SERVER['PHP_SELF']."?".join("&", $this->criteria).(($this->offset!=0)?"&offset=".$this->offset:"")."&qty='+this.value;\">";
        $display_options = array( );
        $default_qtys = array( 50, 100, 200 );
        foreach( $default_qtys as $test_qty ){
            if ( $this->return_qty<$test_qty && is_numeric($this->return_qty) && !isset( $display_options[$this->return_qty])) {
                $display_options[$this->return_qty]=$this->return_qty;
            }
            if ( $test_qty > $options['max_qty']) continue;
            $display_options[strval( $test_qty)] = strval( $test_qty );
        }
		if ($this->udm->admin)  $display_options[$this->total_qty]='All';
		foreach ($display_options as $dvalue => $dtext) {
			$output .= "<option value=\"".$dvalue."\"";
			if ($dvalue==$this->return_qty) {
				$output .= " selected";
			}
			$output .=">".$dtext."</option>";
		}
		
		$output.="</select>";
		return $output;
	}

    //Create an index of records based on the sort criteria
    //or supplied values
    function get_index ($index_col=null, $orderby=null) {		
        $options = $this->getOptions( );
        if ( !( $options['show_jumplist'])) return false;
        if ( isset( $this->_index_set )) return $this->_index_set;
        $criteria=$this->udm->sql_criteria; 
        
        if (!isset($this->udm->sortby)) $this->udm->setSort();
        if (!isset($index_col)) $index_col=$this->udm->sortby['select'];
        if (!isset($orderby)) $orderby=$this->udm->sortby['orderby'];

		$index_sql="SELECT ".$index_col." from userdata ";
        if (is_array($criteria)) $index_sql.="where ".join(" AND ", $criteria);
        if (isset($orderby)) $index_sql.= " ORDER BY ".$orderby;
        
        if (AMP_DISPLAYMODE_DEBUG)  AMP_DebugSQL( $index_sql,  "udm_pager");

		if($indexset=&$this->dbcon->CacheGetAll($index_sql))  {
            $this->_index_set = $indexset;
            return $indexset;
        }
        return false;
    }


}


?>
