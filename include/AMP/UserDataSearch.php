<?php
/*****
 *
 * AMP UserData Search Object
 *
 * (c) 2004 Radical Designs
 * 
 *****/



class UserList {
	var $action_fields="modin";
	var $display_fields="id, Concat(First_Name, \" \", Last_Name) as Name, Company, State, Phone, modin";
	var $current_fieldset;
	var $search_criteria;
	var $page_name="modinput4_search.php";
	var $search_tables="userdata";
	var $search_logic;
	var $is_parent;
	var $is_included;
	var $search_hier;
	var $default_qty=50;
	var $current_offset;
	var $qty_displayed;
	var $include_modin;
	var $base_modin;
	var $sort_by="Last_Name, First_Name";
	var $current_sql;
	var $current_list;
	var $search_count;
	var $current_list_action;
	var $udm;

	function list_isActionField($field) {
		if ($this->action_fields==$field) {
			$this->current_list_action=$field;
			return $field;
		}
		if ($action_fieldset=explode(",", $this->action_fields)){
			$this->current_list_action= array_search($field, $action_fieldset);
			return $this->current_list_action;
		}
		else return FALSE;
	}
	function showlist($action_field, $link_action, $allow_edit=FALSE){
		$default_modin=1;
		$list_row_start='<tr bordercolor="#333333" bgcolor="#CCCCCC" class="results">';
		$list_row_end="</tr>\n";
		$list_item_start="<td>";
		$list_item_end="</td>";
		//begin Row output loop
		if ($this->current_list!=NULL) {
		    
			for ($n=$this->current_offset; ($n<($this->current_offset+$this->qty_displayed)&&$n<count($this->current_list)); $n++){
				$current_row=$this->current_list[$n];
				$list_row="";
				foreach($this->current_fieldset as $current_field) {
				$showfield=TRUE;
					//Check if this is an action field
					
					if (substr($current_field, 0, 5)=="sort_") { $showfield=FALSE; }
					if ($current_action=$this->list_isActionField($current_field)) {
						
							if ($allow_edit){ //edit offered to admin users
								$list_row.=$list_item_start."<a href=\"$link_action?".$current_action."=".$current_row[$current_action]."&uid=".$current_row[$action_field]."\">edit</a>".$list_item_end;
							} else {
								$showfield=FALSE;
							}
					} else {
						if ($showfield)  $list_row.=$list_item_start.$current_row[$current_field].$list_item_end;
						else $list_row.=$list_item_start.$list_item_end;
					}
				}

				$list_row=$list_row_start.$list_row.$list_row_end;
				//append row to html var
				$list_html.=$list_row;
			
			}
		} else { //No Records Found
			$list_html=$list_row_start."<td>No Records Found</td>".$list_row_end;
	
		}	
		return $list_html;	
	}

	function display_qty_choice() {
		$output="<SELECT name=\"page_qty\">";
		$display_options['50']='50';
		$display_options['100']='100';
		$display_options['*']='All';
		foreach ($display_options as $dvalue => $dtext) {
			$output .= "<option value=\"".$dvalue."\"";
			if ($dvalue==$this->qty_displayed) {
				$output .= " selected";
			}
			$output .=">".$dtext."</option>";
		}
		
		$output.="</select>";
		return $output;
	}

	function paged_list_header () {
		$output ="<span class=side>";
		if ($this->current_offset>0) {
			$output .= "&nbsp;<a href=\"#\" onclick=\"sform.elements['offset'].value='";
			if ($this->current_offset>$this->qty_displayed) {
				$output.= $this->current_offset-$this->qty_displayed; 
			} else {
				$output.="0";
			}
			$output.="'; sform.submit();\"><< Prev </a>";
		}
		if (count($this->current_list) > ($this->current_offset+$this->qty_displayed)){
			$output .= "&nbsp;&nbsp;<a href=\"#\" onclick=\"sform.elements['offset'].value=".($this->current_offset+$this->qty_displayed)."; sform.submit();\">Next >></a>";
		}
		$output.="&nbsp;&nbsp;Displaying ".$this->current_offset."-";
		if (count($this->current_list) < ($this->current_offset+$this->qty_displayed)) {
			$output .= count($this->current_list);
		} else {
			$output .= ($this->current_offset+$this->qty_displayed);
		}
		
		$output.="&nbsp;".$this->jumpto_box();
		$output.="</span>";
		return $output;
	}
	
