<?php

class Article_LegacySearch {

    var $_legacy_criteria = array(
        'class',
        'author', 
        'year',
        'area'
        );
    var $dbcon;
    var $_sql_criteria = array();

    function Article_LegacySearch( &$dbcon ) {
        $this->init( $dbcon );
    }

    function init( &$dbcon ) {
        $this->dbcon = &$dbcon;
        $this->_readRequest();
    }

    function getCriteria() {
        if (empty( $this->_sql_criteria )) return false;
        return $this->_sql_criteria;
    }

    function _readRequest() {
        if(!($url_criteria = AMP_URL_Read())) return false;
        $crit_set = array_combine_key( $this->_legacy_criteria, $url_criteria );
        if (empty($crit_set)) return false;
        foreach ( $crit_set as $crit_name => $value ) {
            if (!$value) continue;
            $sql_maker = '_makeLegacyCrit'.ucfirst($crit_name );
            if (!method_exists( $this, $sql_maker )) $sql_maker = '_makeSimpleCrit';
            $this->_sql_criteria[] =  $this->$sql_maker( $value, $crit_name )  ;
        }
    }

    function _makeLegacyCritArea( $value, $varname ) {
        return $this->_makeSimpleCrit( $value, 'region' );
    }

    function _makeLegacyCritAuthor( $value, $varname ) {
        return "author like ". $this->dbcon->qstr( '%'.$value.'%' );
    }

    function _makeSimpleCrit( $value, $varname ) {
        return "$varname = ". $this->dbcon->qstr( $value );
    }

    function _makeLegacyCritYear( $value, $varname ) {
        return "YEAR(`date`) = $value";
    }
}
?>
