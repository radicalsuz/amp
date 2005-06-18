<?php
require_once( 'Modules/Payment/Payment.php' );
require_once( 'Modules/Payment/List.inc.php' );

class UserDataPlugin_Read_AMPPayment extends UserDataPlugin {

    var $options = array(
        'allow_edit' => array(
                    'type'  =>  'checkbox',
                'available' =>  true,
                    'label' => 'Allow changing data for past transactions',
                'default'   =>  false ),
        '_userid' => array(   
                'available' => false,
                    'value' => null) 
        );

    var $available = true;
    var $_field_prefix = "plugin_AMPPayment";

    function UserDataPlugin_Read_AMPPayment (&$udm, $plugin_instance = null) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic() {
        $this->fields = array(
		    'Payment_Info' => array(
                'type'=>'header', 
                'label'=>'Payment Information', 
                'public'=>true,  
                'enabled'=>true),

            'transaction_list' => array(
                'type'=>'html',
                'public'=>false,
                'enabled'=>true
                )
            );
    }

    function execute( $options = null ) {
        $options = array_merge ($this->getOptions(), $options);
        if (!isset( $options['_userid'] ) ) return false;
        $uid = $options['_userid'];
        
        $paymentlist = new PaymentList ( $this->dbcon );
        $paymentlist->getCustomerTransactions( $uid );
        $paymentlist->suppressHeader();
        $paymentlist->suppressAddlink();

        $this->removeSaveFieldset();
        
        $this->udm->fields[ $this->addPrefix('transaction_list') ]['values'] = $this->inForm($paymentlist->output());
    }

    function removeSaveFieldset() {
        foreach ($this->udm->fields as $fname => $fDef) {
            $local_name = $this->checkPrefix($fname);
            if ($local_name && !isset($this->fields[$local_name])) unset ($this->udm->fields[$fname]);
        }
    }

    function inForm( $raw_html ) {
        return "<tr><td colspan=2 class = \"form_span_col\">". $raw_html ."</td></tr>\n";
    }
}
?>
