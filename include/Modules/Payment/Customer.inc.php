<?php

/**************************
 *   
 *    PaymentCustomer
 *
 *      Collection of Customer Address Info from payment table
 *
 *      Author: austin@radicaldesigns.org
 *
 *      Date: 06/17/2005
 *
 *******/

class PaymentCustomer {

    var $dbcon;

    // Information related to the customer to charge
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

    // Reference Table converts column names from payment table
    // to keynames for customer_info array
    var $SQL_translation = array (
        'Name_On_Card' => 'Name',
        'Billing_Street' => 'Street',
        'Billing_City' => 'City',
        'Billing_State'=> 'State',
        'Billing_Zip'=> 'Zip',
        'Billing_Email'=>'Email',
        'user_ID'=>'user_ID' );

    // Address Verification Fields
    var $fields = array(
        'Name' => array('type'=>'text','label'=>'Name', 'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>40),
        'Street' => array('type'=>'text','label'=>'Address', 'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>30),
        'Zip' => array('type'=>'text','label'=>'Postal Code', 'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>30)
        #'City' => array('type'=>'text','label'=>'City', 'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>30),
        #'State' => array('type'=>'text','label'=>'State/Province', 'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>30),
        #'Email' => array('type'=>'text','label'=>'Email', 'required'=>true, 'public'=>true, 'enabled'=>true, 'size'=>30)
        );

    function PaymentCustomer ( &$dbcon, $id=null ) {
        $this->init ($dbcon, $id);
    }

    function init ( &$dbcon, $id = null ) {
        $this->dbcon = &$dbcon;
        if (isset($id)) $this->readData ( $id );
    }


    #######################################
    ### Public Data Retrieval Functions ###
    #######################################

    // getData retrieves values from the customer_info array

    function getData( $fieldname = null ) {
        if (!isset($fieldname)) return $this->customer_info;

        if (isset($this->customer_info[ $fieldname ])) {
            return $this->customer_info[ $fieldname ];
        }

        return false;
    }

    function getInsertData() {
        $data = $this->getData();
        foreach ($data as $key => $value) {
            $sqlkey = array_search( $key, $this->SQL_translation);
            if ($sqlkey) $insert_data[$sqlkey] = $value;
        }
        return $insert_data;
    }
                

    ########################################
    ### Public Data-Assignment Functions ###
    ########################################


    // readData calls up values for a particular payment id from the payment
    // table and assigns them to the current instance 

    function readData ($id) {
        $sql = "Select ".$this->assembleFieldsfromPayment()." from payment where user_ID = " . $id;
        if ($customer_info = $this->dbcon->GetRow( $sql ) ) {
            return $this->customer_info = $customer_info;
        }
        return false;
    }

    // readUserData calls up values for a particular id from the
    // userdata table and assigns them to the current instance 

    function readUserData( $id ) {
        $sql = "Select ".$this->assembleFieldsfromUserData()." from userdata where id=".$id;
        if ($userdata = $this->dbcon->GetRow($sql)) {
            $this->setData( $userdata );
            return $this->customer_info;
        }
        return false;
    }

    // setData assigns values to the customer_info array

    function setData ( $data ) {
        if (!is_array($data)) return false;

        $cust_info = array_combine_key ($this->customer_info_keys, $data );
        
        if (is_array($cust_info)) $this->customer_info=$cust_info;

        //set user_ID value for base Payment class
        if (isset($data['user_ID'])) return $data['user_ID'];
    }


    #################################
    ### Private Utility Functions ###
    #################################

    function _assembleFieldsfromPayment () {
        foreach ($this->SQL_translation as $key => $value ) {
            $fielddesc [] = $key . " as " . $value;
        }
        return join ("," , $fielddesc);
    }


    function _assembleFieldsfromUserData () {
        $fields = array_keys($this->fields);

        $namekey = $fields[ array_search( "Name", $fields );
        if ( $namekey !== FALSE ) unset ( $fields[ $namekey ] );
        $fields[] = "Concat( First_Name, \" \", Last_Name) as Name";

        return join(", ", $fields);
    }
        
}
?>
