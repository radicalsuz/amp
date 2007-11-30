<?php

require_once( 'AMP/Display/System/List.php');
require_once( 'AMP/User/Data/Data.php');
require_once( 'AMP/User/List/Request.php');

class AMP_User_Data_List extends AMP_Display_List {

    var $_pager_active = true;
    var $_pager_index = 'Concat( Last_Name, ", ", First_Name ) as name';
    var $_class_pager = 'AMP_Display_Pager';
    var $_path_pager = 'AMP/Display/Pager.php';

    var $_source_object = 'AMP_User_Data' ;
    var $_source_fields = array( );

    var $_sort_sql_default = 'name';
    var $_sort_sql_translations = array ( 
        'name'      => 'Last_Name, First_Name',
        'status'    => 'publish, Last_Name, First_Name',
        'org'       => 'Company, Last_Name',
        'location'  => 'Country, State, City, Zip, Last_Name, First_Name',
        'contact'   => 'Email, Phone'
    );

    function AMP_User_Data_List( $source = false, $criteria = array( )) {
        $this->__construct( $source, $criteria );
    }

    function _init_criteria( ) {
        if ( !(isset( $this->_source_criteria['modin'] ) && $modin = $this->_source_criteria['modin'])) return false;
        $this->_source_fields = $this->_source_sample->getFields( $modin );
    }

}


?>