	function jumpto_box () {
		$output="&nbsp;Go: <SELECT name=\"UDM_offset\" class=side onchange=\"sform.elements['offset'].value=this.value; sform.submit();\">";
		$sortfields = explode(",", $this->sort_by);
		$sortfields[0]=str_replace(" DESC", " ", $sortfields[0]);
		$sortfields[0]=str_replace(" ASC", " ", $sortfields[0]);
		$mysort=trim($sortfields[0]);
		for ($n=0;$n<=count($this->current_list); $n=$n+$this->qty_displayed) {
			$output.="<option value=\"$n\"";
			if ($n==$this->current_offset) {
				$output.=" selected";
			}
			$output.=">".$this->translateFields($mysort, $this->udm).": ".$this->current_list[$n]["sort_".$mysort]."</option>";
		}
		$output.="</select>";
		return $output;
	}

	function output_list ($link_action="modinput4_view.php") {
		global $userper, $standalone;
		$list_html_start.='<table cellpadding="1" cellspacing="1" width="95%">';
		//Check if pagination header is needed

		
		$list_html_start.='<tr class="toplinks">';
		if ($this->current_list!=NULL) {
			//DISPLAY COLUMN HEADERS
			foreach($this->current_fieldset as $current_field) {
				if (substr($current_field, 0, 5)!="sort_") { //hide sort fields
					$list_html_start.="<td align=\"left\">";
					if (!$this->list_isActionField($current_field)){
						if ($current_field=="Name") { //hack for name field
							$list_html_start.="<b><a href=\"#\" onclick=\"sform.elements['UDM_sort'].value = 'Last_Name, First_Name, '+sform.elements['UDM_sort'].value; sform.submit();\">".$this->translateFields($current_field, $this->udm)."</a></b>";
						} else {
							$list_html_start.="<b><a href=\"#\" onclick=\"sform.elements['UDM_sort'].value = '$current_field, '+sform.elements['UDM_sort'].value; sform.submit();\">".$this->translateFields($current_field, $this->udm)."</a></b>";
						}
					}
					$list_html_start.="</td>";
				}
			}
			#$list_html_start.="<td><!--editlink column--></td>";
			$list_html_start.=$list_row_end;
			$allow_edit=($userper[87] == 1 || $standalone == 1);
			$list_html=$this->showlist("id", $link_action, $allow_edit);			
		}
		$list_html_footer = "</table>";
		//INSERT PAGINATION
		if (count($this->current_list)>$this->qty_displayed) {
			$list_html_start=$this->paged_list_header().$list_html_start;
			$list_html_footer.=$this->paged_list_header();
		}


		return $list_html_start.$list_html.$list_html_footer;
	}

	function set_offset($offset) {
		$this->current_offset=$offset;
	}


	function tab_navs() {
		$page_tabs="Results,Refine Search";
		//Create tabbed div wrapper
		$tabnav_html='<ul id="topnav">';
		$page_tabset = explode(",", $page_tabs);
		foreach ($page_tabset as $tabname){
			$pagecount++;
			$tabnav_html.="<li class=\"tab$pagecount\"><a href=\"#\" id=\"a$pagecount\" onclick=\"change('tabpage_$tabname', 'tabpage');\" >$tabname</a></li>";
		}
		$tabnav_html.="</ul>";
		return $tabnav_html;
	}
	
