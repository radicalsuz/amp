<?php
require_once ('AMP/UserData/Plugin.inc.php');
require_once ('AMP/Region.inc.php');

class UserDataPlugin_TableHTML_Output extends UserDataPlugin {
    
    var $options= array( 
		'list_row_start_template'   
                            =>  array( 'value' => "<tr id=\"listrow_%s\" bordercolor=\"#333333\" bgcolor=\"%s\" class=\"results\" onMouseover=\"this.bgColor='#CCFFCC';\" onMouseout=\"this.bgColor='%s';\" onClick=\"select_id(this.id.substring(8));\">" ),
		'list_row_end'      =>  array( 'value' => "</tr>\n" ),
		'list_item_start'   =>  array( 'value' => "<td class=list_column_%s>"),
		'list_item_end'     =>  array( 'value' => "</td>"),
		'list_html_start'   =>  array( 'value' => '<center><table cellpadding="1" cellspacing="1" width="95%"><tr class="toplinks">'),
		'list_html_footer'  =>  array( 'value' => "</table></center></form>"),
		'list_html_header_column_start'
                            =>  array( 'value' => "<td align=\"left\">"),
		'list_html_header_template'
                            =>  array( 'value' => '<td align="left"><b>%s</b></td>'),
        'form_name'         =>  array( 'value' => 'udm_list'),
                #"<td align=\"left\"><b><a href=\"javascript: document.forms['%1\$s'].elements['sortby'].value = '%2\$s '+document.forms['%1\$s'].elements['sortby'].value; document.forms['%1\$s'].submit();\">%3\$s</a></b></td>"),
        'display_fields'    =>  array( 
            'label'   => 'Select SQL phrase for display fields',
            'type'    => 'textarea',
            'available' => true,
            'default' => 'id,Name,Company,State,Phone,Status'),
        'display_format'    =>  array( 
            'label'   => 'Output method for each row',
            'available' => true,
            'type' => 'text',
            'default' => 'table_format'),
        'editlink'          =>  array( 
            'label' => 'System page to link to',
            'available' => true,
            'type'  => 'text',
            'default' => 'modinput4_view.php')
        );
    var $html_rowcount=0;
    var $display_fieldset;
    var $available=true;
    var $Lookups;
    var $list_row_select;
    var $list_row_edit;

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
                'f_orderby'=>'(if(Country="USA",1,if(Country="CAN",2,if(isnull(Country),3,Country)))),State,City,Company',
                'f_type'=>'text'),
             'Status'=>array(
                'f_alias'=>'Status',
                'f_orderby'=>'publish',
                'f_type'=>'text',
                'f_sqlname'=>'if(publish=1,"Live","Draft")'
              ));

    function UserDataPlugin_TableHTML_Output (&$udm, $plugin_instance=null) {   
        $this->init($udm, $plugin_instance);
    }


    function execute ($options=array( )) { 
        $options = array_merge($this->getOptions(), $options);

        //create fieldset
        $this->display_fieldset=preg_split('/\s?,\s?/', $options['display_fields']);

        //Print the current results list
        if (!($dataset=$this->udm->getData())) return false;
        
        $display_function=isset($options['display_format'])?($options['display_format']):"display_item";
        $inclass=method_exists($this, $display_function);

        //Start Output
        $output='<FORM name="'.$options['form_name'].'" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
        if ($this->udm->admin) $output.=$this->select_script($options['form_name']);
        $output.=$this->column_headers($options);

        //output display format
        foreach ($dataset as $dataitem) {
            if (isset($options['subheader'])) $output.=$this->subheader($dataitem);
            if($inclass) $output.=$this->$display_function($dataitem);
            else $output.=$display_function($dataitem, $options);
        }

		return $options['list_html_start'].$output.$options['list_html_footer'];
    }

    function _register_options_dynamic(){
        $options = $this->getOptions();
        if ($this->udm->admin) { //make editing permission available
            $this->list_row_select.=sprintf($options['list_item_start'], 'ROWSELECT_box').
                "<input name=\"id[]\" type=\"checkbox\" value=\"%s\" onclick=\"this.checked=!this.checked;\">".
                $options['list_item_end'];
            $this->list_row_edit=sprintf($options['list_item_start'],'editlink').
                "<a href=\"".$options['editlink']."?uid=%s&modin=".$this->udm->instance."\">edit</a>".$options['list_item_end'];
        }
    }


    function column_headers($options=array( )) {
        $list_html_headers = "";
        foreach ($this->display_fieldset as $key) {
            if ($sort_set= $this->udm->getPlugins('Sort')) {
                $sort_obj=&$sort_set[key($sort_set)];

                $display_name = false;
                if ( isset( $this->udm->fields[$key]) && isset( $this->udm->fields[$key]['label'])) {
                    $display_name = $this->udm->fields[ $key ]['label'];
                } 
                $key=$sort_obj->makelink($key, $display_name );
            }
            $list_html_headers.=sprintf($options['list_html_header_template'], $key);
        }
        if ($this->udm->admin) { //include columns headers for select and edit cols
            $list_html_headers=$options['list_html_header_column_start'].
                    "<a href=\"javascript: list_selectall();\"><B>All</B></a>".
                    $options['list_item_end'].$list_html_headers;
            $list_html_headers.=$options['list_item_start'].$options['list_item_end'];
        }
        return $list_html_headers;
    }

    function table_format($current_row, $options=array( )) {
        if (empty($options)) $options = $this->getOptions();
        $this->html_rowcount++;

        //assigns an id and background color to each row
        $bgcolor =($this->html_rowcount % 2) ? "#D5D5D5" : "#E5E5E5";
        $list_row="";
        $list_row_start=sprintf($options['list_row_start_template'], $current_row['id'], $bgcolor, $bgcolor);

        foreach($this->display_fieldset as $key) {
            $kvalue = false;
            //Check for values swapped by Lookup ( legacy, probably doesn't work 2006-10 AP )
            if (isset($this->Lookups[$key])) {
                $kvalue=$this->Lookups[$key]['Set'][$current_row[$key]];
            } 
            // swap values from active lookup def
            if ( isset( $this->udm->fields[$key]['lookup'] ) && is_object( $this->udm->fields[$key]['lookup']))  {
                if ( $values = $this->udm->fields[$key]['lookup']->dataset ) {
                    if ( isset( $values[ $current_row[ $key ]])) {
                        $kvalue = $values[ $current_row[ $key ]];
                    }
                }
            } 
            
            if ( !$kvalue ) {
                $kvalue=$current_row[$key];
            }
            //Main Field Format Statement
            $list_row.=sprintf($options['list_item_start'], $key).
                        $kvalue.$options['list_item_end'];
        }
        //Admin View Formatting
        if ($this->udm->admin) {
            $list_row=sprintf($this->list_row_select, $current_row['id']).$list_row;
            $list_row.=sprintf($this->list_row_edit, $current_row['id']);
        }
        return $list_row_start.$list_row.$options['list_row_end'];

    }

    function setAliases() {
        $this->udm->alias = $this->alias;
    }


    function select_script($form_name) {

		$script="<script type=\"text/javascript\">
		
		var sform=document.forms['".$form_name."'];";

		if ($this->udm->admin) { $script.="
		
		//Javascript function to select all/deselect all on a given page
		var sel_action='Select All';

		function list_selectall() {
			sform=document.forms['".$form_name."']; 
			t=document.forms['".$form_name."'].length;
			if (sel_action=='Select All') {
				sel_action='Unselect All';
				var tvalue=true;
			} else {
				sel_action='Select All';
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
			sform=document.forms['".$form_name."']; 
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
			sform=document.forms['".$form_name."'];
			t = sform.length;
			for (n=0; n<t; n++){
				//alert(sform.elements[n].name);
				if (sform.elements[n].name.substring(0,2)=='id'&&sform.elements[n].value==find_id) {
					sform.elements[n].checked=!sform.elements[n].checked;
					return;
				}
			}
		}
		</script>";

		}
        return $script;
    }
}



?>
