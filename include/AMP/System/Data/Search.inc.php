<?php

class AMPSystem_Data_Search  {

    var $_source;
    var $_dbcon;
    var $_exact_value_fields = array( );

    function AMPSystem_Data_Search( &$source ) {
        $this->init( $source );
    }

    function init( &$source ) {
        $this->_source = &$source;
        $this->_dbcon = &$source->dbcon;
    }

    function setExactValues() {
        $this->_exact_value_fields = $this->_source->getLiteralCriteria();
        $this->_exact_value_fields[] = $this->_source->id_field ;
    }

    function &getSource( ){
        return $this->_source;
    }


    function applyValues( $data ) {
        if ( !( isset( $data ) && is_array( $data ))) return false;
        foreach ($data as $key => $value) {
            $crit_method = '_addCriteria' . ucfirst( $key );

            if (method_exists( $this, $crit_method )) {
                $this->$crit_method( $value );
                continue;
            }

            $crit_method = substr( $crit_method, 1 );
            if ( method_exists( $this->_source, $crit_method )) {
                $this->_source->$crit_method( $value );
                continue;
            }

            if ( $crit_method = $this->_getCriteriaMethod( $key )){
                $this->$crit_method( $key, $value );
            }
        }
    }

    function _getCriteriaMethod( $fieldname ) {
        if ( !$this->_source->isColumn( $fieldname )) return false;
        if (array_search( $fieldname, $this->_exact_value_fields ) !==FALSE) return '_addCriteriaEquals';
        return '_addCriteriaContains';
    }

    function _addCriteriaContains( $key, $value ) {
        $sql_criterion = $key . ' LIKE ' . $this->_dbcon->qstr( '%' . $value . '%' );
        $this->_source->addCriteria( $sql_criterion );
    }

    function _addCriteriaEquals( $key, $value ) {
        $sql_criterion = $key . ' = ' . $this->_dbcon->qstr( $value );
        $this->_source->addCriteria( $sql_criterion );
    }

    function _addCriteriaAMPSearch( $value ) {
    }

    function getRelatedSetCriteria( &$set, $external_key, $id_field = null ) {
        if ( !isset( $id_field )) $id_field = $this->_source->id_field;

        $relatedSetCriteria = 'FALSE';

        $allowed_ids = &$set->getLookup( $external_key );
        if ( !empty( $allowed_ids )) $relatedSetCriteria = $id_field . ' in ( '. join( ", ", $allowed_ids ). ')';
        return $relatedSetCriteria;
        
    }

    /**
     * extract quoted phrases
     *
     * method kudos to insipience.com
     *
     * @param   string  $search_string  A value to be parsed for quoted phrases
     * @access  public
     * @return  array   a set of phrases to be searched
     */
    function separateSearchPhrases( $search_string ) {
        if ( !( substr_count( $search_string, '"') >= 2) ) return AMP_removeBlankElements( split(' ', $search_string ));

        preg_match_all("/\"([\w\s]+)(\"|$)/", $search_string, $result_phrases, PREG_PATTERN_ORDER); 
        $quoted_phrases = $result_phrases[1]; 
        $single_terms = explode(" ", preg_replace("/\"[\w\s]*(\"|$)/", "", $search_string));
        return AMP_removeBlankElements( array_merge( $single_terms , $quoted_phrases ));
    }

    function getCriteriaFulltext( $search_string ) {
        $fulltext_fields = $this->_source->getFullTextFields( );
        if ( empty ( $fulltext_fields )) {
            trigger_error( 'No fulltext search fields have been defined for ' . get_class( $this->_source ));
            return false;
        }
        return "MATCH ( " . join( ",", $fulltext_fields ) . " ) AGAINST ( ". $this->_dbcon->qstr( $search_string ) ." )";
    }
}
?>