	function readSearch($udm) {
		global $_REQUEST;
		$searchset_count=0;
		$searchitems_count=1;
		$this->udm=$udm;
		$modin=$udm->instance;
		//Find modules specified for Search
		$this->base_modin=$modin;
		if($_REQUEST['UDM_Search_all_mods']) {//user checked 'Search in all' box
			$this->include_modin="*";
		} elseif(isset($_REQUEST['UDM_Search_modin'])) {
			$mod_set=$_REQUEST['UDM_Search_modin'];
			if (is_array($mod_set)){
				foreach ($mod_set as $this_mod) {
					if ($this_mod!=$modin) { $this->addModule($this_mod);}
				} 
			}
		}


		//Check for old searches stored on form
		if (isset($_REQUEST['UDM_Prev_Search_action'])) {
			if ($_REQUEST['UDM_Prev_Search_action']!='0') {
				while ($field_choice=$_REQUEST['UDM_Prev_Search_field'.$searchitems_count]) {
					$op_choice=$_REQUEST['UDM_Prev_Search_compare'.$searchitems_count];
					$value_choice=$_REQUEST['UDM_Prev_Search_value'.$searchitems_count];
					$set_choice=$_REQUEST['UDM_Prev_Search_set'.$searchitems_count];
					$this->addCriteria($field_choice, $op_choice, $value_choice, $set_choice);
					if ($set_choice>$searchset_count) { $searchset_count=$set_choice; }
					$searchitems_count++;
				}
				for ($n=0; $n<=$searchset_count; $n++) {
					$this->setLogic($_REQUEST['UDM_Prev_Search_set_logic_int'.$n], $n, 'internal');
					$this->setLogic($_REQUEST['UDM_Prev_Search_set_logic_ext'.$n], $n, 'external');
				}
				$this->setLogic($_REQUEST['UDM_Prev_Search_action'], $searchset_count, 'external');
				$this->setLogic($_REQUEST['UDM_Prev_Search_action'], $searchset_count+1, 'external');
				$searchitems_count=1;
				$searchset_count++;
			} 
		}  

		//LOAD NEW SEARCH criteria
		while (isset($_REQUEST['choose_field'.$searchitems_count])){

				$field_choice=$_REQUEST['choose_field'.$searchitems_count];
				$op_choice=$_REQUEST['choose_comparison'.$searchitems_count];
				#$logic_choice=$_REQUEST['choose_logic'.$searchitems_count];
				$value_choice=$_REQUEST['value_field'.$searchitems_count];
				if (($value_choice!=NULL && $value_choice!='')||(substr($op_choice, strlen($op_choice)-5)== "EMPTY")||(substr($op_choice, strlen($op_choice)-4)== "TRUE")) {
					$this->addCriteria($field_choice, $op_choice, $value_choice, $searchset_count);
				}
				$searchitems_count++;


		}

		#$this->search_count=$searchset_count;
		$this->setLogic($_REQUEST['choose_logic'], $searchset_count, 'internal');
	
		//set page offset and display qty
		if (isset($_REQUEST['page_qty'])) {
			$this->qty_displayed=$_REQUEST['page_qty'];
		} else {
			$this->qty_displayed=$this->default_qty;
		}
		if (isset($_REQUEST['offset'])) {
			$this->current_offset=$_REQUEST['offset'];
		} else {
			$this->current_offset=0;
		}
	
		//set fields for search list
		if (isset($_REQUEST['UDM_display_fields'])) {
			$this->display_fields=stripslashes($_REQUEST['UDM_display_fields']);
		}
		if (isset($_REQUEST['UDM_list_fields'])) {
			$list_field_list="id, Concat(First_Name, \" \", Last_Name) as Name";
			foreach ($_REQUEST['UDM_list_fields'] as $current_field) {
				$list_field_list.=", ".$current_field;
			}
			$list_field_list.=", ".$this->action_fields;
			$this->display_fields=$list_field_list;
		}

		//set sort
		if (isset($_REQUEST['UDM_sort'])) {
			print $_REQUEST['UDM_sort'];
			$sort_set=explode(",", $_REQUEST['UDM_sort']);
			$this->sort_by="";
			foreach ($sort_set as $this_sort) {
				$this_sort=str_replace(" DESC", " " , $this_sort);
				$this_sort=str_replace(" ASC", " ", $this_sort);
				$this_sort=trim($this_sort);
				if (strpos($this->sort_by, $this_sort)===FALSE) {
					if (substr_count($_REQUEST['UDM_sort'], $this_sort)>1&&strpos($_REQUEST['UDM_sort'], $this_sort." DESC") === FALSE) {
						$this->sort_by.=$this_sort." DESC, ";
					} else {
						$this->sort_by.=$this_sort.", ";
					}
				}
				#print $this_sort."#<BR>";
			}
				
			$this->sort_by=substr($this->sort_by, 0, strlen($this->sort_by)-2);
			
		}


	}

	function connect_SearchOperators() {
		$compare_operators=array();
		$compare_operators['contains']="LIKE";
		$compare_operators['does not contain']="!LIKE";
		$compare_operators['starts with']="_LIKE";
		$compare_operators['ends with']="LIKE_";
		$compare_operators['equals']="=";
		$compare_operators['does not equal']="!=";
		$compare_operators['is Empty']="EMPTY";
		$compare_operators['not Empty']="NOT EMPTY";
		$compare_operators['is True']="TRUE";
		$compare_operators['is False']="NOT TRUE";
		$compare_operators['greater than']=">";
		$compare_operators['>=']=">=";
		$compare_operators['less than']="<";
		$compare_operators['<=']="<=";
		return $compare_operators;
	}


	
	function translateLogic($current_set) {
		if ($this->search_logic[$current_set]['internal']=="OR") {
			$returnLogic="any";
		} else {
			$returnLogic="all";
		}
		return $returnLogic;

	}

	function translateCompare($compare_term) {
		$compare_options=$this->connect_SearchOperators();
		return array_search($compare_term, $compare_options);
	}

	function translateFields($fieldname, $udm) {
		$returnField=strip_tags($udm->fields[$fieldname]['label']);
		if ($returnField==NULL) {$returnField=$fieldname;}
		return $returnField;
	}


