<?php

class PaymentItem {

    var $name;
    var $description;
    var $amount;
    var $tax_status;
    var $currency_format = '$ %01.2f US';
    var $item_data;
    var $item_data_keys = array( "name", "description", "amount", "tax_status");
    var $id;

    var $dbcon;
    

    function PaymentItem( &$dbcon, $id=null ) {
        $this->init($dbcon, $id);
    }

    function init(&$dbcon, $id=null) {
        $this->dbcon = & $dbcon;
        if (isset($id)) $this->readData($id);
    }

    function readData( $item_id ) {
    
        $sql =  "SELECT * FROM payment_items WHERE id=".$this->dbcon->qstr($item_id);
        if ($data = $this->dbcon->GetRow($sql)) {
            $this->setData( $data );
            $this->id = $item_id;
        }

    }

    function getData( $fieldname = null ) {
        if (!isset( $fieldname ) ) return $this->item_data;

        if (isset($this->item_data[$fieldname])) return $this->item_data[$fieldname];

        return false;
    }


    function setData( $data ) {
        $this->item_data = array_merge( $this->item_data, array_combine_key( $this->item_data_keys, $data ) );
    }

    function save( ) {
        $result = $this->dbcon->Replace( 'payment_items', $this->item_data, 'id', $quot = true);

        if ($result == ADODB_REPLACE_INSERTED) {
            $this->id = $this->dbcon->Insert_ID();
        }

        return $result;
    }

        

    function optionValue() {
        return sprintf( $this->currency_format, $this->getData('amount') ) ."  ". $this->getData('name');

    }


}
?>
