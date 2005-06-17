<?php

class PaymentItem {

    var $name;
    var $description;
    var $amount;
    var $tax_status;
    var $currency_format = '$ %01.2f US';

    var $dbcon;
    

    function PaymentItem( &$dbcon, $id=null ) {
        $this->init($dbcon, $id);
    }

    function init(&$dbcon, $id=null) {
        $this->dbcon = & $dbcon;
        if (isset($id)) $this->getData($id);
    }

    function getData( $item_id ) {
    
        $sql =  "SELECT * FROM payment_items WHERE id=".$this->dbcon->qstr($item_id);
        if ($data = $this->dbcon->GetRow($sql)) {
            $this->name = $data['name'];
            $this->amount = $data['Amount'];
            $this->description = $data['description'];
            $this->tax_status = $data['Tax_Status'];
            $this->id = $item_id;
        }

    }

    function setData( ) {
        $data = compact( $this->id, $this->name, $this->description, $this->amount, $this->tax_status );
        $result = $this->dbcon->Replace( 'payment_items', $data, 'id', $quot = true);

        if ($result == ADODB_REPLACE_INSERTED) {
            $this->id = $this->dbcon->Insert_ID();
        }

        return $result;
    }

        

    function optionValue() {
        return sprintf( $this->currency_format, $this->amount ) ."  ". $this->name;

    }


}
