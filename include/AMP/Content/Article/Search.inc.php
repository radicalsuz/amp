<?php

require_once ( 'AMP/Content/Article/Set.inc.php' );

class ArticleSearch extends ArticleSet {

    var $null_date = '0000-00-00';

    function ArticleSearch ( &$dbcon ) {
        $this->init( $dbcon );
    }

    function applyValues( $data ) {
        foreach ($data as $key => $value) {
            $crit_method = '_addCriteria' . ucfirst( $key );
            if (!method_exists( $this, $crit_method )) $crit_method = $this->_getCriteriaMethod( $key );
            $this->$crit_method( $key, $value );
        }
        $this->readData();
    }

    function _getCriteriaMethod( $fieldname ) {
        $exact_value_fields = array( "id", "class", "type" );
        if (array_search( $fieldname, $exact_value_fields ) !==FALSE) return '_addCriteriaEquals';
        return '_addCriteriaContains';
    }

    function _addCriteriaContains( $key, $value ) {
        $sql_criterion = $key . ' LIKE ' . $this->dbcon->qstr( '%' . $value . '%' );
        $this->addCriteria( $sql_criterion );
    }

    function _addCriteriaEquals( $key, $value ) {
        $sql_criterion = $key . ' = ' . $this->dbcon->qstr( $value );
        $this->addCriteria( $sql_criterion );
    }

    function _addCriteriaDate ( $key, $value ) {
        $date_value = date( 'Y-m-d', mktime(0,0,0, $value['M'], $value['d'], $value['Y']));
        $sql_criterion = $key . ' >= ' . $this->dbcon->qstr( $date_value );
        $sql_criterion = '('. $sql_criterion . ' OR ' . $key . ' = ' . $this->dbcon->qstr( $this->null_date ) . ')';
        $this->addCriteria( $sql_criterion );
    }

    function _addCriteriaAMPSearch( $key, $value ) {
    }
}
?>