	function translateMods($udm) {
		$output = "Searched ";
		if($mymodules=$this->GetModsArray($udm)){
			if ($this->include_modin=="*") {
				$output.= "all modules";
			} elseif (!isset($this->include_modin)) {
				$output.= $udm->name;
			} else {
				$current_mods=explode(",", $this->include_modin);
				foreach ($current_mods as $this_mod) {
					$output.=$mymodules[$this_mod].", ";
				}
				$output.=$udm->name;
			}
		
		} else {
			$output="No Modules Defined";
		}
		return ($output."<BR>");

	}
	
	function translateSearch($udm) {
		$output = "<div id=\"div_search_details\" style=\"display: none\">";
		$output .= "Searched for ";
		for ($current_set=0; $current_set<=$this->search_count; $current_set++) {
			if (count($this->search_criteria[$current_set])>1) {
				$output.=$this->translateLogic($current_set)." of these:<BR>";
				$startline="<li>";
				$endline="</LI>";
				$endlist="";
			}	 else {
				$startline="";
				$endline="<BR>";
				$endlist="";
			}
			foreach($this->search_criteria[$current_set] as $fieldname => $searchdef) {
				$output.=$startline."<b>".$this->translateFields($fieldname, $udm)." ".$this->translateCompare($searchdef['operator'])." ".$searchdef['value']."</b>";
				if (isset($this->include_modin)&&(substr($fieldname,0,6)=="custom")) { $output.= " in ".$udm->name." only";}
				$output.=$endline;
			}
			$output.=$endlist;
			if ($this->search_count>$current_set) {
				switch ($this->search_logic[$current_set]['external']) {
					case "AND":	$output.="Narrowed to records where:<BR>";break;
					case "OR": $output.="Expanded to also include records where:<BR>";break;
				}

			}

		}
		$output.="</div>";
		$output.=$this->translateMods($udm);
		$output.="Your search returned ".count($this->current_list)." results &nbsp;&nbsp;<a href=\"#\" onclick=\"change('div_search_details')\">details</a><BR>";
		return $output;
	}
			





	//JAVASCRIPT FOR ADDING AND SUBTRACTING ROWS OF SEARCH CRITERIA
	//There must be a better place to put this

