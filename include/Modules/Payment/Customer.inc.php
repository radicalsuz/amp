<?php

class PaymentCustomer {

    // Information related to the customer to charge
    var $dbcon;
    var $customer_info = array();
    var $customer_info_keys = array(
        'user_ID',
        'Name',
        'Street',
        'Street2',
        'City',
        'State',
        'Zip',
        'Email');
    var $SQL_translation = array (
        'Name_On_Card' => 'Name',
        'Billing_Address' => 'Street',
        'Billing_City' => 'City',
        'Billing_State'=> 'State',
        'Billing_Zip'=> 'Zip',
        'Billing_Email'=>'Email',
        'user_ID'=>'user_ID' );
    var $fields = array(
        'First_Name' => array('type'=>'text','label'=>'First Name', 'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>30),
        'Last_Name' => array('type'=>'text','label'=>'Last Name', 'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>30),
        'Street' => array('type'=>'text','label'=>'Address', 'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>30),
        'City' => array('type'=>'text','label'=>'City', 'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>30),
        'State' => array('type'=>'text','label'=>'State/Province', 'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>30),
        'Zip' => array('type'=>'text','label'=>'Postal Code', 'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>30),
        'Email' => array('type'=>'text','label'=>'Email', 'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>30));

    function PaymentCustomer ( &$dbcon, $id=null ) {
        $this->init ($dbcon, $id);
    }

    function init ( &$dbcon, $id = null ) {
        $this->dbcon = &$dbcon;
        if (isset($id)) $this->readData ( $id );
    }

    function assembleFieldsforRead () {
        foreach ($this->SQL_translation as $key => $value ) {
            $fielddesc [] = $key . " as " . $value;
        }
        return join ("," , $fielddesc);
    }

    function getDataforSQL () {
        $data = $this->getData();
        foreach ($data as $key => $value) {
            $sqlkey = array_search( $key, $this->SQL_translation);
            if ($sqlkey) $insert_data[$sqlkey] = $value;
        }
        return $insert_data;
    }
                


    function readData ($id) {
        $fields = $this->assembleFieldsforRead();
        $sql = "Select $fields from payment where user_ID = " . $id;
        if ($customer_info = $this->dbcon->GetRow( $sql ) ) {
            return $this->customer_info = $customer_info;
        }
        return false;
    }

    function readUserData( $id ) {
        $sql = "Select ".join(", ", array_keys($this->fields))." from userdata where id=".$id;
        if ($userdata = $this->dbcon->GetRow($sql)) {
            $this->setData( $userdata );
            return $this->customer_info;
        }
        return false;
    }

        

    function setData ( $data ) {
        if (!is_array($data)) return false;

        $cust_info = array_combine_key ($this->customer_info_keys, $data );
        //hack for Name field
        if ($data['First_Name']&&$data['Last_Name']) {
            $cust_info['Name']=$data['First_Name'].' '.$data['Last_Name'];
        }
        
        if (is_array($cust_info)) $this->customer_info=$cust_info;

        //set user_ID value for base Payment class
        if (isset($data['user_ID'])) return $data['user_ID'];
    }

    function getData( $fieldname = null ) {
        if (!isset($fieldname)) return $this->customer_info;

        if (isset($this->customer_info[ $fieldname ])) {
            return $this->customer_info[ $fieldname ];
        }

        return false;
    }
}
?>
