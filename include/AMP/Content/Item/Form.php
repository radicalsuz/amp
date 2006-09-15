<?php

class AMP_Content_Item_Type_Form extends AMP_Content_Item_Type {

    var $_profile;

    function AMP_Content_Item_Type_Form ( $uid ) {
        $this->__construct( $uid );
    }

    function __construct( $uid ) {
        $this->_profile = &new AMP_System_User_Profile( AMP_Registry::getDbcon( ), $uid );
    }

    function getName( ) {
        return $this->_profile->getName( );
    }

}

?>