	function searchForm_Jscript() {
		
		$script = "<script type=\"text/javascript\">\r\n
	var SearchLines=new Array(); //Holds pointers to search criteria form elements
	var searchitems=1;  //the number of search lines being displayed on the form
	var sform=document.forms['UDM_Advanced_Search'];
	var searchtable=document.getElementById('UDM_Search_tbl');
		
	
//This is A Javascript Function
//to create a new row of search criteria
	function AddItem() { 
		sform=document.forms['UDM_Advanced_Search'];
		searchtable=document.getElementById('UDM_Search_tbl');

		searchitems++; 
		
		var newfieldbox=SetupSelect('field');
		//var newlogicbox=SetupSelect('logic');
		var newcomparebox=SetupSelect('comparison');
		var newvaluebox=document.createElement('input');
		newvaluebox.name='value_field'+searchitems;
		newvaluebox.type='text';
		newvaluebox.size='20';
		var newaddbtn=document.createElement('input');
		newaddbtn.type='button';
		newaddbtn.value='+';
		event(newaddbtn, 'onclick', 'AddItem();');
		var newrmvbtn=document.createElement('input');
		newrmvbtn.type='button';
		newrmvbtn.value='-';
		event(newrmvbtn, 'onclick', ('RemoveItem('+searchitems+');'));
		var newrow=searchtable.tBodies[0].appendChild(document.createElement('tr'));
		var newcell=document.createElement('td');
		newcell.appendChild(newfieldbox);
		newrow.appendChild(newcell);
		newcell=document.createElement('td');
		newcell.appendChild(newcomparebox);
		newrow.appendChild(newcell);
		newcell=document.createElement('td');
		newcell.appendChild(newvaluebox);
		newrow.appendChild(newcell);
		newcell=document.createElement('td');
		newcell.appendChild(newaddbtn);
		newrow.appendChild(newcell);
		newcell=document.createElement('td');
		newcell.appendChild(newrmvbtn);
		newrow.appendChild(newcell);
		
		SearchLines[searchitems]=new Array();
		SearchLines[searchitems][0]=newfieldbox;
		SearchLines[searchitems][1]=newcomparebox;
		SearchLines[searchitems][2]=newvaluebox;
		//SearchLines[searchitems][3]=newlogicbox;

		
		
	} 

	function SaveRow (rowindex) { //This is A Javascript Function
		//won't work in IE for rows generated by script
		//used to commit the first row, which serves as a template
		sform=document.forms['UDM_Advanced_Search'];
		SearchLines[rowindex]=new Array();
		SearchLines[rowindex][0]=sform.elements['choose_field'+rowindex];
		SearchLines[rowindex][1]=sform.elements['choose_comparison'+rowindex];
		SearchLines[rowindex][2]=sform.elements['value_field'+rowindex];
		//SearchLines[rowindex][3]=sform.elements['choose_logic'+rowindex];
	}

	function RemoveItem(which) { //This is A Javascript Function
		if (searchitems>1) {
			for (n=which; n<searchitems; n++) {
				MoveRow(n+1, n);
			}
			searchitems=searchitems-1;
			searchtable.deleteRow(searchitems);
		} else {
			sform.elements['choose_field1'].selectedIndex=0;
			sform.elements['choose_comparison1'].selectedIndex=0;
			sform.elements['value_field1'].value = '';
			sform.elements['choose_field1'].focus;
		}
	} 

//This is A Javascript Function
//Which Moves values *from* one set of select boxes *to* another

	function MoveRow (from,to) {
		//if (to==1) { //First field not stored in array
		//	sform.elements['choose_field1'].selectedIndex=SearchLines[from][0].selectedIndex;
		//	sform.elements['choose_comparison1'].selectedIndex=SearchLines[from][1].selectedIndex;
		//	sform.elements['choose_logic1'].selectedIndex=SearchLines[from][3].selectedIndex;
		//	sform.elements['value_field1'].value = SearchLines[from][2].value;
		//} else { //retrieve values using array data
			SearchLines[to][0].selectedIndex=SearchLines[from][0].selectedIndex;
			SearchLines[to][1].selectedIndex=SearchLines[from][1].selectedIndex;
			//SearchLines[to][3].selectedIndex=SearchLines[from][3].selectedIndex;
			
			SearchLines[to][2].value = SearchLines[from][2].value;
		//}

	}	

	//This is A Javascript Function
	//To Ensure IE Compliance for assigning onClick action
	function event(elem,handler,funct) {//This is A Javascript Function
		if(document.all) {
			elem[handler] = new Function(funct);
		} else {
			elem.setAttribute(handler,funct);
		}
	}

//This is A Javascript Function
//To Create a Selectbox by copying the options from an Existing Select

	function SetupSelect(selecttype) {
		var newselect=document.createElement('select');
		var selbox = SearchLines[1][convertSelectType(selecttype)];
		//var selbox = sform.elements['choose_'+selecttype+'1'];
		newselect.name='choose_'+selecttype+searchitems;
		for (n=0; n<selbox.options.length; n++) {
			newselect.options[n] = new Option(selbox.options[n].text, selbox.options[n].value);
		}
		
		return(newselect);
	}

	//this is a javascript function
	//for putting select boxes into an array
	function convertSelectType(selecttype) {
		var numindex;
		switch (selecttype) {
			case('logic'): numindex=3; break;
			case('field'):numindex=0; break;
			case('comparison'): numindex=1;break;
			case('value'):numindex=2;break;
		}
		return numindex;
	}

//This is A Javascript Function
//which assesses the entries on the search form before the form submits

	function goSearch() {
		var searchflag=false;
		for (n=1;n<=searchitems;n++){
				if ( (SearchLines[n][2].value != '')|| (SearchLines[n][1].value=='EMPTY')||(SearchLines[n][1].value=='NOT EMPTY')||(SearchLines[n][1].value=='TRUE')||(SearchLines[n][1].value=='NOT TRUE')) {
					searchflag=true;
				}
				if (sform.elements['UDM_Prev_Search_field1']) {
					searchflag=true;
				}
		}
		if (searchflag) {
			sform.elements['UDM_search_items'].value=searchitems;
			sform.submit();
		} else {
			alert ('No search criteria have been selected');
		}
	}

	</script>";
	return $script;
	}
	
	//BACK TO PHP
	//Stores the previous search on the form
	function storeSearch() {
		$fieldcount=0;
		if (count($this->search_criteria)>0) {
		for ($n=0; $n<=$this->search_count;$n++) {
			foreach ($this->search_criteria[$n] as $fieldname => $fdef) {
				$fieldcount++;
				$output.="<input type='hidden' name=\"UDM_Prev_Search_field$fieldcount\"	  value=\"$fieldname\">";
				$output.="<input type='hidden' name=\"UDM_Prev_Search_compare$fieldcount\" value=\"".$fdef['operator']."\">";
				$output.="<input type='hidden' name=\"UDM_Prev_Search_value$fieldcount\" value=\"".$fdef['value']."\">";
				$output.="<input type='hidden' name=\"UDM_Prev_Search_set$fieldcount\" value=\"".$n."\">";
			}	
			$output.="<input type='hidden' name=\"UDM_Prev_Search_set_logic_int$n\" value=\"".$this->search_logic[$n]['internal']."\">";
			$output.="<input type='hidden' name=\"UDM_Prev_Search_set_logic_ext$n\" value=\"".$this->search_logic[$n]['external']."\">";
		}
		$output.="<select name=\"UDM_Prev_Search_action\"><option value='AND' selected>Refine the current search using the criteria below</option><option value='OR'>Expand the current search to include results which match:</option><option value='0'>Start a new search</option></select><br>";
		}
		return $output;
	}



