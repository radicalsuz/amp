<?php
require_once ('Modules/Calendar/Plugin.inc.php');
#require_once ('HTML/QuickForm.php');

class CalendarPlugin_Actions_Output extends CalendarPlugin {

    var $action;
    var $form_def;
    var $form;
    var $criteria;
    var $options = array (
        'allow_email'=>array(
            'default'=>false,
            'type'=>'checkbox',
            'available'=>false,
            'description'=>'Allow user to Email event contact'),
        'allow_delete'=>array(
            'default'=>true,
            'type'=>'checkbox',
            'available'=>true,
            'description'=>'Allow user to Delete event records'),
        'allow_export'=>array(
            'default'=>false,
            'type'=>'checkbox',
            'available'=>true,
            'description'=>'Allow user to Export event records'),
        'allow_publish'=>array(
            'default'=>true,
            'type'=>'checkbox',
            'available'=>true,
            'description'=>'Allow user to Publish event records'),
        'allow_unpublish'=>array(
            'default'=>true,
            'type'=>'checkbox',
            'available'=>true,
            'description'=>'Allow user to UnPublish event records'),
        'form_name'=>array(
            'default'=>'calendar_actions',
            'type'=>'text',
            'available'=>true,
            'description'=>'Name of Action Form'),
        'control_class'=>array(
            'default'=>'go',
            'type'=>'text',
            'available'=>true,
            'description'=>'Class used for list controls')
        );


    function CalendarPlugin_Actions_Output (&$calendar, $plugin_instance=null) {
        $this->init ($calendar, $plugin_instance);
        $this->form_def=$this->define_form();
        $this->read_request();
    }
    function _register_options_dynamic() {
        if ($this->calendar->admin) $this->options['control_class']['value']='list_controls';
    }

    function read_request($options=array()) {
        $options=array_merge($this->getOptions(), $options);
        
        if (isset($_POST['list_action'])&&$_POST['list_action']&&$options['allow_'.$_POST['list_action']]) {
            
            $this->action=$_POST['list_action'];
            $action=$this->action.'_set';
            
            if (method_exists($this, $action)) {

                $count=$this->$action($_POST['list_action_id']);

                if (is_numeric($count)) {
                    $this->calendar->error="$count item(s) ".$this->action.(strrchr($this->action, "e")=="e"?"":"e")."d.<BR>";
                } else {
                    $this->calendar->error=$count;
                }
            }
        }
    }

    /***
    ** setCriteria()
    ** Captures the values in the current Query String
    ** so the current searchdata/page won't be lost when a list action is taken
    **
    ** accepts criteria vetted by the searchform / pager plugins when such exist
    **/

    function setCriteria() {
        if ($searchform=$this->calendar->getPlugin('Output','SearchForm')) {
            //if a SearchForm plugin is used, use that to validate the query
            //string
           $this->criteria=$searchform->url_criteria;
        } else {
            //otherwise pass the whole thing along
            parse_str($_SERVER['QUERY_STRING'], $parsed_criteria);
            foreach ($parsed_criteria as $pkey=>$pvalue) {

                if (isset($pvalue)&&($pvalue||$pvalue==='0')) {

                    if ($pkey!='offset'&&$pkey!='qty') {
                        $this->criteria[]=$pkey.'='.$pvalue;
                    }
                }
            }
        }
        if ($pager=$this->calendar->getPlugin('Output','Pager')) {
            if ($pager->offset) $this->criteria[]='offset='.$pager->offset;
            if ($pager->return_qty) $this->criteria[]='qty='.$pager->return_qty;
        }
    }

    function define_form($options=array()) {
        $options=array_merge($this->getOptions(), $options);

        //Each of these sets the List_Action value of the form and submits the form
        //Selected items are posted in id

        //Intro text
        $def['intro']=array('type'=>'static',
                            'label'=>'<span class="'.$options['control_class'].'"><B>With Selected:</B></span>',
                            'enabled'=>true,
                            'public'=>true);
        
        //Publish Button
        $def['publish']=array(  
                                'type'=>'button',
                                'label'=>' Publish',
                                'attr'=>
                                    array('class'=>$options['control_class'],
                                    'onClick'=>"list_DoAction( 'publish' );"),
                                'enabled'=>$options['allow_publish']);
        //Unpublish Button
        $def['unpublish']=array('type'=>'button',
                                'label'=>'Unpublish',
                                'attr'=>
                                    array('class'=>$options['control_class'],
                                    'onClick'=>"list_DoAction( 'unpublish' );"),
                                'enabled'=>$options['allow_publish']);
        //Delete Button
        $def['delete']=array(  'type'=>'button',
                                'label'=>'Delete',
                                'attr'=>
                                    array('class'=>$options['control_class'],
                                    'onClick'=>"list_DoAction( 'delete' );"),
                                'enabled'=>($this->calendar->admin&&$options['allow_delete']));
        //Export Button
        $def['export']=array(  'type'=>'button',
                                'label'=>'Export',
                                'attr'=>
                                    array('class'=>$options['control_class'],
                                    'onClick'=>"list_DoAction( 'export' );"),
                                'enabled'=>$options['allow_export']);
        //Email Button
        $def['email']=array(  'type'=>'button',
                                'label'=>'Email',
                                'attr'=>
                                    array('class'=>$options['control_class'],
                                    'onClick'=>"list_DoAction( 'email' );"),
                                'enabled'=>$options['allow_email']);
        //list_action form items
        $def['list_action']=array(  'type'=>'hidden',
                                'value'=>'',
                                'enabled'=>true);
        $def['list_action_id']=array(  'type'=>'hidden',
                                'value'=>'',
                                'enabled'=>true);
        $def['list_return_url']=array(  'type'=>'hidden',
                                'value'=>'',
                                'enabled'=>true);
		return $def;
	
	}

