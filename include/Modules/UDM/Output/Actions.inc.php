<?php
require_once ('AMP/UserData/Plugin.inc.php');
require_once ('HTML/QuickForm.php');

class UserDataPlugin_Actions_Output extends UserDataPlugin {

    var $action;
    var $form_def;
    var $form;
    var $available=true;
    var $criteria;
    var $message;
    var $options = array (
        'allow_email'=>array(
            'default'=>true,
            'type'=>'checkbox',
            'available'=>false,
            'label'=>'Allow user to Email list'),

        'allow_delete'=>array(
            'default'=>true,
            'type'=>'checkbox',
            'available'=>true,
            'label'=>'Allow user to Delete records'),

        'allow_export'=>array(
            'default'=>true,
            'type'=>'checkbox',
            'available'=>true,
            'label'=>'Allow user to Export records'),

        'allow_publish'=>array(
            'default'=>true,
            'type'=>'checkbox',
            'available'=>true,
            'label'=>'Allow user to Publish records'),
            
        'allow_unpublish'=>array(
            'default'=>true,
            'type'=>'checkbox',
            'available'=>true,
            'label'=>'Allow user to UnPublish records'),

        'allow_subscribe'=>array(
            'default'=>true,
            'type'=>'checkbox',
            'available'=>true,
            'label'=>'Allow subscribing to Email lists'),


        'form_name'=>array(
            'default'=>'udm_actions',
            'type'=>'text',
            'available'=>true,
            'label'=>'Name of Action Form'),

        'control_class'=>array(
            'default'=>'go',
            'type'=>'text',
            'available'=>true,
            'label'=>'CSS class used for list controls')
        );


    function UserDataPlugin_Actions_Output (&$udm, $plugin_instance) {
        $this->init ($udm, $plugin_instance);
        $this->form_def=$this->define_form();
        $this->read_request();
    }
    function _register_options_dynamic() {
        if ($this->udm->admin) {
            $this->options['control_class']['value']='list_controls';
        }
    }

    function read_request($options=array()) {
        $options=array_merge($this->getOptions(), $options);
        
        if (isset($_POST['list_action'])&&$_POST['list_action']&&$options['allow_'.$_POST['list_action']]) {
            
            $this->action=$_POST['list_action'];
            $action=$this->action.'_set';
            
            if (method_exists($this, $action)) {

                $count=$this->$action($_POST['list_action_id']);

                if (is_numeric($count)) {
                    $message = "$count item(s) ".$this->action.(strrchr($this->action, "e")=="e"?"":"e")."d.<BR>";
                } else {
                    $message = $count;
                }

                $this->message = $message;
            }
        }
    }

    /***
    ** setCriteria()
    ** Captures the values in the current Query String
    ** so the current searchdata/page won't be lost when a list action is taken
    **
    ** accepts criteria vetted by the pager plugins when such exist
    **/