	function SearchForm($udm, $action="modinput4_search.php") {
		foreach ($udm->fields as $fieldname => $fdef) {
			$thislabel=strip_tags($fdef['label']);
			if (strlen($thislabel)>25) {
					$thislabel=substr($thislabel, 0, 25);
			} 
			$fieldselect.="<option value=\"$fieldname\">".$thislabel."</option>";
		}
		
		$compare_operators=$this->connect_SearchOperators();
		
		$logic_set['all']="AND";
		$logic_set['any']="OR";
		
		$name_set=array_keys($compare_operators);
		foreach($name_set as $compare_value) {
			$compare_select.="<option value=\"".$compare_operators[$compare_value]."\">$compare_value</option>";
		}

		$name_set=array_keys($logic_set);
		foreach($name_set as $logic_value) {
			$logic_select.="<option value=\"".$logic_set[$logic_value]."\">$logic_value</option>";
		}


		$new_row="<tr><td><select name = 'choose_field%1\$s' width='25'>".$fieldselect."</select>&nbsp;</td><td><select name='choose_comparison%1\$s' width='15'>".$compare_select."</select>&nbsp;</td><td><input name='value_field%1\$s' type='text' size='20'>&nbsp;</td><td><input name='add_criteria%1\$s' type='button' value='+'  onclick='AddItem();'>&nbsp;</td><td><input name='remove_criteria%1\$s' type='button' value='-'  onclick='RemoveItem(%1\$s);'></td></tr>";

		$search_button_go="<input name=\"btnUdmSubmit\" value=\"Search\" type=\"button\" onclick=\"goSearch();\" id=\"UDM_Search_btn\">";
		$hidden_values="<input name=\"UDM_search_items\" type=\"hidden\" value=\"1\"><input name=\"modin\" value=\"".$udm->instance."\" type=\"hidden\"><input name=\"offset\" value=\"0\" type=\"hidden\"><input name=\"UDM_display_fields\" value='".$this->display_fields."' type=\"hidden\"><input name=\"UDM_sort\" value='".$this->sort_by."' type=\"hidden\">";
		$control_script= $this->SearchForm_Jscript();
		$logic_box='<div style ="background-color:E3E3E3; min-height=50px; vertical-align:center; text-align:left; padding: 5px;">'.$this->storeSearch().'Find results which match <select name="choose_logic">'.$logic_select.'</select> of the following:</div>';
		$form_header=$control_script."	<form name=\"UDM_Advanced_Search\" action=\"$action\" method=\"POST\">$hidden_values $logic_box<table id=\"UDM_Search_tbl\">";
		
		if (isset($this->include_modin)) {$modlist_style="block"; } else {$modlist_style="none";}
		if ($this->include_modin=="*") {$is_selected=" CHECKED"; $modlist_style="none";}

		$modinbox="<div id=\"UDM_search_modules\" style=\"display: $modlist_style;\">Currently searching <B>".$udm->name."</b><BR>Also include:<BR><SELECT MULTIPLE name=\"UDM_Search_modin[]\" size=\"6\">".$this->GetMods($udm)."</select></DIV><BR><input name=\"UDM_Search_all_mods\" type=\"checkbox\" value=\"1\" $is_selected>Search users from all sources&nbsp;&nbsp;<a href=\"#\" onclick=\"change('UDM_search_modules')\">show list</a>";
		
		$fieldselect_no_custom=substr($fieldselect, 0, strpos($fieldselect, "<option value=\"custom"));
		$display_fields_box="<a href=\"#\" onclick=\"change('UDM_search_listfields');\">Choose fields for list</a><BR><div id=\"UDM_search_listfields\" style=\"display: none;\">Name will show in list by default - please select other fields to include:<BR><select multiple name=\"UDM_list_fields[]\">$fieldselect_no_custom</select></div>";
		$controls_html="<span>".sprintf($new_row, '1')."</span><BR>";
		$controls_html.="<script type=\"text/javascript\">SaveRow(1);</script>";
		$form_footer="</table><center>".$search_button_go."<P>".$modinbox."<BR>Display ".$this->display_qty_choice()." matches per page of results<BR>".$display_fields_box."</center></form>";

		$form_html= $form_header.$controls_html.$form_footer;
		return $form_html;
		
	}	

