<?php

require_once ('AMP/System/Data/Set.inc.php' );

class VoterGuideSet extends AMPSystem_Data_Set {

	var $datatable = "voterguides";
    var $sort = array( "state", "city", "election_date" );
    var $_positionSet;
    var $_search_fulltext = array( 'name', 'city', 'blurb', 'footer', 'short_name', 'affiliation' );

	function VoterGuideSet ( &$dbcon ) {
		$this->init ($dbcon );
	}

    function applySearch( $data, $run_query = true ){
        parent::applySearch( $data, false );
        $this->_applyPositionSetCriteria();
        if ( !AMP_Authorized( AMP_PERMISSION_VOTERGUIDE_PUBLISH  )) $this->addCriteriaLive( );

		if(defined('AMP_VOTERGUIDE_CURRENT_CYCLE')) {
			$this->addCriteria("election_cycle='".AMP_VOTERGUIDE_CURRENT_CYCLE."'");
		}
        if ( $run_query ) $this->readData( );
    }

    function _applyPositionSetCriteria(){
        if ( !isset( $this->_positionSet)) return true;
        $positionCriteria = $this->_search->getRelatedSetCriteria( $this->_getPositionSet(), 'voterguide_id');
        return $this->addCriteria( $positionCriteria );

    }

    function &_getPositionSet() {
        if ( isset( $this->_positionSet)) return $this->_positionSet;
        require_once( 'Modules/VoterGuide/Position/Set.inc.php' );
        $this->_positionSet = &new VoterGuidePositionSet( $this->dbcon ) ;
        return $this->_positionSet;
    }



    function addCriteriaPosition( $value ) {
        return $this->_addPositionSetCriteria( 'position', $value );
    }

    function _addPositionSetCriteria( $key, $value ){
        $positionSet= &$this->_getPositionSet( );
        $positionSet->applySearch( array( $key=>$value ) );
    }

    function addCriteriaItem( $value ) {
        return $this->_addPositionSetCriteria( 'item', $value );
    }

    function addCriteriaLive() {
        return $this->addCriteria( 'publish=1' );
    }

    function addCriteriaOwner_name( $value ) {
        require_once( 'AMP/UserData/Lookups.inc.php');
        $owner_names = &FormLookup_Names::instance( AMP_FORM_ID_VOTERGUIDES );
        $owner_ids = array_keys( $owner_names, $value );
        $owner_criteria = "FALSE";
        if ( !empty( $owner_ids )) $owner_criteria = 'owner_id in ( '. join( ',', $owner_ids ) .' )';
        $this->addCriteria( $owner_criteria );

    }

    function addCriteriaFullText( $search_string, $sortby_relevance = true ) {
        $search = &$this->getSearch( );
        $fulltext_criteria = $search->getCriteriaFulltext( $search_string );
        $positionTextSet = &new VoterGuidePositionSet( $this->dbcon );
        $positionTextSearch = &$positionTextSet->getSearch( );
        $position_match = $positionTextSearch->getCriteriaFulltext( $search_string );
        $positionTextSet->addCriteria( $position_match );
        #$position_matches = $positionTextSet->getLookup( $position_match . ' as Match_Score');
        $position_criteria  = $positionTextSearch->getRelatedSetCriteria( $positionTextSet, 'voterguide_id' );
        
        if ( $sortby_relevance ) $this->addSort( $this->_getFullTextSort( $fulltext_criteria, $position_criteria ) );
        if ( $position_criteria != "FALSE" ) $fulltext_criteria = "( " . $fulltext_criteria . " OR " . $position_criteria . " )";
        return $this->addCriteria( $fulltext_criteria );
    }

    function _getFullTextSort( $fulltext_criteria, $position_matches ) {
       if ( $position_matches == "FALSE" ) return $fulltext_criteria; 
       $position_bonus = "if ( " . $position_matches . ", 1, 0)";
       return "( " . $fulltext_criteria ." + " . $position_bonus . ") DESC";
    }

    function addCriteriaFulltextManual( $search_string ) {
        $phrase_set = $this->_search->separateSearchPhrases( $search_string );
        // determine the fields to include in the search
        $textfields = $this->avoidNullSql( $this->getTextFields( ) );

        $aggregate_textfield = "Concat( ". join( ',' , $textfields). ")";
        $regex_elements = array( );
        /*
        foreach ($phrase_set as $search_phrase) {
            if (!$search_phrase) continue;
            $regex_elements[] = "[[.".$search_phrase.".]]";
        }
        */
        $this->addCriteria( $aggregate_textfield . " REGEXP " . $this->dbcon->qstr( join( "|", $phrase_set)) );

    }

    function avoidNullSql( $fields ) {
        $results = array( );
        foreach( $fields as $fieldName ){
            $results[] = 'ifnull( ' . $fieldName . ', "" )';
        }
        return $results;
    }
}

?>
