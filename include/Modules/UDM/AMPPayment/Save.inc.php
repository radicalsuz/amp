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

    function save($data) {
        $options = $this->getOptions();

        if (!isset($data['item_ID'])) return false;
        if (empty($data)) return true;
        
        $data['user_ID'] = $this->udm->uid;
        $data['merchant_ID'] = $options['merchant_ID'];
        $item = $this->getItems( $data['item_ID'] ) ;

        $this->processor->setData( $data );

        if ($this->processor->execute( $item->amount, $item->name )) return true;
            
        //in case of failure
        $this->_pass_errors_to_UDM();
        return false;
    }

    function setProcessor( $type = null ) {
        if (!isset($this->processor)) {
            $this->processor =& new Payment ( $this->dbcon, $type );
        }
    }

    function getSaveFields() {

        $save_fields = array();
        
        $types_to_avoid = array ("html", "static", "header");

        foreach ($this->fields as $fname => $fdef) {
            if ( array_search($this->fields[$fname]['type'], $types_to_avoid)!==FALSE ) continue;

            $save_fields[] = $fname;

        }

        return $save_fields;
    }

    function getPaymentType() {
        if ( isset($_REQUEST[$this->addPrefix('Payment_Type')]) ) {
            return $_REQUEST[$this->addPrefix('Payment_Type')];
        }
        return false;
    }
                
    function _pass_errors_to_UDM () {
        if (!isset($this->processor->errors)) return false;
        foreach ($this->processor->errors as $error_message) {
            $this->udm->errorMessage( $error_message );
        }
    }

    function _register_fields_dynamic() {
        
        $options = $this->getOptions();
        $fields = & $this->fields; 

        $fields = array();
        
        //Grab the item data
        $fields['item_ID'] = $this->setupPaymentItems( $options );
        if (!isset($fields['item_ID'])) return;

        //Get fields from the Payment object
        $fields = array_merge( $fields, $this->setupPaymentTypes($options) );

    }

    function setupPaymentItems( $options ) {
        if (!isset($options['item_IDs'])) return;
        $item_set = split("[ ]?,[ ]?", $options['item_IDs']);
        if (!is_array($item_set)) return;

        foreach ($item_set as $item_id) {
            $this->item_info[$item_id] = & new PaymentItem ( $this->dbcon, $item_id );
        }

        return array( 'label'=>$options['purchase_description'],
                                    'type'=>'select',
                                    'required'=>true,
                                    'values'=>$this->getItemOptions(),
                                    'public'=>true,
                                    'enabled'=>true);
    }

    function &getItems( $item_id = null ) {
        if (!isset($this->item_info)) return false;
        if (!isset($item_id)) return $this->item_info;

        if (isset($this->item_info[$item_id])) return $this->item_info[$item_id];
        return false;
    }

    function getItemOptions() {
        if (!$this->getItems()) return false; 

        $itemOptions = array();
        foreach ($this->getItems() as $item) {
            $itemOptions[$item->id] =  $item->optionValue();
        }
        return $itemOptions;
    }

    function setupPaymentTypes( $options = null ) {

        //if the payment type is already set
        //return only the fields from the relevent processor
        if ($selected_type = $this->getPaymentType()) {
            $this->setProcessor( $selected_type );
            $selector_field['Payment_Type'] = $this->getPaymentSelect( $options, $allow_select = false );
            $selector_field['Payment_Type']['default'] = $selected_type;
            return ($selector_field + $this->processor->fields);
        }

        //Otherwise Return fields from all processor types
        
        $selector_field['Payment_Type'] = $this->getPaymentSelect($options);

        $fieldswapper = & new ElementSwapScript( $this->fieldswap_object_id );
        $fieldswapper->formname = $this->udm->name;
        $paymentType_fields = array();

        foreach ($this->getAllowedPaymentTypes( $options ) as $payment_type => $description) {
            $current = &new Payment ($this->dbcon, $payment_type);
            
            $fieldswapper->addSet( $payment_type, $this->convertFieldDefstoDOM($current->fields)) ;
            $paymentType_fields = array_merge($paymentType_fields, $current->fields);
        }

        $this->_register_javascript ($fieldswapper->output()); 

        return ($selector_field + $paymentType_fields);
    }

    function getPaymentSelect( $options, $allow_select=true ) {
        $payment_options = $this->getAllowedPaymentTypes( $options );
        $type = $allow_select?'select':'hidden';

        $new_select = array('type'      => $type,
                            'label'     => 'Payment Method',
                            'enabled'   => true,
                            'public'    => true,
                            'required'  => true
                    );

        if (!$allow_select) return $new_select;

        $new_select['values'] = $payment_options;
        $new_select['attr']   = array(  'onChange'=>
                                        'ActivateSwap( window.'.$this->fieldswap_object_id.', this.value );');

        return $new_select;
    }

    function getAllowedPaymentTypes( $options ) {
        $allowed_types =  split("[ ]?,[ ]?", $options['allowed_payment_types']);
        return array_combine_key( $allowed_types, $this->options['allowed_payment_types']['values']);
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
