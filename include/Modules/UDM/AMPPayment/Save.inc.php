<?php
require_once ('Modules/Payment/CreditCard.inc.php');
require_once ('AMP/UserData/Plugin/Save.inc.php');

class UserDataPlugin_Save_AMPPayment extends UserDataPlugin_Save {

    var $short_name = "AMPPayment";

    var $options = array(
        'merchant_ID'=> array('label'=>'Merchant',
                             'type'=>'select',
                             'available'=>true,
                             'default'=>1,
                             'values'=>'Lookup(payment_merchants,id,Merchant)'),
        'item_ID' => array( 'label'=>'Item for Purchase',
                            'type'=>'select',
                            'available'=>true,
                            'values'=>'Lookup(payment_items,id,name)'),
        'email_receipt' => array( 'label'=>'Send Receipt Email',
                                 'type'=>'checkbox',
                                 'available'=>true,
                                 'value'=>false ),
        'email_receipt_template' => array( 'label' => 'Template For Receipt',
                                          'type'  => 'select',
                                          'available' => true)
        );

    var $_field_prefix = 'plugin_AMPPayment';

    var $item_info;
    
    function UserDataPlugin_Save_AMPPayment (&$udm, $plugin_instance=null) {
        $this->processor = &new Payment_CreditCard($udm->dbcon);
        $this->init($udm, $plugin_instance);
    }

    function getSaveFields() {

        $save_fields=array_keys($this->fields);

        $new_save_fields = array();

        foreach ($save_fields as $fname) {
            if ($fname == 'Share_Data') continue;
            switch ($this->fields[$fname]['type']) {
                case 'html':
                case 'static':
                case 'header':
                    continue;
                    break;
                default:
                    $new_save_fields[] = $fname;
                    break;
            }
        }

        return $new_save_fields;
    }
                
    function save($data) {
        $processor=& $this->processor;
        $options = $this->getOptions();

        $this->item_info = $this->getItem();

        $processor->setCard($data);
        $processor->setCustomer($data);

        if ($processor->charge($this->item_info['name'], $this->item_info['amount'])) {
            return true;
        } else {
            $this->udm->errorMessage($processor->error);
            return false;
        }
    }

    function getItem() {
    
        $options = $this->getOptions();
        if (!isset($options['item_ID'])) return false;
        $sql =  "SELECT * FROM payment_items WHERE id=".$this->dbcon->qstr($options['item_ID']);
        return $this->dbcon->CacheGetOne($sql);
    }

    function _register_fields_dynamic() {
        
        $options = $this->getOptions();
        $this->processor->setMerchant($options['merchant_ID']);
        $prefix = ($this->_field_prefix?$this->_field_prefix:'plugin_AMPPayment').'_';
        
        //Get fields from the Payment_CreditCard object
        $fields=$this->processor->fields;

        //set the field order to put the dynamic checkbox before the Customer
        //Data
        $cc_fieldorder_tmp = array();
        foreach ($fields as $cc_field=>$cc_fDef) {
            if ($cc_field=="First_Name") $cc_fieldorder_tmp[] = $prefix."Share_Data,";
            $cc_fieldorder_tmp[] = $prefix.$cc_field.",";
        }
        $cc_fieldorder = join(",", $cc_fieldorder_tmp);
        
        //add a fancy javascript to save users time when Cardholder data matches
        //personal data
        $fields['Share_Data']=array('type'=>'checkbox','label'=>'Check here if information below is the same as above', 
                                    'required'=>false, 'public'=>true, 'enabled'=>true, 'size'=>30,
                                    'attr'=>array('onClick'=>'plugin_AMPPayment_setAddress(this.checked);'));
        $fields['setaddress_script']=array('type'=>'html', 'values'=>
            $this->address_script($fields), 'enabled'=>true,'public'=>true, 'required'=>false);

//        $this->udm->_module_def[ 'field_order' ] = join(",", array($this->udm->_module_def[ 'field_order'],$cc_fieldorder));
       
        $this->fields = array_merge($this->fields, $fields);

    }

    function address_script($fields) {
        if (!isset($this->_field_prefix)) $this->_field_prefix = "plugin_AMPPayment";

        $script = '
        <script type="text/javascript">
        var save_table;

        function plugin_AMPPayment_setAddress (chk_val) {
            var payform = document.forms["'.$this->udm->name.'"];
            if (chk_val) {';

        //Setup the form keys array
        $form_keys = $this->processor->customer_info_keys;
        $form_keys[] = "First_Name";
        $form_keys[] = "Last_Name";

        $script_on = '';
        foreach ($form_keys as $cust_key) {
            if (!isset($this->udm->fields[$cust_key])) continue;
            $field_key = $this->_field_prefix.'_'.$cust_key;
            $script_on .= '
                payform.elements["'.$field_key.'"].value=payform.elements["'.$cust_key.'"].value;';
                #payform.elements["'.$field_key.'"].disabled=true;';
            #$script_off .='payform.elements["'.$field_key.'"].disabled=false;';
        }
        $script .= $script_on ."\n 
            }
        }
        
            
        </script>";
        return $script;
    }

    function _register_options_dynamic () {
        if ($this->udm->admin) {
            $udm_mod_id  = $this->dbcon->qstr( $this->udm->instance );
            $modlist_sql = "SELECT   moduletext.id, moduletext.name FROM moduletext, modules
                            WHERE    modules.id = moduletext.modid
                                AND modules.userdatamodid = $udm_mod_id
                            ORDER BY name ASC";
            $modlist_rs  = $this->dbcon->CacheExecute( $modlist_sql )
                or die( "Error fetching module information: " . $this->dbcon->ErrorMsg() );

            $modules[ '' ] = '--';
            while ( $row = $modlist_rs->FetchRow() ) {
                $modules[ $row['id'] ] = $row['name'];
            }
            $this->options['Email_Receipt_Template']['values']=$modules;
        }
    }
}
?>
