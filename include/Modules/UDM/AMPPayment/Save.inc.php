<?php
require_once ('Modules/Payment/Payment.php');
require_once ('Modules/Payment/Item.inc.php');
require_once ('AMP/UserData/Plugin/Save.inc.php');
require_once ('AMP/Form/ElementSwapScript.inc.php');

class UserDataPlugin_Save_AMPPayment extends UserDataPlugin_Save {

    var $short_name = "AMPPayment";

    var $options = array(
        'merchant_ID'=> array('label'=>'Merchant',
                             'type'=>'select',
                             'available'=>true,
                             'default'=>1,
                             'values'=>'Lookup(payment_merchants,id,Merchant)'),
        'item_IDs' => array( 'label'=>'Items for Purchase',
                            'type'=>'multiselect',
                            'available'=>true,
                            'values'=>'Lookup(payment_items,id,name)'),
        'purchase_description' => array( 'label'=>'Label for Purchase Field',
                            'type'=>'text',
                            'available'=>true,
                            'default'=>'Purchase Description'),
        'email_receipt' => array( 'label'=>'Send Receipt Email',
                                 'type'=>'checkbox',
                                 'available'=>true,
                                 'value'=>false ),
        'email_receipt_template' => array( 'label' => 'Template For Receipt',
                                          'type'  => 'select',
                                          'available' => true),
        'allowed_payment_types' => array( 'label' => 'Allowed Payment Options',
                                          'type'  => 'multiselect',
                                          'values'=> array('CreditCard'=>'Credit Card','Check'=>'Check'),
                                          'default'=>'CreditCard,Check',
                                          'available' => true)
        );

    var $_field_prefix = 'plugin_AMPPayment';
    var $fieldswap_object_id = 'plugin_AMPPayment_Swap';

    var $item_info;
    
    function UserDataPlugin_Save_AMPPayment (&$udm, $plugin_instance=null) {

        $this->init($udm, $plugin_instance);
    }

    function setProcessor( $type = null ) {
        if (!isset($this->processor)) {
            $this->processor = new Payment ( $this->dbcon, $type );
        }
    }