    function setCriteria() {

        if (!isset($this->udm->url_criteria)) $this->criteria=$this->udm->parse_URL_crit(); 
        else $this->criteria=$this->udm->url_criteria;
        
        if ($pager_set=&$this->udm->getPlugins('Pager')) {
            $pager = current($pager_set);

            if ($pager->offset) $this->criteria[]='offset='.$pager->offset;
            if ($pager->return_qty) $this->criteria[]='qty='.$pager->return_qty;
        }
        return $this->criteria;
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
                                'label'=>'Publish',
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
        $def['delete']=array(   'type'=>'button',
                                'label'=>'Delete',
                                'attr'=>
                                    array('class'=>$options['control_class'],
                                    'onClick'=>"list_DoAction( 'delete' );"),
                                'enabled'=>($this->udm->admin&&$options['allow_delete']));
        //Export Button
        $def['export']=array(   'type'=>'button',
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
        //Subscribe Options
        $def['blastlist_id'] = array(  'type' =>'select',
                                'label' =>'Subscribe to',
                                'enabled'=>AMP_authorized( AMP_PERMISSION_BLAST_ACCESS ),
                                'attr'=>
                                    array('class'=>$options['control_class']),
                                'values' => AMPSystem_Lookup::instance( 'lists'));
        //Subscribe Button
        $def['subscribe']=array(  'type'=>'button',
                                'label'=>'Subscribe',
                                'attr'=>
                                    array('class'=>$options['control_class'],
                                    'onClick'=>"list_DoAction( 'subscribe' );"),
                                'enabled'=>AMP_authorized( AMP_PERMISSION_BLAST_ACCESS ));
        //list_action form items
        $def['list_action']=array(  'type'=>'hidden',
                                'value'=>'',
                                'enabled'=>true);
        $def['list_action_id']=array(  'type'=>'hidden',
                                'value'=>'',
                                'enabled'=>true);
        $def['sqlp']=array(  'type'=>'hidden',
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
                var al_msg ="No record is selected to "+action;
                if (action=="export") {

                    if (confirm ( al_msg + "\nExport entire list?")) {
                        aform.elements["list_action"].value = action;
                        aform.submit();
                    } else {
                        return false;
                    }

                } else {

                    alert (al_msg);
                    return false;

                }
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
                    aform.elements["sqlp"].value = "from userdata '.(is_array($this->udm->sql_criteria)?"WHERE ".join(" AND ", $this->udm->sql_criteria):"") .'"; 
                } else {
                    aform.elements["sqlp"].value = "from userdata where id in(" + id_val + ") "; 
                }
                aform.elements["list_return_url"].value = aform.action;
                aform.elements["list_action"].value = "email";
                aform.elements["list_action_id"].value = id_val; 
                aform.action="udm_mailblast.php?modin='.$this->udm->instance.'";
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
        $frmAction  = $_SERVER['PHP_SELF'].'?'.(is_array($this->criteria)?join("&",$this->criteria):"") ;

        $form = &new HTML_QuickForm( $frmName, $frmMethod, $frmAction );

        foreach ($this->form_def as $fname=>$fdef) {
            $this->form_addElement( $form, $fname, $fdef, $this->udm->admin );
        }

        $this->form = &$form;
        $output = $this->action_script($options);

        /*
           if ($email_plugin=$this->udm->getPlugin('Output','EmailForm')){
           $output .= $this->udm->output('EmailForm');
           }*/

        $output .= $form->toHtml();
        $output .= (isset($this->message) ? "<span class='page_result'>" . $this->message."</span><BR>" : "");

        return  $output;

    }


    function publish_set($set) {
        $id_set=split(",", $set);
        if (is_array($id_set)) {
            $sql="UPDATE userdata set publish=1 where id in(".$set.");";
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
            $sql="UPDATE userdata set publish=0 where id in(".$set.");";
            if ($this->dbcon->execute($sql)) {
                return $this->dbcon->Affected_Rows();
            } else {
                return "UnPublish Failed: ".$this->dbcon->ErrorMsg();
            }
        } else {
            return "No items selected to unpublish";
        }
    }

    function export_set($set=null) {

        $criteria = $this->udm->getURLCriteria();

        if (isset($set) && $set) {
            $set_values = split(",", $set);
            $criteria[] = 'uid[]=' . join('&uid[]=', $set_values);
        }

        $url_vals = join("&", $criteria);

        $target = '/system/form_export.php?'.$url_vals;
        $metatag ='<META http-equiv="refresh" content="2; URL='.$target.'">';
        $message = "Your download should begin within one minute.<BR>
                    If it does not, please <a href=\"$target\">click here</a>.";
        return $metatag.$message;
        

    }

    function delete_set($set) {
        $id_set=split(",", $set);
        if (is_array($id_set)) {
            $sql="DELETE FROM userdata where id in(".$set.");";
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
        if ($this->udm->registerPlugin('Output','EmailForm')) {
            return "Please compose your message below";
        } else {
            return "Sorry, the Email feature isn't available";
        }
    }

    function subscribe_set( $set, $api = null ){
        if ( !( isset( $_POST['blastlist_id'] ) && $list_id = $_POST['blastlist_id'])) return 'Please select a list';
        $ids = split( ",", $set );
        $emailSet = &AMPSystem_Lookup::instance( 'userDataEmails');
        $new_subscribers = &array_combine_key( $ids, $emailSet );

		if(AMP_MODULE_BLAST == 'PHPlist') {
			require_once( 'Modules/Blast/API.inc.php');
			$_PHPlist = &new PHPlist_API( $this->dbcon );
			$count = $_PHPlist->add_subscribers( $new_subscribers, $list_id );
		} elseif(AMP_MODULE_BLAST == 'DIA') {
			require_once('DIA/API.php');
			if(!isset($api)) {
				$api =& DIA_API::create();
			}
			$result = $api->addMembersByEmail($new_subscribers, $list_id);
			$count = sizeof($result);
		}
        $listSet = &AMPSystem_Lookup::instance( 'lists');
        return ( $count . ' users subscribed to '.$listSet[$list_id]);
    }

    function form_addElement( &$form, $name, &$field_def, $admin = false ) {
        $options = $this->getOptions( );

        if (  !( isset( $field_def['public']) && $field_def[ 'public' ] ) && !$admin ) return false;
        if (  !( isset( $field_def['enabled']) && $field_def[ 'enabled' ] ) ) return false;

        $type     = isset( $field_def['type']) ? $field_def[ 'type'   ]:'';
        $label    = isset( $field_def['label']) ? $field_def[ 'label'  ] : '';
        $defaults = isset( $field_def['values']) ? $field_def[ 'values' ] : null; 
        $size     = isset( $field_def['size']) ? $field_def[ 'size' ]:null;
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
            if ( isset($field_def['value']) && $field_def['value'] ) $selected = $field_def['value'];
        }

        //add the element
        $form->addElement( $type, $name, $label, $defaults );

        //get the element reference
        $fRef =& $form->getElement( $name );
        if ( isset( $field_def['attr'])) $fRef->updateAttributes($field_def['attr']);
        if ( isset( $selected ) ) {
            $fRef->setSelected( $selected );
        }


        if ($type=='static') {
            $renderer->setElementTemplate("{label}", $name);
        } elseif ($type=='submit') {
            $renderer->setElementTemplate("{element}", $name);
        } else {
            $renderer->setElementTemplate("\n\t\t<span align=\"right\" valign=\"top\" class=\"".$options['control_class']."\">{label} {element}\n\t", $name);
        }


        return 1;
    }
}

?>