	//Make an option list of modules
	function GetMods($udm) {
		if($modlist=$udm->dbcon->Execute("SELECT id, name from userdata_fields")) {
			while (!$modlist->EOF) {
				$output.="<option value=\"".$modlist->Fields("id")."\"";
				if ($this->isModule($modlist->Fields("id"))) {$output.=" selected";}
				$output.=">".$modlist->Fields("name")."</option>";
				$modlist->MoveNext();
			}
		} else {
			$output = "<option value=\"\">No Modules Defined</option>";
		}
		return $output;
	}

	//Make an array list of modules
	function GetModsArray($udm) {
		return $udm->dbcon->GetAssoc("SELECT id, name from userdata_fields");
	}
	
	
	function addModule ($modin) {
		$current_modules=explode(",", $this->include_modin);
		if (!isset($this->base_modin)) {
			$this->base_modin=$modin;
			} elseif (array_search($modin, $current_modules)===FALSE&& $modin!=$this->base_modin) {
			if (isset($this->include_modin)) {
				$this->include_modin.=",".$modin;
			} else {$this->include_modin=$modin;}
		}
		
	}

	function isModule($modin) {
		if (!strpos($this->include_modin, ",")===FALSE) {
			$current_modules=explode(",", $this->include_modin);
			$result = array_search($modin, $current_modules);
			if($result===FALSE) {
				$output=FALSE;
			} else { $output=TRUE; }
		} else {
			if ($modin==$this->include_modin){
				$output=TRUE;
			}else { $output=FALSE;}
		}
		if ($modin==$this->base_modin) { $output=TRUE; }
		return $output;
	}

	function addCriteria ($field, $operator, $value, $search_set=0, $logic="AND") {
		#$search_set=$this->setSearchParent($search_set);
		if ($search_set>$this->search_count) {$this->search_count=$search_set;}
		$this->search_criteria[$search_set][$field]['operator']=$operator;
		$this->search_criteria[$search_set][$field]['logic']=$logic;
		$this->search_criteria[$search_set][$field]['value']=$value;
		//echo "Added Criteria in Search[$search_set]:$field$operator$value";
		if ($this->search_count<$search_set||!isset($this->search_count)) { $this->search_count=$search_set;}
	}

	function setLogic ($logic, $search_set=0, $target=NULL) {
		if (!isset($target)) {
			$this->search_logic[$search_set]['internal']=$logic;
			$this->search_logic[$search_set]['external']=$logic;
		} else {
			$this->search_logic[$search_set][$target]=$logic;
		}
	}

	function setSearchParent($parent_search, $child_set) {
		$this->is_parent[$parent_search]=$child_set;
	}


	function setup_subSearch ($dbcon, $current_set, $avoid_custom=FALSE) {
			$this->is_included[$current_set]=TRUE;
			//echo "starting subsearch $current_set<BR>";
			//ADD EACH CRITERIA  WITHIN SET
			$working_fieldset=array_keys($this->search_criteria[$current_set]);
			foreach ($working_fieldset as $currentField) {
				//Check for custom fields in multi-module searches
				if (!(substr($currentField,0,6)=="custom" && $avoid_custom)) { 
					//Convert special operators to sql and add quotes to values
					$op_prepped=$this->search_criteria[$current_set][$currentField]['operator'];
					if (!(strpos($op_prepped, "LIKE")===FALSE)) {
						$value_prepped=$this->search_criteria[$current_set][$currentField]['value'];
						if ($op_prepped<>"_LIKE") {
							$value_prepped="%".$value_prepped;	}
						if ($op_prepped<>"LIKE_") {
							$value_prepped=$value_prepped."%";}
						if ($this->search_criteria[$current_set][$currentField]['operator']=="!LIKE") {
							$clause_start="!(";
							$clause_end=")";
						} else { 
							$clause_start="";		
							$clause_end="";
						}
						$op_prepped="LIKE";

						$value_prepped =$dbcon->qstr($value_prepped);
					} else {
						$value_prepped = $dbcon->qstr($this->search_criteria[$current_set][$currentField]['value']);
					}
					
					if (strpos($op_prepped, "EMPTY")===FALSE) {
						if (strpos($op_prepped, "TRUE")===FALSE){
							$setup_sql.=" ".$clause_start.$currentField." ".$op_prepped;
							$setup_sql.=" ".$value_prepped.$clause_end;
						} else {
							if ($op_prepped=="TRUE") {
								$setup_sql.=" (".$currentField.") ";
							} else { //operator is NOT TRUE
								$setup_sql.=" (!".$currentField.") ";
							}
						}
					} elseif ($op_prepped=="EMPTY") {
						$setup_sql.=" (IsNull($currentField) OR $currentField='') ";
					} else { //NOT EMPTY
						$setup_sql.=" (!IsNull($currentField) AND $currentField!='') ";
					}
					if (isset($this->search_logic[$current_set]['internal'])) {
						$logic_prepped =$this->search_logic[$current_set]['internal'];
					} else {
						$logic_prepped =$this->search_criteria[$current_set][$currentField]['logic'];
					}
					$setup_sql .= " ".$logic_prepped; 
				}
			}//End Clause Loop
			$lastcall=strpos(strrev($setup_sql), " ");
			if ($lastcall) {
				$setup_sql2 = "((".substr($setup_sql, 0, strlen($setup_sql)-$lastcall).")";
				if(isset($this->is_parent[$current_set])) {
					$all_children=explode(",", $this->is_parent[$current_set]);
					foreach ($all_children as $this_child) {
						#echo "child $this_child found<BR>";
						$setup_sql2.=" ".$this->search_logic[$this_child]['external']." ";
						$setup_sql2 .= $this->setup_subSearch($dbcon, $this_child, $avoid_custom);
						$lastcall2=strpos(strrev($setup_sql2), ")");
						$setup_sql2 = substr($setup_sql2, 0, strlen($setup_sql2)-$lastcall2);
					}
				}
				$setup_sql2.=")".substr($setup_sql, strlen($setup_sql)-$lastcall);
			}
		return $setup_sql2;
	}

