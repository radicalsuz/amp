<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'Modules/Blast/ComponentMap.inc.php');

class BlastSubscriber_Set extends AMPSystem_Data_Set {
    var $datatable = PHPLIST_TABLE_USER;
    var $sort = array( 'email');

    function BlastSubscriber_Set ( &$dbcon, $id=null ) {
        if ( isset( $id )) $this->addCriteriaList( $id );
        $this->init( $dbcon );
    }

    function addCriteriaList( $list_id ) {
        $search = $this->getSearch( );
        $listSet = &new BlastSubscription_Set( $this->dbcon );
        $listSet->setIdFieldLookups( 'listid' );
        $listSet->addCriteriaList( $list_id );
        $this->addCriteria( $search->getRelatedSetCriteria( $listSet, 'userid' ));
    }

    function addCriteriaUid( $uid ) {
        #$emailSet = &AMPSystem_Lookup::instance( 'userDataEmails');
        #if ( !isset( $emailSet[$uid])) return false;
        $this->addCriteria( 'foreignkey='.$uid );
        $this->readData( );
    }

    function addCriteriaEmail( $email ) {
        return $this->addCriteria( 'email=' . $this->dbcon->qstr( $email));
    }

    function addCriteriaUnique( $hash ) {
        return $this->addCriteria( 'uniqid=' . $this->dbcon->qstr( $hash ));
    }

    function addCriteriaActive() {
        $this->addCriteria( 'disabled != 1');

    }

}
?>
