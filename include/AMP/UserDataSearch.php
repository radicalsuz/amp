<?php
class UserList {
	var $display_fields="id, Concat(First_Name, \" \", Last_Name) as Name, Company, State, Phone";
	var $current_fieldset;
	var $search_criteria;
	var $search_tables="userdata";
	var $search_logic;
	var $is_parent;
	var $is_included;
	var $search_hier;
	var $qty_displayed;
	var $include_modin;
	var $sort_by="Last_Name, First_Name";
	var $current_sql;
	var $current_list;
	var $search_count;

	function showlist($action_field, $link_action, $allow_edit=FALSE){
		$default_modin=3;
		$list_row_start='<tr bordercolor="#333333" bgcolor="#CCCCCC" class="results">';
		$list_row_end="</tr>\n";
		$list_item_start="<td>";
		$list_item_end="</td>";
		
		//begin Row output loop
		if ($this->current_list!=NULL) {
		    
			foreach ($this->current_list as $current_row){
				$list_row="";
				#$debug_list=array_flip($current_row);
				foreach($this->current_fieldset as $current_field) {
				 $list_row.=$list_item_start.$current_row[$current_field].$list_item_end;
				}
				//insert edit link
				$list_row.=$list_item_start;

				if ($allow_edit){ //edit offered to admin users
					if(!strpos($this->include_modin, ",")===FALSE) 
						{$action_modin=$default_modin;} 
					else {$action_modin=$this->include_modin;}
					$list_row.="<a href=\"$link_action?modin=$action_modin&uid=".$current_row[$action_field]."\">edit</a>";
				}
				$list_row=$list_row_start.$list_row.$list_item_end.$list_row_end;
				//append row to html var
				$list_html.=$list_row;
			
			}
		} else { //No Records Found
			$list_html=$list_row_start."<td>No Records Found</td>".$list_row_end;
	
		}	
		return $list_html;	
	}

	
	function output_list ($link_action="modinput4_view.php", $current_page=1) {
		global $userper, $standalone;
		$list_html_start='<table cellpadding="1" cellspacing="1" width="95%">';
		$list_html_start.='<tr class="toplinks">';
		if ($this->current_list!=NULL) {
			//DISPLAY COLUMN HEADERS
			foreach($this->current_fieldset as $current_field) {
				$list_html_start.="<td align=\"left\"><b>$current_field</b></td>";
			}
			$list_html_start.="<td><!--editlink column--></td>";
			$list_html_start.=$list_row_end;
			$allow_edit=($userper[87] == 1 || $standalone == 1);
			$list_html=$this->showlist("id", $link_action, $allow_edit);			
		}
		$list_html_footer = "</table>";
		return $list_html_start.$list_html.$list_html_footer;
	}

