<?php
#payment usage



    function execute( $options = null ) {
        $options = array_merge($this->getOptions(), $options);
        // Check for the existence of a userid.
        if (!isset( $options['calid'] ) &&
            !isset($this->options['calid']['value'] )) return false;

        $calid = (isset($options['calid'])) ? $options['calid'] : $this->options['calid']['value'];
	
        //Read Calendar Record
        $sql  = "SELECT * FROM calendar WHERE "; 
        $sql .= "id='" . $calid . "'";      
    
        $calDataSet = $this->dbcon->CacheExecute( $sql );
        if ($calData = $calDataSet->FetchRow()) {
            $this->setData( $calData );
            return true;
        }
        return false;

    }






$pay = new Payments($dbcon);

$data array (
			'First_Name'=>'',
			'Last_Name'=>'',
			'Address'=>'',
			'City'=>'',
			'State'=>'',
			'Zip'=>'',
			'Email'=>'',
			'Amount'=>'',
			'Credit_Card_Type'=>'',
			'Credit_Card_Number'=>'',
			'Credit_Card_Expiration'=>'',
			'Payment_Type'=>'',
			'user_ID'=>'',

		)
$status = $pay->process_payment($data);


?>
