<?php
require_once ('Modules/Calendar/Plugin.inc.php');

class CalendarPlugin_Pager_Output extends CalendarPlugin {

	var $options = array (
        'control_class'=>array(
            'available'=>true,
            'description'=>'CSS class for pagebar controls, user side',
            'default'=>'go',
            'value'=>'go'), 
        'max_qty'=>array (
            'available'=>true,
            'description'=>'Most results allowed on one page from front end',
            'default'=>200,
            'value'=>200 ), 
        'form_name'=>array (
            'available'=>true,
            'description'=>'Name of Pager form',
            'default'=>'calendar_list_pager',
            ) );
	var $criteria;
    var $return_qty;
    var $total_qty;
    var $offset;
    var $stored_dataset;
    var $indexset;
    var $indexname;
    var $available=true;
			
	
	function CalendarPlugin_Pager_Output (&$calendar, $plugin_instance=null) {
        $this->init ($calendar, $plugin_instance);
		$this->read_request();	
	}

	function read_request() {
		if(isset( $_REQUEST['offset']) && is_numeric($_REQUEST['offset'])&&$_REQUEST['offset']) {
			$this->offset=$_REQUEST['offset'];
		} else {
			$this->offset='0';
		}
		if(isset( $_REQUEST['qty']) && is_numeric($_REQUEST['qty'])&&$_REQUEST['qty']) {
			$this->return_qty=$_REQUEST['qty'];
		} else {
			$this->return_qty=$this->options['max_qty']['value'];
		}
		//block frontend users from making large requests
		if (($this->return_qty>$this->options['max_qty']['value']||$this->return_qty=='*')&&(!$this->calendar->admin)) {
			$this->return_qty=$this->options['max_qty']['value'];
		}
	}
	
	function execute($options=null) {
        $output = "";
        if (!isset($options)) $options=$this->getOptions();
        else $options=array_merge($this->getOptions(), $options);

        if ($search_plugin=$this->calendar->getPlugin('AMP','Search')) {
            //if a Search plugin is used, inherit qualities of that plugin
            $this->total_qty=$search_plugin->count_events();
            $this->indexset=$search_plugin->get_index($this->return_qty);
            $this->indexname=$search_plugin->sortby['name'];
        } else {
            //Otherwise slice the current page out of the calendar dataset
            $this->total_qty=count($this->calendar->events);
            $this->stored_dataset=$this->calendar->results();
            if ($this->total_qty>$this->return_qty) {
                $this->calendar->setEvents(array_slice($this->calendar->events, $this->offset, $this->return_qty));
            }
        }
        if (!isset($this->calendar->url_criteria)) $this->criteria = $this->calendar->parse_URL();
        else $this->criteria=$this->calendar->url_criteria;

        if ($this->calendar->admin) $options['control_class']="list_controls";

		$output .="<div class=".$options['control_class']." style=\"width:100%;text-align:center;padding-bottom:5px;padding-top:2px;background-color:#E5E5E5;\">";
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

		if ($this->total_qty>$this->return_qty && isset($this->indexset)) {
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
		$output=" Go:&nbsp;<SELECT name=\"List_offset\" class=\"".$options['control_class']
            ."\" onchange=\"window.location.href='".$_SERVER['PHP_SELF']."?".join("&", $this->criteria)
            ."&qty='+document.forms['".$options['form_name']."'].elements['qty_selector[]'].value + '&offset='+this.value;\">";

		$jumpto_set=$this->indexset;
		$mysort_alias=$this->indexname;
		for ($n=0;$n<count($jumpto_set); $n++) {
			$output.="<option value=\"".($n*$this->return_qty)."\"";
			if ($n==$this->offset) {
				$output.=" selected";
			}
			$output.=">".$mysort_alias.": ";
			$output.=$jumpto_set[$n][$mysort_alias];
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
		if ($this->return_qty<50&&is_numeric($this->return_qty)) {
			$display_options[$this->return_qty]=$this->return_qty;
		}
		$display_options['50']='50';
		$display_options['100']='100';
		$display_options['200']='200';
		if ($this->calendar->admin)  $display_options[$this->total_qty]='All';
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

}


?>
