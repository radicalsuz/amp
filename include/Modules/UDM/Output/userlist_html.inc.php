<?php


function udm_output_userlist_html($udm, $options=null) {
		global $ID, $MM_email_from; // - returns current user variable
		//List Vars
		//create default options array
		$default_options['editlink_fields']="modin,id";
		$default_options['editlink_action']="modinput4_view.php";
		$default_options['allow_edit']=TRUE;
		$default_options['display_fields']="id, Concat(First_Name, \" \", Last_Name) as Name, Company, State, Phone, publish";
		$default_options['default_qty']=50;
		$default_options['current_offset']=0;
		$default_options['qty_displayed']=$default_options['default_qty'];
		$default_options['sort_by']="Last_Name, First_Name";
		$default_options['page_name']=$_SERVER['PHP_SELF'];
		$default_options['usertable']="userdata";
		#$default_options['Lookups'][]=array("fieldname"=>"modin", "LookupTable"=>"userdata_fields", "LookupField"=>"name");
		$default_options['Lookups']['publish']=array("LookupSet"=>array("0"=>"draft" , "1"=>"live"), 'LookupName'=>'status');
		
		$default_options['show_headers']=TRUE;
		$default_options['is_dynamic']=TRUE;
		$default_options['form_name']='UDM_List';
		$default_options['show_action_bar']=TRUE;
		$default_options['allow_lookups']=TRUE;
		$default_options['list_form']="UDM_Listing";
		$default_options['allow_publish']=TRUE;
		$default_options['allow_email']=TRUE;
		$default_options['email_action']="udm_mailblast.php";
		$default_options['allow_include_modins']=FALSE;
		$default_options['show_advanced_modin']=FALSE;

		if ($default_email=$udm->dbcon->GetAssoc("SELECT id, name, email from users where id=$ID")){
			if (isset($default_email[$ID]['email'])) {
				$default_options['user_email']=$default_email[$ID]['email'];
				$default_options['user_name']=$default_email[$ID]['name'];
			}
		} else { 
			$default_options['user_email']=$MM_email_from;
		}
		
		//pass default options into standard options array when no value exists
		foreach ($default_options as $key=>$this_option) {
			if ($key=='Lookups') {
				foreach ($default_options['Lookups'] as $look_key=>$lookup) {
					if (!isset($options['Lookups'][$look_key])) { $options['Lookups'][$look_key]=$lookup;}
				}
			} else {
				if (!isset($options[$key])) { $options[$key]=$this_option;}
			}
		}

	//check display fields for admin/enabled
	$options=list_check_fields($udm, $options);
	
	if ($options['is_dynamic']) {
		$options=list_readFormOptions($udm, $options);
	}

	if ($options['allow_include_modins']&&$options['show_advanced_modin']) {
		$options['Lookups']['modin']['LookupName']="Source";
		$options['Lookups']['modin']['LookupTable']="userdata_fields";
		$options['Lookups']['modin']['LookupField']="name";
	}

	if ($options['allow_lookups']) {
		$options=list_setupLookups($udm, $options);
	}
	
	$options['hidden_fields'].=list_setupSort($options).list_setupEditLink($options);


	///Create the query for the list
	$udm->set_sql['from']=$options['usertable'];
	$udm->set_sql['where']="modin=".$udm->instance;

	//Extra SQL manipulation for including info from multiple lists
	if ($options['show_advanced_modin']&&isset($options['include_modin'])) {
		$udm->set_sql['where']= ($options['include_modin']=="*")?"1":$udm->set_sql['where']." OR modin in(".$options['include_modin'].") ";
		$options['display_fields'].=", modin";
	
	}
	$udm->set_sql['select']=$options['display_fields'].$options['hidden_fields'];
	$udm->set_sql['orderby']=$options['sort_by'];

	//Retrieve the Set
	if ($udm->getSet($options)) {
		//Write the Form Options (for persistent behaviours)
		$output=list_writeFormOptions($udm, $options);
		//Write the list page w/controls
		$output.=list_output_dynamic($udm, $options);
		$output.="</form>";
	} else {
		$output="This Module is currently empty";
	}
	if ($udm->authorized) {
		return $output;
	} else {
		return 'You do not have permission to view this list';
	}
}

	
	/// READS PAGINATION, SORTS, DISPLAYFIELDS, ACTIONS FROM FORM
	
	function list_readFormOptions($udm, $options) {
		$options=list_readPagination($options);
		$options=list_readSort($options);
		$options=list_readDisplayFields($options);
		$options=list_readAction($udm, $options);
		if ($options['allow_include_modins']) {
			$options=list_readModins($options);
		}
		
		return $options;
	}


	/// WRITES PAGINATION, SORT, DISPLAYFIELD To FORM
	function list_writeFormOptions($udm, $options) {
		$script="<script type=\"text/javascript\">
		var sform=document.forms['".$options['list_form']."'];     
		
		//Javascript function to select all/deselect all on a given page

		function list_selectall() {
			sform=document.forms['".$options['list_form']."']; 
			t=document.forms['".$options['list_form']."'].length;
			if (sform.elements['list_select'].value=='Select All') {
				sform.elements['list_select'].value='Unselect All';
				var tvalue=true;
			} else {
				sform.elements['list_select'].value='Select All';
				var tvalue=false;
			}
			for (n=0; n<t; n++){
				//alert(sform.elements[n].name);
				if (sform.elements[n].name.substring(0,2)=='id') {
					sform.elements[n].checked=tvalue;
				}
			}
		}
		//Javascript - returns the currently selected ids as a comma separated string
		function list_return_selected() {
			sform=document.forms['".$options['list_form']."']; 
			t=sform.length;
			var selected_list='';
			for (n=0; n<t; n++){
				//alert(sform.elements[n].name);
				if (sform.elements[n].name.substring(0,2)=='id') {
					if (sform.elements[n].checked==true) {
						selected_list=selected_list+','+sform.elements[n].value;
					}
				}
			}
			selected_list=selected_list.substring(1);
			return selected_list;
		}

		//Javascript - selects an ID - used by table row_select
		function select_id(find_id) {
			sform=document.forms['".$options['list_form']."'];
			t = sform.length;
			for (n=0; n<t; n++){
				//alert(sform.elements[n].name);
				if (sform.elements[n].name.substring(0,2)=='id'&&sform.elements[n].value==find_id) {
					sform.elements[n].checked=!sform.elements[n].checked;
					return;
				}
			}
		}

	
		//Javascript - checks current status and updates SQL before starting Email Blast
		function setup_Email() {
			var eform=document.forms['UDM_email'];
			var mylist=list_return_selected();
			if (mylist=='') {
				var reply= confirm('you have not selected any names\\nSend e-mail to entire list?');
				if (reply) { 
					eform.elements['sqlp'].value=eform.elements['start_sql'].value;
					//alert( eform.elements['sqlp'].value);
					eform.submit(); 
				}
			} else {
				eform.elements['sqlp'].value=eform.elements['start_sql'].value+'AND id IN('+mylist+')'; 
				//alert( eform.elements['sqlp'].value);
				eform.submit();
			}
		}

		//Javascript - checks current status and updates SQL before starting Export
		function setup_Export() {
			var sform=document.forms['".$options['list_form']."'];
			var mylist=list_return_selected();
			if (mylist=='') {
				var reply= confirm('you have not selected any names\\nExport entire list?');
				if (reply) { 
					sform.action='modinput4_export.php?modin=".$udm->instance."';
					//alert (sform.action+'\\n'+sform.elements['sqlp'].value);
					sform.submit(); 
				}
			} else {
				sform.action='modinput4_export.php?modin=".$udm->instance."';
				sform.submit();
			}
		}

		
		</script>";
		if ($options['allow_edit']&&$options['show_action_bar']) { 		
			$form.=list_emailBlastConnect($udm, $options);
		}
		$form.="<form name='".$options['list_form']."' action='".$options['page_name']."?modin=".$udm->instance."' method=\"POST\"><input name=\"UDM_sort\" value=\"".$options['sort_by']."\" type=\"hidden\"><input name=\"offset\" value=\"".$options['current_offset']."\" type=\"hidden\"><input name=\"UDM_Action\" value=\"\" type=\"hidden\"><input name=\"list_page_qty\" value=\"".$options['qty_displayed']."\" type=\"hidden\"><input name=\"list_editlink_action\" value=\"".$options['editlink_action']."\" type=\"hidden\"><input name=\"list_show_adv_modin\" value=\"".$options['show_advanced_modin']."\" type=\"hidden\">";

		if (isset($options['message'])) {$form=$options['message']."<BR>".$form;}

		return $script.$form;
	}


	////// Creates the listing of results
	function showlist(&$udm, $options){
		$list_row_start_template="<tr id=\"listrow_%s\" bordercolor=\"#333333\" bgcolor=\"%s\" class=\"results\" onMouseover=\"this.bgColor='#CCFFCC';\" onMouseout=\"this.bgColor='%s';\" onClick=\"select_id(this.id.substring(8));\">";
		$list_row_end="</tr>\n";
		$list_item_start="<td class=list_column_%s>";
		$list_item_end="</td>";
		if ($options['allow_edit']){ //edit offered to admin users
				$list_row_select.=sprintf($list_item_start, 'ROWSELECT_box')."<input name=\"id[]\" type=\"checkbox\" value=\"%s\" onclick=\"this.checked=!this.checked;\">".$list_item_end;
				$list_row_edit=sprintf($list_item_start,'editlink')."<a href=\"%s\">edit</a>".$list_item_end;
				#$list_row_edit="<TD><a href=\"%s\">edit</a>".$list_item_end;


		}
		//begin Row output loop
		if ($udm->results!=NULL) {
			if ($options['qty_displayed']=="*"){$options['qty_displayed']=count($udm->results);}    
			for ($n=$options['current_offset']; ($n<($options['current_offset']+$options['qty_displayed'])&&$n<count($udm->results)); $n++){
				$current_row=$udm->results[$n];
				//Alternates the background color
				$bgcolor =($n % 2) ? "#D5D5D5" : "#E5E5E5";
				//assigns an id and background color to each row
				$list_row="";
				$list_row_start=sprintf($list_row_start_template, $current_row['hide_id'], $bgcolor, $bgcolor);
				
				//Field Output Loop
				foreach($current_row as $key=>$current_field) {
					//Check if this is a hidden field
					if (!(substr($key, 0, 5)=="hide_")) { 
						//Check for lookup field
						if ($lookup=$options['Lookups'][$key]) {
							$current_field=$lookup['LookupSet'][$current_field];
						}
						
						$list_row.=sprintf($list_item_start, $key).$current_field.$list_item_end;
					}
				}
				
				if ($options['allow_edit']) {
					$list_row=sprintf($list_row_select, $current_row['hide_id']).$list_row.sprintf($list_row_edit, $current_row['hide_editlink']);
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




//////Wrapper for results list - formats the container and title row
//// includes control bars
	function list_output_dynamic (&$udm, &$options) {
		$list_html_start.='<center><table cellpadding="1" cellspacing="1" width="95%">';
		$list_html_start.='<tr class="toplinks">';
		if ($udm->results != NULL) {
			//DISPLAY COLUMN HEADERS
			$column_count=0;
			foreach($udm->results[0] as $current_field=>$field_value) {
				if (substr($current_field, 0, 5)!="hide_") { //hide sort fields and editlink fields
					$list_html_headers.="<td align=\"left\">";
					//Each field is linked to change the sort_by value and submit the form 
					if ($current_field=="Name") { 
						
						//hack for name field to sort properly
						$list_html_headers.="<b><a href=\"javascript: document.forms['".$options['list_form']."'].elements['UDM_sort'].value = 'Last_Name, First_Name, '+document.forms['".$options['list_form']."'].elements['UDM_sort'].value; document.forms['".$options['list_form']."'].submit();\">".list_translateFields($current_field, $udm, $options)."</a></b>";
					} else {
						
						//standard column header
						$list_html_headers.="<b><a href=\"javascript: document.forms['".$options['list_form']."'].elements['UDM_sort'].value = '$current_field, '+document.forms['".$options['list_form']."'].elements['UDM_sort'].value; document.forms['".$options['list_form']."'].submit();\">".list_translateFields($current_field, $udm, $options)."</a></b>";
					}
					$column_count++;
					$list_html_headers.="</td>";
				}

			}
			
			if ($options['allow_edit']) {
				//include columns for checkbox and editlink values
				$list_html_headers="<td align=\"left\"><a href=\"javascript: list_selectall();\"><B>All</B></a></td>".$list_html_headers."<td></td>";
				$column_count=$column_count+2;
			}
			$list_html_start.=$list_html_headers."</tr>";
			$list_html=showlist($udm, $options);			
		}
		$list_html_footer = "</table></center>";
			
		
		//INSERT PAGINATION
		$list_html_start=list_pagination_header($udm, $options).$list_html_start;
		$list_html_footer.=list_pagination_header($udm, $options+array('show_advanced_links'=>true));
		
		
		//INSERT action bar
		if ($options['allow_edit']&&$options['show_action_bar']) {
			$list_html_start=list_action_bar($options)."\n  ".$list_html_start;
		}

		//INSERT modin selectbox
		if ($options['show_advanced_modin']) {
			$list_html_footer .= list_include_modin_box(&$udm, &$options);
		} 

		//INSERT LIST ACTION OPTIONS
		#$options_html="<div class=\"side\" style=\"float:right;\"><form name='export_button' action='export4.php?id=".$udm->instance."' method='POST'><input type=\"hidden\" name=\"sqlsend\" value=\" FROM userdata WHERE modin=".$udm->instance."\"><a href=\"#\" onclick=\"checkSave();\">Save This Search</a> &nbsp;| &nbsp;<a href=\"#\" onclick=\"document.forms['export_button'].submit();\">Export List</a></form></div><BR>";
		#$list_html_start=$options_html.$list_html_start;

		return $list_html_start.$list_html.$list_html_footer;
	}
			
///CREATES HTML for PUBLISH, UNPUBLISH, DELETE, SELECT-ALL Buttons
	function list_action_bar($options) {
		if ($options['allow_publish']) {

			//Each of these sets the UDM_Action value of the form and submits the form
			//Selected items are posted in UDM_id
			//Publish Button
			$publish_btn="<input type=\"button\" name=\"Publish\" value=\"Publish\" class=\"name\" onclick=\"document.forms['".$options['list_form']."'].elements['UDM_Action'].value='Publish'; document.forms['".$options['list_form']."'].submit();\">";
			//Unpublish Button
			$unpublish_btn="<input type=\"button\" name=\"UnPublish\" value=\"Unpublish\" class=\"name\"  onclick=\"document.forms['".$options['list_form']."'].elements['UDM_Action'].value='Unpublish'; document.forms['".$options['list_form']."'].submit();\">";
			//Delete Button
			$delete_btn="<input type=\"button\" name=\"Delete\" value=\"Delete\" class=\"name\"  onclick=\"if (confirm('Are you sure you want to DELETE these records?')) {document.forms['".$options['list_form']."'].elements['UDM_Action'].value='Delete'; document.forms['".$options['list_form']."'].submit();}\">";
		}
		//Email Button
		if ($options['allow_email']) {
			$email_btn="<input type=\"button\" class=\"name\" value=\"Send Email\" name=\"list_email\" onclick=\"setup_Email();\">";
		}	
		///Export Button
		if ($options['allow_export']) {
			$export_btn="<input type=\"button\" class=\"name\" value=\"Export\" name=\"list_export\" onclick=\"setup_Export();\">";
		}
		$select_all_btn="<input type=\"hidden\" name=\"list_select\" class=\"name\"  value=\"Select All\" onclick=\"list_selectall()\">";

		$output ="<div class=side style=\"width:100%;text-align:left;vertical-align: top;background-color:#FFFFFF;padding: 0 5px 5px 10px;\"><B>With Selected:</b> ".	$select_all_btn.$publish_btn."&nbsp;&nbsp;".$unpublish_btn."&nbsp;&nbsp;".$delete_btn."&nbsp;&nbsp;".$email_btn."&nbsp;&nbsp;".$export_btn."</div>";
		return $output;
	}

	/// CREATES the Prev/Next/Current Location/Qty/Go Control Set for the Results list
	function list_pagination_header (&$udm, $options) {
		$output ="<div class=side style=\"width:100%;text-align:center;padding-bottom:5px;padding-top:2px;background-color:#E5E5E5;\">";
		
		if ($options['qty_displayed']=="*") {$options['qty_displayed']=count($udm->results);}
		if (count($udm->results)>$options['qty_displayed']) {
		

			//PREV button
			if ($options['current_offset']>0) {
				$output .= "&nbsp;<a href=\"javascript: document.forms['".$options['list_form']."'].elements['offset'].value='";
				if ($options['current_offset']>$options['qty_displayed']) {
					$output.= $options['current_offset']-$options['qty_displayed']; 
				} else {
					$output.="0";
				}
				$output.="'; document.forms['".$options['list_form']."'].submit();\"><< Prev </a>";	
			}
			//NEXT button
			if (count($udm->results) > ($options['current_offset']+$options['qty_displayed'])){
				$output .= "&nbsp;&nbsp;<a href=\"javascript: document.forms['".$options['list_form']."'].elements['offset'].value=".($options['current_offset']+$options['qty_displayed'])."; document.forms['".$options['list_form']."'].submit();\">Next >></a>";
			}
		}
		
		//Current Location

		$output.="&nbsp;Showing ".$options['current_offset']."-";
		if (count($udm->results) < ($options['current_offset']+$options['qty_displayed'])) {
			$output .= count($udm->results);
		} else {
			$output .= ($options['current_offset']+$options['qty_displayed']);
		}
		$output.=" of ".count($udm->results);

		//Display Qty choice - convert the qty back to a * for listbox
		if ($options['qty_displayed']==count($udm->results)) {$options['qty_displayed']="*";}
		$output.="&nbsp;&nbsp;".list_display_qty_choice($options);
		if ($options['qty_displayed']=="*") {$options['qty_displayed']=count($udm->results);}

		if (count($udm->results)>$options['qty_displayed']) {
			//Go: Jumpto box
			$output.="&nbsp;".list_jumpto_box($udm, $options);
		}
		if ($options['allow_include_modins']&&$options['show_advanced_links']) {
			$output.="<BR><a href=\"javascript: 
			document.forms['".$options['list_form']."'].elements['UDM_Action'].value='Advanced_Modin'; document.forms['".$options['list_form']."'].submit();\" class=side>Show Sources</A>";
		}
		$output.="</div>";
		return $output;
	}
	
////Creates the HTML for the Go Box on Paginated Lists
	function list_jumpto_box (&$udm, $options) {
		$output="&nbsp;Go: <SELECT name=\"UDM_offset\" class=side onchange=\"document.forms['".$options['list_form']."'].elements['offset'].value=this.value; document.forms['".$options['list_form']."'].submit();\">";
		$sortfields = explode(",", $options['sort_by']);
		$sortfields[0]=str_replace(" DESC", " ", $sortfields[0]);
		$sortfields[0]=str_replace(" ASC", " ", $sortfields[0]);
		$mysort=trim($sortfields[0]);
		for ($n=0;$n<=count($udm->results); $n=$n+$options['qty_displayed']) {
			$output.="<option value=\"$n\"";
			if ($n==$options['current_offset']) {
				$output.=" selected";
			}
			$output.=">".list_translateFields($mysort, $udm, $options).": ".$udm->results[$n]["hide_sort_".$mysort]."</option>";
		}
		$output.="</select>";
		return $output;
	}

	///// CREATES the Display Quantity Select Box
	function list_display_qty_choice($options) {
		$output="Qty:&nbsp;<SELECT name=\"qty_selector[]\" onchange=\"document.forms['".$options['list_form']."'].elements['list_page_qty'].value=this.value;document.forms['".$options['list_form']."'].submit();\" class=\"name\">";
		$display_options['50']='50';
		$display_options['100']='100';
		$display_options['*']='All';
		foreach ($display_options as $dvalue => $dtext) {
			$output .= "<option value=\"".$dvalue."\"";
			if ($dvalue==$options['qty_displayed']) {
				$output .= " selected";
			}
			$output .=">".$dtext."</option>";
		}
		
		$output.="</select>";
		return $output;
	}

//UDM INSTANCE CONTROL PANEL

	function list_include_modin_box(&$udm, &$options) {
		if (isset($options['display_include_modin_box'])) {$modlist_style="block"; } else {$modlist_style="none";}
		if ($options['include_modin']=="*") {$is_selected=" CHECKED"; $modlist_style="none";}

		$modinbox="Currently listing <B>";
		if (isset($options['include_modin'])){
			if ($options['include_modin']=="*") {
				$modinbox.="All Sources";
			} else {
				$modinbox.=$udm->name;
				$modin_set=split(",", $options['include_modin']);
				foreach ($modin_set as $this_modin) {
					$modinbox.=", ".$options['Lookups']['modin']['LookupSet'][$this_modin];
				}
			}
		} else {
			$modinbox.=$udm->name;
		}
		
			
		$modinbox.="</b><BR><input name=\"UDM_include_all_modins\" type=\"checkbox\" value=\"1\" $is_selected>List users from all sources<BR><a href=\"javascript: void(0);\" onclick=\"change_any('UDM_list_modules')\">Select sources</a><br><div id=\"UDM_list_modules\" style=\"display: $modlist_style;\">Also include:<BR><SELECT MULTIPLE name=\"UDM_include_modins[]\" size=\"6\">".GetModsOptions(&$udm, &$options)."</select><br><input type=\"submit\" value=\"Go\"></DIV>";
		return $modinbox;
	}


	//Make an option list of modules
	function GetModsOptions(&$udm, &$options) {
		//Required Option: allowed modules string
		if ($options['allow_include_modins']) {
			if (!$options['allowed_modins']=="*"&&isset($options['allowed_modins'])) { 
				$allowed_modin_set=split(",", $options['allowed_modins']);
				$all_modin_set=array_keys($options['Lookups']['modin']['LookupSet']);
				print join(",", $all_modin_set);
				$allowed_modins=array_intersect($allowed_modin_set, $all_modin_set);
				foreach ($allowed_modins as $this_modin) {
					$allowed_modins[$this_modin]=$options['Lookups']['modin']['LookupSet'][$this_modin];
				}
				asort($allowed_modins);
				$output=makeSelbox($allowed_modins, split(",", $options['include_modin']));
			} elseif ($options['allowed_modins']=="*") {
				asort($options['Lookups']['modin']['LookupSet']);
				$output=makeSelbox($options['Lookups']['modin']['LookupSet'], split(",", $options['include_modin']));
			}
		} else {
			$output = "<option value=\"\">No Modules Defined or Permission Missing</option>";
		}
		return $output;
	}

	function makeSelbox ($data, $selected='') {
		if (is_array($data)) {
			//allow multiple selects
			if (is_array($selected)) {
				foreach ($data as $key => $value) {
					$output.="<option value='$key'";
					if (in_array($key,$selected)){
						$output.= " selected";
					}
					$output.=">$value</option>\n";
				}
			} else {
				//just one select
				foreach ($data as $key => $value) {
					$output.="<option value='$key'";
					if ($key==$selected&&($selected!='')){
						$output.= " selected";
					}
					$output.=">$value</option>\n";
				}
			}

		} else {
			$output="<option value=''>No Items Found</option>";
		}
		return $output;
	}

/*
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
*/

	//utility debug function to print the current Lookup Array
	function outputLookups($options){
		foreach ( $options['Lookups'] as $this_lookup) {
			foreach ($this_lookup as $key=>$lkvalue) {
				print $key.": ".$lkvalue."     ";
				if ($key=='LookupSet') {
					foreach ($lkvalue as $kkey =>$kvalue) {
						print "Set ($kkey : $kvalue )         ";
					}
				}
			}
			print "<BR>";
		}
	}


	function list_check_fields(&$udm, $options) {
		$display_fields =str_replace("Concat(First_Name, \" \", Last_Name) as Name,", "Name,", $options['display_fields']);
		$display_fieldset=split(",", $display_fields);
		foreach ($display_fieldset as $current_field) {
			$current_field=trim($current_field);
			if (isset($udm->fields[$current_field])) {
				if (!($udm->fields[$current_field]['public']==false&&$udm->admin==false)) {
					$return_fieldset[]=$current_field;
				}
			} else {
				switch ($current_field) {
					case "Name":
					if ($udm->admin) { $return_fieldset[]="Name";}
						elseif ($udm->fields['Last_Name']['public']&&$udm->fields['First_Name']['public']) {
							$return_fieldset[]="Name";
						} 
						break;
					case "id":
						if ($udm->admin) { $return_fieldset[]="id";}
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

	// converts system fieldnames to UDM-assigned fieldnames

	function list_translateFields($fieldname, &$udm, $options) {
		$returnField=strip_tags($udm->fields[$fieldname]['label']);
		if ($options['allow_lookups']) {
			if (isset($options['Lookups'][$fieldname]['LookupName'])){
				$returnField=$options['Lookups'][$fieldname]['LookupName'];
			}
		}

		if ($returnField==NULL) {$returnField=$fieldname;}
		return $returnField;
	}

	//returns Sort columns for inclusion in a SQL query
	function list_setupSort($options) {
		if($sort_set = explode(',', $options['sort_by'])) {
			$sort_set[0]=str_replace(" DESC", " " , $sort_set[0]);
			$sort_set[0]=str_replace(" ASC", " ", $sort_set[0]);
			$primary_sort=trim($sort_set[0]);
			$output = ", ".$primary_sort." as hide_sort_".$primary_sort;
		}
		return $output;
	}

	//retrieves Lookup values from database tables and stores them in the options array
	function list_setupLookups(&$udm, $options) {
		if (is_array($options['Lookups'])) {
			foreach($options['Lookups'] as $key=>$this_lookup) {
				if (isset($this_lookup['LookupTable'])) {
					$options['Lookups'][$key]['LookupSet']=$udm->dbcon->GetAssoc( "Select id, ".$this_lookup['LookupField']." FROM ".$this_lookup['LookupTable']);
				}
			}
		}
		return $options;
	}

	//returns the editlink value  for inclusion in the SQL query
	function list_setupEditlink($options) {
		$editlink_set=split(",", $options['editlink_fields']);
		if (is_array($editlink_set)) {
			$editlink="Concat( '".$options['editlink_action']."?";
		
			foreach ($editlink_set as $current_link) {
				if ($current_link=='id') { //hack to change id to uid
					$editlink.="u".$current_link."=', $current_link, '&";
				} else {
					$editlink.=$current_link."=', $current_link, '&";
				}
			}
			$editlink=substr($editlink, 0, strlen($editlink)-4);
			$editlink.=") as hide_editlink, id as hide_id ";
		} else {
			$editlink=$options['editlink_action']." AS hide_editlink, id as hide_id ";
		}
		$editlink= ", ".$editlink;
		return $editlink;
	
	}


///reads Pagination values posted from the form
	function list_readPagination($options) {
		//set page offset and display qty from form data
		global $_REQUEST;
		if (isset($_REQUEST['list_page_qty'])) {
			$options['qty_displayed']=$_REQUEST['list_page_qty'];
		} else {
			$options['qty_displayed']=$options['default_qty'];
		}
		if (isset($_REQUEST['offset'])) {
			$options['current_offset']=$_REQUEST['offset'];
		} else {
			$options['current_offset']=0;
		}
		if ($options['qty_displayed']=="*"){$options['current_offset']=0;}
		return $options;
	}


	function list_readDisplayFields($options) {
		//set fields for results list from form data
		global $_REQUEST;
		if (isset($_REQUEST['UDM_display_fields'])) {
			$options['display_fields']=stripslashes($_REQUEST['UDM_display_fields']);
		}
		if (isset($_REQUEST['UDM_list_fields'])) {
			$list_field_list="id, Concat(First_Name, \" \", Last_Name) as Name";
			foreach ($_REQUEST['UDM_list_fields'] as $current_field) {
				$list_field_list.=", ".$current_field;
			}
			$options['display_fields']=$list_field_list;
		}
		return $options;
	}

	function list_readModins($options) {
		//set fields for results list from form data
		global $_REQUEST;
		if (isset($_REQUEST['UDM_include_all_modins'])) {
			$options['include_modin']="*";
		} elseif (is_array($_REQUEST['UDM_include_modins'])) {
			$options['include_modin']=join(',', $_REQUEST['UDM_include_modins']);
		}
		return $options;
	}




	function list_readSort($options) {
		global $_REQUEST;
		//set sort fields from form data
		if (isset($_REQUEST['UDM_sort'])) {
			$sort_set=explode(",", $_REQUEST['UDM_sort']);
			$options['sort_by']="";
			foreach ($sort_set as $this_sort) {
				$this_sort=str_replace(" DESC", " " , $this_sort);
				$this_sort=str_replace(" ASC", " ", $this_sort);
				$this_sort=trim($this_sort);
				if (strpos($options['sort_by'], $this_sort)===FALSE) {
					//sort descending when sortfield appears in form data twice
					//but not if the sortfield is already DESC
					if (substr_count($_REQUEST['UDM_sort'], $this_sort)>1&&strpos($_REQUEST['UDM_sort'], $this_sort." DESC") === FALSE) {
						$options['sort_by'].=$this_sort." DESC, ";
					} else {
						$options['sort_by'].=$this_sort.", ";
					}
				}
			
			}
				
			$options['sort_by']=substr($options['sort_by'], 0, strlen($options['sort_by'])-2);
			
		}
		return $options;
		
	}

//reads the requested action from the form
	function list_readAction(&$udm, $options) {
		global $_REQUEST;
		if (isset($_REQUEST['UDM_Action'])) {
			switch ($_REQUEST['UDM_Action']) {
				case 'Publish' : $options=list_publish($udm, $options, $_REQUEST['id']); break;
				case 'Unpublish': $options=list_unpublish($udm, $options, $_REQUEST['id']); break;
				case 'Delete':$options=list_delete($udm, $options, $_REQUEST['id']);break;
				case 'Advanced_Modin':$options['show_advanced_modin']=true;
				break;
			}
		}
		if (isset($_REQUEST['list_editlink_action'])) { 		
			$options['editlink_action']=$_REQUEST['list_editlink_action'];}
		if (isset($_REQUEST['list_show_adv_modin'])) { 		
			$options['show_advanced_modin']=true;}

		
		return $options;
	}


//Builds a form to call the Email Blaster or the Export page
	function list_emailBlastConnect (&$udm, $options) {
		$output='<form name="UDM_email" method="post" action="'.$options['email_action'].'">
  				  <input type="hidden" name="start_sql" value=" from userdata where Email != \'\' and !(isnull(Email)) and modin = '.$udm->instance.' ">
				<input type="hidden" name="start_export_sql" value=" from userdata where modin = '.$udm->instance.' ">
				<input type="hidden" name="sqlp" value="">				
				  <input type="hidden" name="emailfrom" value="'.$options['user_email'].'">
				  <input type="hidden" name="emailname" value="'.$options['user_name'].'">
  					<input type="hidden" name="id" value="'.$udm->name.'">
   					<input type="hidden" name="modin" value="'.$udm->instance.'">
			</form>';
		return $output;


	}










//publish a set of ids
function list_publish(&$udm, $options, $ids) {
	if (is_array($ids)) {
		$id_set=join(',', $ids);
		$q = "update ".$options['usertable']." set publish=1 where id IN($id_set)";
		$udm->dbcon->execute($q) or die($dbcon->errorMsg());
		$options['message']='Selected items posted live.';

	}
	return $options;
}

function list_unpublish(&$udm, $options, $ids) {
	if (is_array($ids)) {
		$id_set=join(',', $ids);
		$q = "update ".$options['usertable']." set publish=0 where id IN($id_set)";
		$udm->dbcon->execute($q) or die($dbcon->errorMsg());
		$options['message']='Selected items unpublished.';
	}
	return $options;
}


/**
 * a function to take an array of IDs and delete them
 */
function list_delete(&$udm, $options, $ids) {
	if (is_array($ids)) {
		$id_set=join(',', $ids);
		$q = "delete from ".$options['usertable']." where id IN($id_set)";
		$udm->dbcon->execute($q) or die($dbcon->errorMsg());
		$options['message']='Selected items deleted.';
	}
	return $options;
}

/**
 * a function to to do a header redirect, you can feed it an option associative array to build a query string
 */
function list_send_to($loc, $query=null) {
global $_POST;
	if (is_array($query)) {
		$q = '?';
		foreach ($query as $k=>$v) {
			$q .= "$k=$v&";
		}
	}
	$modin = "&modin=".$_POST[modin];
	header("location:$loc$q$modin ");
}

	
	
	



?>