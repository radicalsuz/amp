<?php

require_once( 'AMP/Form/SearchForm.inc.php');

class AMP_User_Profile_Search_Form extends AMPSearchForm {

    function AMP_User_Profile_Search_Form( ) {
        $this->__construct( );//false, false, AMP_SYSTEM_URL_PROFILE );
    }

}

?>
