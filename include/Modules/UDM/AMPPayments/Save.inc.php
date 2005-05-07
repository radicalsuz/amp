<?php
require_once ('Modules/Payment/CreditCard.php');
require_once ('AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Save_AMPPayment extends UserDataPlugin {
    var $options = array(
        'merchant_ID'= array('label'=>'Merchant',
                             'type'=>'select',
                             'available'=>true,
                             'default'=>1,
                             'values'=>'Lookup(payment_merchants,id,Merchant)'),
        'item_ID' = array( 'label'=>'Item for Purchase',
                            'type'=>'select',
                            'available'=>true,
                            'values'=>'Lookup(payment_items,id,name)'),
        'email_receipt' = array( 'label'=>'Send Receipt Email',
                                 'type'='checkbox',
                                 'available'=true,
                                 'value'=false ),
        'email_receipt_template' = array( 'label' => 'Template For Receipt',
                                          'type'  => 'select',
                                          'available' => true)
        );
    var $item_info;


    
    function UserDataPlugin_Save_AMPPayment (&$udm, $plugin_instance=null) {
        $this->init($udm, $plugin_instance);
        $options => $this->getOptions();
        $this->processor = &new Payment_CreditCard($this->dbcon, $options['merchant_ID']);

    }
    function getSaveFields() {
        $save_fields=array_keys($this->fields);
        unset($save_fields['Share_Data']);

        foreach ($save_fields as $fname) {
            switch ($this->udm->fields[$fname]['type']) {
                case 'html':
                case 'static':
                case 'header':
                    unset($save_fields[$fname]);
            }
        }
        return $save_fields;
    }
                
    function save($data) {
        $processor=& $this->processor;
        $options = $this->getOptions();
        
        $this->item_info = $this->getItem();
        $data['user_ID'] = $this->udm->uid;

        $processor->setCard($data);
        $processor->setCustomer($data);
        if ($processor->charge($this->item_info['name'], $this->item_info['amount']) {
            return true;
        } else {
            $this->udm->errorMessage($processor->error);
            return false;
        }
        

    }

    function getItem() {
    
        $options = $this->getOptions();
        $sql =  "Select * from payment_items where id=".$this->dbcon->qstr($options['payment_item_ID']);
        return $this->dbcon->CacheGetOne($sql);
    }

    function _register_fields_dynamic() {
        //Get fields from the Payment_CreditCard object
        $fields=$this->processor->fields;
        
        //add a fancy javascript to save users time when CC data matches
        //personal data
        $fields['Share_Data']=array('type'=>'checkbox','label'=>'Check here if information below is the same as above', 
                                    'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>30
                                    'attr'='onClick=plugin_AMPPayment_setAddress(this.value);');
        $fields['setaddress_script']=array('type'=>'html', 'value'=>
        '
        <script type="text/javascript">
        function plugin_AMPPayment_setAddress (chk_val) {
            var payform = document.forms["'.$this->udm->name.'"];
            if (chk_val) {
                payform.elements["plugin_AMPPayment_First_Name"].value=payform.elements["First_Name"].value;
                payform.elements["plugin_AMPPayment_First_Name"].enabled=false;
            } else {
                payform.elements["plugin_AMPPayment_First_Name"].enabled=true;
            }
        }
        </script>
        ', 'enabled'=>true,'public'=>true);
        $this->fields=$fields+$this->fields;
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