    function getSaveFields() {

        $save_fields=array_keys($this->fields);

        $new_save_fields = array();

        $fields_to_avoid = array ('Share_Data');

        foreach ($save_fields as $fname) {
            if ( array_search($fname, $fields_to_avoid) ) continue;
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

    function getPaymentType() {
        if ( isset($_REQUEST[$this->addPrefix('Payment_Type')]) ) {
            print $_REQUEST[$this->addPrefix('Payment_Type')].'cat';
            return $_REQUEST[$this->addPrefix('Payment_Type')];
        }
        print '<pre>';
        print_r ($_REQUEST);
        print '</pre>';
        return false;
    }
                
    function save($data) {
        $options = $this->getOptions();

        $this->setProcessor( $this->getPaymentType() );
        $this->processor->setMerchant($options['merchant_ID']);
        $this->processor->prepareTransaction( $data );

        $item = $this->item_info[  $data['item_ID']  ] ;

        if ($this->processor->execute($item->name, $item->amount)) return true;
            
        //in case of failure
        $this->udm->errorMessage($this->processor->error);
        return false;
    }

    function _register_fields_dynamic() {
        
        $options = $this->getOptions();

        $fields = array();
        
		$fields['Payment_Info'] = array('type'=>'header', 'label'=>'Payment Information', 'public'=>true,  'enabled'=>true);

        //Grab the item data
        $fields['item_ID'] = $this->setupPaymentItems( $options );
        if (!isset($fields['item_ID'])) return;

        //Get fields from the Payment object
        $fields = array_merge( $fields, $this->setupPaymentTypes($options));

        $this->fields = &$fields;
        #$this->insertAfterFieldOrder(array('Payment_Type'));
        

        //set the field order to put the dynamic checkbox before the Customer
        //Data
        /*
        $this->insertAfterFieldOrder($fields);
        $this->insertBeforeFieldOrder('Share_Data', 'First_Name');

        //add a fancy javascript to save users time when Cardholder data matches
        //personal data
        $fields['Share_Data']=array('type'=>'checkbox','label'=>'Check here if information below is the same as above', 
                                    'required'=>false, 'public'=>true, 'enabled'=>true, 'size'=>30,
                                    'attr'=>array('onClick'=>'plugin_AMPPayment_setAddress(this.checked);'));
        
        $this->_register_javascript ($this->address_script());
        */


    }

    function setupPaymentItems( $options ) {
        if (!isset($options['item_IDs'])) return;
        $item_set = split("[ ]?,[ ]?", $options['item_IDs']);
        if (!is_array($item_set)) return;

        foreach ($item_set as $item) {
            $this->item_info[$item] = & new PaymentItem ( $this->dbcon, $item );
        }

        return array( 'label'=>$options['purchase_description'],
                                    'type'=>'select',
                                    'required'=>true,
                                    'values'=>$this->getItemOptions(),
                                    'public'=>true,
                                    'enabled'=>true);
    }

    function getItemOptions() {
        if (!isset($this->item_info)) return false;

        $itemOptions = array();
        foreach ($this->item_info as $item) {
            $itemOptions = array_merge($itemOptions, $item->optionValue());
        }
        return $itemOptions;
    }

    function setupPaymentTypes( $options = null ) {

        //if the payment type is already set
        //return only the fields from the relevent processor
        if ( $this->udm->uid ) {
            //don't do anything
            return;
        }

        //Otherwise Return fields from all processor types
        
        $paymentType_fields = array();
        $allowed_types = split("[ ]?,[ ]?", $options['allowed_payment_types']);
        $payment_options = array_combine_key( $allowed_types, $this->options['allowed_payment_types']['values']);
        $selector_field['Payment_Type'] = 
                array(  'type'      => 'select',
                        'values'    => $payment_options,
                        'label'     => 'Payment Method',
                        'enabled'   => true,
                        'default'   => null,
                        'public'    => true,
                        'required'  => true,
                        'attr'      => array(   'onChange'=>
                                                'ActivateSwap( window.'.$this->fieldswap_object_id.', this.value );')
                        );


        $fieldswapper = & new ElementSwapScript( $this->fieldswap_object_id );
        $fieldswapper->formname = $this->udm->name;

        foreach ($allowed_types as $payment_type) {
            $current = &new Payment ($this->dbcon, $payment_type);
            
            $fieldswapper->addSet( $payment_type, $this->convertFieldDefstoDOM($current->fields)) ;
            $paymentType_fields = array_merge($paymentType_fields, $current->fields);
        }

        $this->_register_javascript ($fieldswapper->output()); 

        return ($selector_field + $paymentType_fields);
    }

    function returnTransactions ( $uid ) {
        $listing = new PaymentList ($this->dbcon);
        $cust_payments = $listing->getCustomerTransactions( $uid );
        foreach ($cust_payments as $row=>$payment_set) {
            $this->setProcessor( $payment_set['PaymentType'] );
            $this->processor->readData( $payment_set['id'] );
        }

        if (isset($this->processor->paymentType)) {
            return $this->processor->fields;
        }
    }

    /*
    function address_script() {

        $script = '
        <script type="text/javascript">
        var save_table;

        function plugin_AMPPayment_setAddress (chk_val) {
            var payform = document.forms["'.$this->udm->name.'"];
            if (chk_val) {';

        //Setup the form keys array
        $form_keys = $this->processor->customer->customer_info_keys;
        $form_keys[] = "First_Name";
        $form_keys[] = "Last_Name";

        $script_on = '';
        foreach ($form_keys as $cust_key) {
            if (!isset($this->udm->fields[$cust_key])) continue;
            $field_key = $this->_field_prefix.'_'.$cust_key;
            $script_on .= '
                payform.elements["'.$field_key.'"].value=payform.elements["'.$cust_key.'"].value;'."\n";
                #payform.elements["'.$field_key.'"].disabled=true;';
            #$script_off .='payform.elements["'.$field_key.'"].disabled=false;';
        }
        $script .= $script_on ."\n 
            }
        }
        
            
        </script>";
        return $script;
    }
    */

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
