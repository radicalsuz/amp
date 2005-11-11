<?php

class SugarData_Translator {

    var $_keySets = array (
        'Contacts' => array(
			'salutation'	=>	'Title',
            'first_name'    =>  'First_Name',
            'last_name'     =>  'Last_Name',
            'title'         =>  'occupation',
            'phone_home'    =>  'Phone',
			'phone_mobile'	=>	'Cell_Phone',
			'phone_work'	=>	'Work_Phone',
			'phone_fax'		=>	'Work_Fax',
            'email1'        =>  'Email',
            'description'   =>  'Notes',
            'primary_address_street'        =>  'Street',
            'primary_address_city'          =>  'City',
            'primary_address_state'         =>  'State',
            'primary_address_postalcode'    =>  'Zip',
            'primary_address_country'       =>  'Country'
            ),
        'Users' => array(
			'salutation'	=>	'Title',
            'first_name'    =>  'First_Name',
            'last_name'     =>  'Last_Name',
            'title'         =>  'occupation',
            'phone_home'    =>  'Phone',
			'phone_mobile'	=>	'Cell_Phone',
			'phone_work'	=>	'Work_Phone',
			'phone_fax'		=>	'Work_Fax',
            'email1'        =>  'Email',
            'description'   =>  'Notes',
            'address_street'        =>  'Street',
            'address_city'          =>  'City',
            'address_state'         =>  'State',
            'address_postalcode'    =>  'Zip',
            'address_country'       =>  'Country'
			),
        );

    function Sugar_Data_Translator() {
        //stub constructor
    }


    function translateKeys( $data_array, $module_name = 'Contacts' ) {
        if (!($keySet = $this->getTranslation( $module_name ))) return $data_array;
        $result_data = array();

        foreach( $data_array as $dKey => $dValue ) {
            if (!($new_key = array_search( $dKey, $keySet ))) $new_key = $dKey;
            $result_data[ $new_key ] = $dValue;
        }
        return $result_data;
    }

    function hasTranslation( $module_name ) {
        return isset($this->_keySets[ $module_name ]);
    }

    function getTranslation( $module_name ) {
        if (!$this->hasTranslation( $module_name )) return false;
        return $this->_keySets[ $module_name ];
    }


}
?>