    function action_script($options=null) {
        $script='
        <script type="text/javascript">
        function list_DoAction( action ) {
            aform = document.forms["'.$options['form_name'].'"];
            id_val=list_return_selected();
            if (action=="email") return (list_DoEmail(id_val));
            if (id_val.length==0) {
                alert ("No record is selected to "+action);
                return false;
            } else {
                aform.elements["list_action"].value = action;
                aform.elements["list_action_id"].value = id_val; 
                aform.submit();
            }
        }';

        if ($options['allow_email'])  {
            $script .= '
            function list_DoEmail(id_val) {
                aform = document.forms["'.$options['form_name'].'"];
                if (id_val.length==0) {
                    response= (confirm("No email address is selected.\\n Send email to entire list?"));
                    if (response==false) return false;
                }
                aform.elements["list_return_url"].value = aform.action;
                aform.elements["list_action"].value = "email";
                aform.elements["list_action_id"].value = id_val; 
                aform.action="calendar_email.php";
                aform.submit();
            }
        ';
    }
                    
                    

    $script.='</script>';
    return $script;
}

function execute ($options=null) {
    if (!isset($options)) {
        if ($this->executed) return false;
        $options=$this->getOptions();
    } else {
        $options=array_merge($this->getOptions(), $options);
        $this->form_def=$this->define_form($options);
    }

    $this->setCriteria();

    $frmName    = $options['form_name']; 
    $frmMethod  = 'POST';
    $frmAction  =   $_SERVER['PHP_SELF'].'?'.(is_array($this->criteria)?join("&",$this->criteria):"") ;

    $form = &new HTML_QuickForm( $frmName, $frmMethod, $frmAction );

    foreach ($this->form_def as $fname=>$fdef) {
        $this->form_addElement( $form, $fname, $fdef, $this->calendar->admin );
    }
            
    $this->form = &$form;
    $output=$this->action_script($options);
    if ($email_plugin=$this->calendar->getPlugin('Output','EmailForm')){
        $output .= $this->calendar->output('EmailForm');
    }
    $output.=$form->toHtml();
    
    return  $output;

}
    
    
function publish_set($set) {
    $id_set=split(",", $set);
    if (is_array($id_set)) {
        $sql="UPDATE calendar set publish=1 where id in(".$set.");";
        if ($this->dbcon->execute($sql)) {
            return $this->dbcon->Affected_Rows();
        } else {
            return "Publish Failed: ".$this->dbcon->ErrorMsg();
        }
    } else {
        return "No items selected to publish";
    }
}
    
function unpublish_set($set) {
    $id_set=split(",", $set);
    if (is_array($id_set)) {
        $sql="UPDATE calendar set publish=0 where id in(".$set.");";
        if ($this->dbcon->execute($sql)) {
            return $this->dbcon->Affected_Rows();
        } else {
            return "UnPublish Failed: ".$this->dbcon->ErrorMsg();
        }
    } else {
        return "No items selected to unpublish";
    }
}
    
function delete_set($set) {
    $id_set=split(",", $set);
    if (is_array($id_set)) {
        $sql="DELETE FROM calendar where id in(".$set.");";
        if ($this->dbcon->execute($sql)) {
            return $this->dbcon->Affected_Rows();
        } else {
            return "Delete Failed: ".$this->dbcon->ErrorMsg();
        }
    } else {
        return "No items given to delete.";
    }
}

function email_set ($set) {
    if ($this->calendar->registerPlugin('Output','EmailForm')) {
        return "Please compose your message below";
    } else {
        return "Sorry, the Email feature isn't available";
    }
}

function form_addElement( &$form, $name, &$field_def, $admin = false ) {

    if ( $field_def[ 'public' ] != 1 && !$admin ) return false;
    if ( $field_def[ 'enabled' ] != 1) return false;

    $type     = $field_def[ 'type'   ];
    $label    = $field_def[ 'label'  ];
    $defaults = $field_def[ 'values' ];
    $size     = $field_def[ 'size' ];
    $renderer =& $form->defaultRenderer();

    // Check to see if we have an array of values.
    if (!is_array($defaults)) {
        $defArray = explode( ",", $defaults );
        if (count( $defArray ) > 1) {
            $defaults = array();
            foreach ( $defArray as $option ) {
                $defaults[ $option ] = $option;
            }
        } else {
            $defaults = $defArray[0];
        }
    }			

    
    // Add a default blank value to the select array.
    if ( $type  == 'select' && is_array( $defaults ) ) {
        //Move label into select box for non colonned entries.
        if (substr($label, strlen($label)-1)!=":") {
            $defaults = array('' => $label) + $defaults;
            $label="";
        } 
        if ($field_def['value']&&isset($field_def['value'])) $selected=$field_def['value'];
    }
    
    //add the element
    $form->addElement( $type, $name, $label, $defaults );

    //get the element reference
    $fRef =& $form->getElement( $name );
    $fRef->updateAttributes($field_def['attr']);
    if ( isset( $selected ) ) {
        $fRef->setSelected( $selected );
    }


    if ($type=='static') {
            $renderer->setElementTemplate("{label}", $name);
    } elseif ($type=='submit') {
        $renderer->setElementTemplate("{element}", $name);
    } else {
            $renderer->setElementTemplate("\n\t\t<span align=\"right\" valign=\"top\" class=\"".$this->control_class."\">{label} {element}\n\t", $name);
    }
    
    
    return 1;
}
}

?>
