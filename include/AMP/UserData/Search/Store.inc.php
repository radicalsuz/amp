<?php

define( 'UDM_SEARCH_CRITERIA_DELIMITER', '::&&::' );

class UserDataSearch_Store extends AMPSystem_Data_Item {

    var $datatable = "userdata_search";
    var $udm;


    function UserDataSearch_Store (&$udm, $id=null ) {
        $this->init( $udm, $id );
    }

    function init( &$udm, $id ) {
        $this->udm = &$udm;
        PARENT::init( $udm->dbcon, $id );
    }

    function makeCriteria() {
        $this->mergeData(  array( 'fields' => join( UDM_SEARCH_CRITERIA_DELIMITER, array_keys($this->udm->sql_criteria ))) );
        $this->mergeData(  array( 'criteria' => join( UDM_SEARCH_CRITERIA_DELIMITER, $this->udm->sql_criteria )) );
    }

    function applyCriteria() {
        if (!($crit =  $this->getCriteria() ) return false;
        $search_options = array (   'criteria'  =>  array( 'value'=>$crit),
                                    'clear_criteria'    => array('value'=> true) );
        return $this->udm->doAction( 'Search', $search_options );
    }

    function getCriteria() {
        return $this->_getSplit( 'criteria' );
    }

    function _getSplit( $fieldname ) {
        if (!($raw_data= $this->getData($fieldname))) return false;
        return split( UDM_SEARCH_CRITERIA_DELIMITER, $raw_data );

    }

    function getFields() {
        return $this->_getSplit( 'fields' );
    }


	
}

?>