	function tab_navs() {
		$page_tabs="Results,Add Search,Advanced Search";
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
	
	function readAdvSearch($select_output) {
		foreach ($select_output as $criteria) {
			print " : ".$criteria."<BR>";
			$search_item=explode("|//|", $criteria);
			// $criteria is split into 0)search_set - 1)Field -2) operator - 3)value - 4)logic- 5)orderby
			$this->addCriteria($search_item[1], $search_item[2], $search_item[3], $search_item[0], $search_item[4]);
			#$usersearch->setLogic($search_item[4], $search_item_

		}
	}
	
	
	function advSearchForm($udm, $action="modinput4_search.php") {
		foreach ($udm->fields as $fieldname => $fdef) {
			if (strlen($fdef['label'])>35) {
					$thislabel=substr($fdef['label'], 0, 35);
			} else {$thislabel=$fdef['label'];}
			$fieldselect.="<option value=\"$fieldname\">".$thislabel."</option>\r";
		}
		$compare_operators=array();
		$compare_operators['equals']="=";
		$compare_operators['contains']="LIKE";
		$compare_operators['does not contain']="LIKE";
		$compare_operators['does not equal']="!=";
		$compare_operators['greater than']=">";
		$compare_operators['greater than or equal to']=">=";
		$compare_operators['less than']="<";
		$compare_operators['less than or equal to']="<=";
		
		$logic_set['AND']="AND";
		$logic_set['OR']="OR";
		/*$logic_set['New Group']='(';
		$logic_set['End Group']=')';
		*/
		$name_set=array_keys($compare_operators);
		foreach($name_set as $compare_value) {
			$compare_select.="<option value=\"".$compare_operators[$compare_value]."\">$compare_value</option>";
		}

		$name_set=array_keys($logic_set);
		foreach($name_set as $logic_value) {
			$logic_select.="<option value=\"".$logic_set[$logic_value]."\">$logic_value</option>";
		}


		$form_header_addCriteria="<form name=\"AddCriteria\">";
		$form_body_addCriteria="<P><select name = \"choose_field\" width=\"30\">".$fieldselect."</select><BR><select name=\"choose_comparison\">".$compare_select."</select><BR><input name=\"value_field\" type=\"text\" size=\"30\"><BR><select name=\"choose_logic\">$logic_select</select><br><input name=\"add_criteria\" type=\"button\" value=\"Add Field\"  onclick=\"addCriteriatoSearch();\">";
		$form_footer="</form>";


		$remove_button = "<input name=\"remove_criteria\" type=\"button\" value=\"Remove Criteria\"  onclick=\"delOptions();\">";
		$search_box="<SELECT MULTIPLE name=\"UDM_Search_Options[]\" size=\"10\" width=\"30\"><option value=\" \" selected=\"selected\">Please enter criteria below</option></select>";
		$new_group_button="<input name=\"newgroup\" type=\"button\" value=\"Start Group\" onclick=\"startGroup();\">";
		$select_group="<select name=\"selectgroup\" type=\"select\" onchange=\"setGroup();\" style=\"display: none;\"><option value=\"0\" selected>No Group Selected</option>";
		$search_button_go="<input name=\"btnUdmSubmit\" value=\"Search\" type=\"button\" onclick=\"goSearch();\">";
		$hidden_values="<input name=\"currentgroup\" type=\"hidden\" value=\"0\"><input name=\"totalgroups\" type=\"hidden\" value=\"0\"><input name=\"parentgroup\" type=\"hidden\" value=\"0\"><input name=\"num_items\" type=\"hidden\" value=\"0\"><input name=\"modin\" value=\"".$udm->instance."\" type=\"hidden\">";
		$form_header="<script type=\"text/javascript\">\r\n";
	var SearchCriteria=new Array();
		
	function addOptions(chosen, ch_value) {
		var selbox = document.forms['UDM_Advanced_Search'].elements['UDM_Search_Options[]'];
		if (selbox.options[0].value == ' ') {
			selbox.options.length = 0;
		}
		var fnd = 0;
		for (n=0;n<selbox.length;n++){
			if(selbox.options[n].text == chosen){
				fnd = 1;}
			if(selbox.options[n].selected==1){
				var finalselect=n;}
		}
		if (!fnd) {
			document.forms['UDM_Advanced_Search'].elements['num_items'].value++;
			lowerlines=new Array;
			if ((finalselect+1)<selbox.length) {
				alert(finalselect+'/'+selbox.length);
				lowerlines=moveOptions(finalselect+1, selbox.length, 1);
				selbox.length=finalselect+1;
			}
			selbox.options[selbox.options.length] = new Option(chosen, ch_value+'|//|'+(finalselect+1), false, true);
			if (lowerlines.length == 0) {
				//selbox.options[0]= new Option('Please enter criteria below',' ');
			} else {
				alert ('lowerlines passed');
				for (n=0;n<lowerlines.length;n++){
					selbox.options[finalselect+n+2] = new Option(lowerlines[n][1], lowerlines[n] [0]);
			}} 

		}
	}

	function moveOptions(start, finish, step) {
		var selbox = document.forms['UDM_Advanced_Search'].elements['UDM_Search_Options[]'];
		searchlines=new Array;		
		for (n=start; n<finish;n++){
				searchlines[searchlines.length] = new Array(selbox.options[n].value, selbox.options[n].text);
				var orderby_start = searchlines[(n-start)][0].lastIndexOf('|//|')+4;
				var orderby=parseInt((searchlines[(n-start)][0].substring(orderby_start)))+step;
				searchlines[(n-start)][0]=searchlines[(n-start)][0].substring(0, orderby_start+1)+orderby;
				alert (searchlines[(n-start)][0]);
		}
		selbox.options.length=finish;
		return searchlines;
	}

 
	function delOptions() {
		var selbox = document.forms['UDM_Advanced_Search'].elements['UDM_Search_Options[]'];
		if (selbox.options[0].value != ' ') {
			nomatch = new Array();
			for (n=0;n<selbox.length;n++){
				if (selbox.options[n].selected==0){
					nomatch[nomatch.length] = new Array(selbox.options[n].value, selbox.options[n].text);
			}}
			selbox.options.length = 0;
			if (nomatch.length == 0) {
				selbox.options[0]= new Option('Please enter criteria below',' ');
			} else {
				for (n=0;n<nomatch.length;n++){
					selbox.options[n] = new Option(nomatch[n][1], nomatch[n] [0]);
	}}}}
	
	function addCriteriatoSearch() {
		var comparebox=document.forms['AddCriteria'].elements['choose_comparison'];
		var logicbox=document.forms['AddCriteria'].elements['choose_logic'];
		var fieldbox=document.forms['AddCriteria'].elements['choose_field'];
		if (document.forms['UDM_Advanced_Search'].elements['selectgroup'].value!=0) {
			var current_group=document.forms['UDM_Advanced_Search'].elements['selectgroup'].value;
			var grouptext='Grp ['+current_group+']: ';
		} else {
			var current_group=0;
			var grouptext='';
		}
		var chosen_text=(grouptext+fieldbox.options[fieldbox.selectedIndex].text+ ' ' + comparebox.options[comparebox.selectedIndex].text+' '+document.forms['AddCriteria'].elements['value_field'].value+' '+logicbox.options[logicbox.selectedIndex].text);
		var chosen_values=(current_group+'|//|' + fieldbox.value+ '|//|' + comparebox.value+'|//|'+document.forms['AddCriteria'].elements['value_field'].value+'|//|'+logicbox.value);
		addOptions(chosen_text, chosen_values);
	}

	function startGroup () {
		document.forms['UDM_Advanced_Search'].elements['totalgroups'].value++;
		var mytotal=document.forms['UDM_Advanced_Search'].elements['totalgroups'].value;
		var selbox=document.forms['UDM_Advanced_Search'].elements['selectgroup'];
		var myparent=document.forms['UDM_Advanced_Search'].elements['parentgroup'];
		if (selbox.selectedIndex==0) {
			selbox.options[selbox.options.length]=new Option('Group '+mytotal, mytotal, true, true);
			myparent.value=0;
		} else {//a group is already selected
			//make new group as child of previous 'parentless' group
			myparent.value=selbox.value;
			selbox.options[selbox.options.length]=new Option('Grp '+mytotal+' in Group '+myparent.value, myparent.value+':'+mytotal, true, true);
			
		}
		document.forms['UDM_Advanced_Search'].elements['selectgroup'].style.display='block';
	}
	function setGroup () {
		var selbox = document.forms['UDM_Advanced_Search'].elements['UDM_Search_Options[]'];
		var current_group=document.forms['UDM_Advanced_Search'].elements['selectgroup'].value;
		for (n=0;n<selbox.length;n++){
				if (selbox.options[n].value.substring(0, selbox.options[n].value.indexOf('|//|'))==current_group) 	{
					selbox.options[n].selected=1;} else {selbox.options[n].selected=0;}
		}
	
	}

	function goSearch() {
		var selbox = document.forms['UDM_Advanced_Search'].elements['UDM_Search_Options[]'];
		if (selbox.options[0].value != ' ') {
			for (n=0;n<selbox.length;n++){
					selbox.options[n].selected=1;
			}
			document.forms['UDM_Advanced_Search'].submit();
		} else {
			alert ('No search criteria have been selected');
		}
	}
	</script>
	<form name=\"UDM_Advanced_Search\" action=\"$action\" method=\"POST\">";
		$controls_html="<Table><tr><td>$search_box</td><td valign=\"top\" align=\"center\">$remove_button<BR>$new_group_button<BR>$select_group<BR>$hidden_values</td></tr><tr><td colspan=2 align=\"center\">$search_button_go</td></tr></table>";
		

		$form_html= $form_header.$controls_html.$form_footer.$form_header_addCriteria.$form_body_addCriteria.$form_footer;
		return $form_html;
		



	}	
	
	function addModule ($modin) {
		$current_modules=explode(",", $this->include_modin);
		if (!isset($this->include_modin)) {
			$this->include_modin=$modin;
		} elseif (array_search($modin, $current_modules)===FALSE) {
			$this->include_modin.=",".$modin;
		}
	}

	function addCriteria ($field, $operator, $value, $search_set=0, $logic="AND") {
		$search_set=$this->setSearchParent($search_set);
		if ($search_set>$this->search_count) {$this->search_count=$search_set;}
		$this->search_criteria[$search_set][$field]['operator']=$operator;
		$this->search_criteria[$search_set][$field]['logic']=$logic;
		$this->search_criteria[$search_set][$field]['value']=$value;
		echo "Added Criteria in Search[$search_set]:$field$operator$value";
		echo "/".$this->search_criteria[$search_set][$field]."<BR>";
	}

	function setLogic ($logic, $search_set=0, $target=NULL) {
		if (!isset($target)) {
			$this->search_logic[$search_set]['internal']=$logic;
			$this->search_logic[$search_set]['external']=$logic;
		} else {
			$this->search_logic[$search_set][$target]=$logic;
		}
	}

	function setSearchParent($search_set) {
		if (strpos($search_set, ":")===FALSE&&(!isset($this->is_parent[$search_set])||$this->is_parent[$search_set]===FALSE)) {
			$this->is_parent[$search_set]=FALSE;
			$last_child=$search_set;
		} else {
			$search_hier=array_reverse(explode(":", $search_set));
			$lastcall= strpos(strrev($search_set), ":");
			$last_child=substr(strrev($search_set), 0, $lastcall);

			foreach ($search_hier as $current_level) {
				if ($current_level!=$last_child){
					if ($this->is_parent[$current_level]>0) {
						$counted_child=FALSE;
						$all_children=explode(",", $this->is_parent[$current_level]);
						foreach($all_children as $one_child) {
							if ($one_child==$previous_level) { $counted_child=TRUE; }
						}
						if (!$counted_child) {$this->is_parent[$current_level].=",".$previous_level;}
					} else {
						$this->is_parent[$current_level]=$previous_level;
					}
				} 
				$previous_level=$current_level;
			}
		}
#		$this->is_parent[$last_child]=FALSE;
		return $last_child; 
	}


	function setup_subSearch ($dbcon, $current_set) {
			$this->is_included[$current_set]=TRUE;
			echo "starting subsearch $current_set<BR>";
			//ADD EACH CRITERIA  WITHIN SET
			$working_fieldset=array_keys($this->search_criteria[$current_set]);
			foreach ($working_fieldset as $currentField) {
				#echo "$current_set#".$currentField."/".$this->search_criteria[$current_set][$currentField]."<BR>";
				$setup_sql.=" ".$currentField." ".$this->search_criteria[$current_set][$currentField]['operator'];
				$setup_sql.=$dbcon->qstr($this->search_criteria[$current_set][$currentField]['value']);
				$setup_sql .= " ".$this->search_criteria[$current_set][$currentField]['logic']; 
			}
			$lastcall=strpos(strrev($setup_sql), " ");
			$setup_sql2 = "(".substr($setup_sql, 0, strlen($setup_sql)-$lastcall);
			#$setup_sql2="(".$setup_sql;
			if($this->is_parent[$current_set]>0) {
				$all_children=explode(",", $this->is_parent[$current_set]);
				foreach ($all_children as $this_child) {
					echo "child $this_child found<BR>";
					$setup_sql2 .= $this->setup_subSearch($this_child);
					$lastcall2=strpos(strrev($setup_sql2), ")");
					$setup_sql2 = substr($setup_sql2, 0, strlen($setup_sql2)-$lastcall2);
				}
			}
			$setup_sql2.=")".substr($setup_sql, strlen($setup_sql)-$lastcall);
			#$setup_sql2.=")";
			return $setup_sql2;
	}

	function setupSearch ($dbcon, $search_set=NULL) {
		if (!isset($search_set)) {
		    $search_set=array();
			for ($i=0; $i<=$this->search_count; $i++) {
				$search_set[$i]=$i;
			}
		}
		//CYCLE THROUGH SEARCH SETS
		foreach($search_set as $current_set) {
			echo "$current_set: is_parent:".$this->is_parent[$current_set]."<BR>";
			/*
			if (strpos($current_set, ":")===TRUE) {
				$search_hier=explode(":", $current_set);
				foreach($search_hier as $parent_search) {
					if (!isset($criteria_sql[$parent_search])) {
						$criteria_sql[$parent_search]=$this->setup_subSearch($parent_search);
					}else {
						$lastcall=strpos(strrev($criteria_sql), ")");
						$final_logic = substr($criteria_sql[$parent_search], strlen($criteria_sql)-$lastcall);
						$criteria_sql[$parent_search] = substr($criteria_sql[$parent_search], 0, strlen($criteria_sql)-$lastcall).setup_subSearch($child_search).$final_logic;
					}
				}
			} else {
			*/
			if (!$this->is_included[$current_set]) {
				$criteria_sql.=$this->setup_subSearch($dbcon, $current_set);
			}
		}
		
		//STRIP OUT FINAL LOGIC CALL
		$lastcall=strpos(strrev($criteria_sql), ")");
		$criteria_sql = substr($criteria_sql, 0, strlen($criteria_sql)-$lastcall);
		//INCLUDE MODIN RESTRICTION
		$criteria_sql .= " AND modin IN(".$this->include_modin.")";
		//ASSEMBLE SQL statment
		$this->current_sql = "SELECT DISTINCTROW ".$this->display_fields." FROM ".$this->search_tables." WHERE ".$criteria_sql." ORDER BY ".$this->sort_by;
		echo $this->current_sql."<BR>";
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
		} else {
			$this->current_list=NULL;
		}
	}


} ?>
