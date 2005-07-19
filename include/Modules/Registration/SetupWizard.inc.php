<?php

require_once('AMP/System/XMLEngine.inc.php');
require_once('AMP/System/Form.inc.php');
require_once('AMP/Content/Map.inc.php');
require_once('AMP/Form/ElementCopierScript.inc.php');
require_once('Modules/Payment/ItemSet.inc.php');

class RegistrationSetup_Form extends AMPSystem_Form {
    
    var $payment_fields= array(
        'payment_items_id'=>array(
            'type'=>'select',
            'label'=>'Existing Payment Item'),
        'payment_items_Amount'=>array(
            'type'=>'text',
            'label'=>'Registration Fee'),
        'payment_items_name'=>array(
            'type'=>'text',
            'required'=> true,
            'label'=>'Payment Item Name'),
        'payment_items_description'=>array(
            'type'=>'textarea',
            'label'=>'Payment Item Description')
            );
    var $list = array(
        'sort' => 'id',
        'alias' => array (
            'ID'=>'id',
            'Name'=>'name',
            'Status'=>'publish'),
        'criteria'=>array('(name like "%Registration" or name like "%Application")'),
        'extra' => array(
            'view data'=>'modinput4_data.php?id=')
        );

        

    function RegistrationSetup_Form() {
        $name = "Registration_Wizard";
        $this->init( $name );
        if ($this->addFields( $this->getFields() )) {
            $this->setDynamicValues();
        }
        $this->addFields( $this->paymentTypeCopier() );
    }
    
    function paymentTypeCopier() {
        $PaymentAdder = &new ElementCopierScript( $this->getPaymentFields() );
        $PaymentAdder->formname = $this->formname;
        $PaymentAdder->copier_name = 'payment_type';
        return array( "adder" =>
            array( "type" => "html",
                   "default" => $PaymentAdder->output()) );
         
    }

    function getPaymentFields() {
        $payment_field_set = $this->payment_fields;
        $reg = &AMP_Registry::instance();
        $payment_set = & new PaymentItemSet ( $reg->getDbcon() );
        $payment_set->readData();
        $payment_field_set['payment_items_id']['values'] = $payment_set->optionValues();
        return $payment_field_set;
    }


    function getFields() {
        $xmlEngine = &new AMPSystem_XMLEngine( 'Registration/SetupFields' );
        if( $fields =  $xmlEngine->readData()) {
            return $fields;
        }
        return false;
    }

    function setDynamicValues() {
        $map = & AMPContent_Map::instance();
        $this->setFieldValueSet( 'sectionadd',  $map->selectOptions());
    }



}
?>        
