<?php
require_once ('AMP/UserData/Plugin.inc.php');
#require_once ('HTML/QuickForm.php');

class UserDataPlugin_EmailForm_Output extends UserDataPlugin {
    var $form;
    var $options = array(
        'form_name'=>array(
            'description'   =>'Name of Email Form',
            'available'     =>false,
            'value'         =>'udm_email'),
        'field_order'=>array(
            'description'   =>'Order of Fields, User Side',
            'available'     =>true,
            'value'         =>'email_from,endline,email_from_name,endline,email_from_text,endline,email_subject,endline,message,endline,email_send,email_cancel,uid,otp,list_action_id'),
        'field_order_admin'=>array(
            'description'   =>'Order of Fields, Admin View',
            'available'     =>true,
            'value'         =>'email_from,endline,email_from_name,endline,email_from_text,endline,email_subject,endline,message,endline,email_send,email_cancel,uid,otp,list_action_id'),
        'email_from'=>array(
            'description'   =>'Email From Address',
            'available'     =>true,
            'default'       =>'user@domain.com'),
        'email_from_name'=> array (
            'description'   =>'Email From Name',
            'available'     =>true,
            'default'       =>'Current User'),
            
        'control_class'=> array (
            'description'   =>'css class for form controls',
            'available'     =>true,
            'default'       =>'go')
            );

    function UserDataPlugin_EmailForm_Output (&$udm, $plugin_instance) { 
        $this->init($udm, $plugin_instance);
        $this->form_def=$this->define_form();
        $this->read_request();
    }
    function read_request() {
    }

    function _register_options_dynamic() {
        global $SiteName, $MM_email_from;
        if ($this->udm->admin) {
            $this->options['email_from']['value']=$MM_email_from;
            $this->options['email_from_name']['value']=$SiteName;
            $this->options['control_class']['value']='list_controls';
        }
    }

    function action_script($options) {
		$script.="<script type=\"text/javascript\">
			function sendTestEmail() {
				var eform = document.forms['".$options['form_name']."'];
                if ( eform.elements[ 'test_address' ] != '' ) {
                    eform.elements['test_only']=1;
                    eform.submit();
                } else {
                    alert ('Please enter an address to send a test copy of this email');
                }
				//eform.elements['list_email_send_btn'].value='Send this Email';
				//event(eform.elements['list_email_send_btn'], 'onclick', 'sendEmail();');
				
			}	
			function cancel_Email() {
				var eform_div=document.getElementById('List_Email_Form');
				eform_div.style.display='none';
			}

			function sendEmail() {
					alert ('now I\\'m gonna send something');
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
				
			</script>";
        return $script;
    } 
        
	function execute($options) {
        if (!isset($options)) {
            $options=$this->getOptions();
        } 

		$frmName    = $options['form_name']; 
		$frmMethod  = 'POST';
		$frmAction  =   $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

	    $form = &new HTML_QuickForm( $frmName, $frmMethod, $frmAction );

		if ( isset( $this->form_def[ 'field_order' ] ) ) {
		
			$fieldOrder = split( ',', $this->form_def[ 'field_order']  );
			
			foreach ( $fieldOrder as $field ) {
				$field = trim( $field );
                if (isset($this->form_def[$field])) {
                    $this->form_addElement( &$form, $field, $this->form_def[ $field ], $this->udm->admin );
                }
			}

		} else {
            foreach ($this->form_def as $fname=>$fdef) {
                $this->form_addElement( &$form, $fname, $fdef, $this->udm->admin );
            }
        }
                
		$this->form = &$form;
		$output="<div id='List_Email_Form' style=\"display: block;\" >";
        $output.=$this->action_script($options);
        $output.= $this->form->toHtml();
        $output.="</div>";
		return $output;
	}

    function define_form() {
        $options=$this->getOptions();
		$def['field_order']=$options['field_order'];
		
		if ($this->udm->admin) {
			$options->control_class='list_controls'; 
			$def['field_order']=$options['field_order_admin'];
		} else {
			$this->control_class=$options['control_class']; 
		}
        $def['email_subject'] = array (
            'type'  =>'text',
            'label' =>'Subject: ',
            'size'  => 20,
            'enabled'=>true,
            'public'=>true );
        $def['email_send'] = array (
            'type'  =>'button',
            'label' =>'Send Email',
            'attr'  =>array('class'=>$options['control_class'], 'onClick'=>'sendTestEmail();'),
            'enabled'=>true,
            'public'=>true);
        $def['email_cancel'] = array (
            'type'  =>'button',
            'label' =>'Cancel',
            'attr'  =>array('class'=>$options['control_class'], 'onClick'=>'cancel_Email();'),
            'enabled'=>true);
        $def['email_from'] = array (
            'type'  => 'text',
            'label' => 'From: ',
            'size'  => 20,
            'value'=> $options['email_from_address'],
            'enabled'=>true,
            'public'=>false );
        $def ['email_from_text'] = array (
            'type'  => 'static',
            'label' => 'From :'.$options['email_from_address'].','.$options['email_from_name'],
            'size'  =>0,
            'enabled'=>true,
            'public'=>true );
        $def['email_from_name'] = array (
            'type'  => 'text',
            'label' => 'From Name',
            'size'  => 0,
            'value'=> $options['email_from_name'],
            'enabled'=>true,
            'public'=>false);
        $def['message']= array (
            'type'  => 'textarea',
            'size'  => '15:40',
            'enabled'=>true,
            'public'=>true);
        $def['uid'] = array (
            'type'  => 'hidden',
            'enabled'=>true,
            'public'=>true,
            'value' => $_REQUEST['uid']);
        $def['otp'] = array (
            'type'  => 'hidden',
            'enabled'=>true,
            'public'=>true,
            'value' =>$_REQUEST['otp']);
        $def['list_return_url'] = array (
            'type'  => 'hidden',
            'enabled'=>true,
            'public'=>true,
            'value' =>$_REQUEST['list_return_url']);
        $def['list_action_id'] = array (
            'type'  => 'hidden',
            'enabled'=>true,
            'public'=>true,
            'value' =>$_POST['list_action_id']);
		$def['endline']=array(
            'type'  =>'static', 
            'label' =>'<br>', 
            'public'=>'1',
            'enabled'=>true);
        return $def;
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
        //fixing the textarea size
        if ( isset( $size ) && $size && ( $type == 'textarea' ) ) {
            if ( strpos( $size, ':' ) ) {
                $tmpArray = split( ':', $size );
                $rows = $tmpArray['0'];
                $cols = $tmpArray['1'];
            } else {
                $cols = $size;
            }

            if ( isset( $rows ) ) $fRef->setRows( $rows );
            if ( isset( $cols ) ) $fRef->setCols( $cols );
        }


		if ($type=='static') {
			  $renderer->setElementTemplate("{label}", $name);
		} elseif ($type=='textarea') {
			$renderer->setElementTemplate("{element}", $name);
		} else {
			  $renderer->setElementTemplate("\n\t\t<span align=\"right\" valign=\"top\" class=\"".$this->options['control_class']['value']."\">{label} {element}\n\t", $name);
		}
		
		
		return 1;
	}
}