	function setupSearch ($dbcon, $avoid_custom=FALSE, $search_set=NULL) {
		if (!isset($search_set)) {
		    $search_set=array();
			for ($i=$this->search_count; $i>=0; $i--) {
				$search_set[$i]=$i;
				if ($i>0) {
					$this->setSearchParent($i, $i-1);
				}
			}
		}

		//CYCLE THROUGH SEARCH SETS
		foreach($search_set as $current_set) {
			if (!$this->is_included[$current_set]) {
				$criteria_sql.=$this->setup_subSearch($dbcon, $current_set, $avoid_custom);
			}
		}
		
		//STRIP OUT FINAL LOGIC CALL
		$lastcall=strpos(strrev($criteria_sql), ")");
		$criteria_sql = substr($criteria_sql, 0, strlen($criteria_sql)-$lastcall);
		//INCLUDE MODIN RESTRICTION
		$criteria_sql = "(".$criteria_sql." AND modin = ".$this->base_modin.")";
		
		//CHECK for additional Modules Defined
		if (isset($this->include_modin)) {
			$this->is_included= array();
			foreach($search_set as $current_set) {
				if (!$this->is_included[$current_set]) {
					$criteria_sql2.=$this->setup_subSearch($dbcon, $current_set, TRUE);
				}
			}
			//STRIP OUT FINAL LOGIC CALL
			$lastcall=strpos(strrev($criteria_sql2), ")");
			$criteria_sql2 = substr($criteria_sql2, 0, strlen($criteria_sql2)-$lastcall);
			//INCLUDE MODIN RESTRICTION
			if ($this->include_modin!="*") {
				$criteria_sql2 .= " AND modin IN(".$this->include_modin.")";
			} else {
				$criteria_sql2.=" AND modin != ".$this->base_modin;
			}
			$criteria_sql.=" OR (".$criteria_sql2.") ";
		}

		//ASSEMBLE SQL statment
		$this->current_sql = "SELECT DISTINCTROW ".$this->display_fields.$this->setupSort()." FROM ".$this->search_tables." WHERE ".$criteria_sql." ORDER BY ".$this->sort_by;
		echo $this->current_sql."<BR>";
	}

	function setupSort() {
		if($sort_set = explode(',', $this->sort_by)) {
			$sort_set[0]=str_replace(" DESC", " " , $sort_set[0]);
			$sort_set[0]=str_replace(" ASC", " ", $sort_set[0]);
			$primary_sort=trim($sort_set[0]);
			$output = ", ".$primary_sort." as sort_".$primary_sort;
		}
		return $output;
	}
		

	function changeDisplay($showFields) {
		$this->display_fields = $showFields;
	}
	function changeSort($sortFields) {
		$this->sort_by = $sortFields;
	}
	function runSearch($dbcon) {
		if ($templist = $dbcon->Execute($this->current_sql)) {
			for ($i=0; $i<=$templist->FieldCount(); $i++) {
				$fieldobj=$templist->FetchField($i);
				$this->current_fieldset[$i]=$fieldobj->name;
			}
			$this->current_list=$templist->GetArray();
			if ($this->qty_displayed=="*") {$this->qty_displayed=count($this->current_list);}
		
		} else {
			$this->current_list=NULL;
		}
	}


} ?>