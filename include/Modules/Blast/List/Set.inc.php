<?php
require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'Modules/Blast/ComponentMap.inc.php');

class BlastList_Set extends AMPSystem_Data_Set {
    var $datatable = PHPLIST_TABLE_LIST;
    var $sort = array( 'listorder');

    function BlastList_Set( &$dbcon ) {
        $this->init( $dbcon );
    }

    function addCriteriaMessage( $message_id ){
        require_once( 'Modules/Blast/SentMessage/Set.inc.php');
        $search = $this->getSearch( );
        $messageSet = &new BlastSentMessage_Set( $this->dbcon );
        $messageSet->addCriteriaMessage( $message_id );
        $this->addCriteria( $search->getRelatedSetCriteria( $messageSet, 'listid'));
    }

    function addCriteriaPublic( ){
        $this->addCriteria( 'public=1');
    }

    function addCriteriaSubscriber( $user_id ){
        require_once( 'Modules/Blast/Subscription/Set.inc.php');
        $search = $this->getSearch( );
        $subSet = &new BlastSubscription_Set( $this->dbcon );
        $lists = $subSet->getListsByUser( $user_id );
        if ( !count( $lists )) return false;
        $this->addCriteria( "id in (". join( ", ", $lists ) . " )");
    }

    function deleteList( $list_id ) {
        require_once( 'Modules/Blast/SentMessage/Set.inc.php');
        require_once( 'Modules/Blast/Subscription/Set.inc.php');
        $messages = &new BlastSentMessage_Set( $this->dbcon );
        $result = $messages->deleteData( 'listid='.$list_id);
        $subscribers = &new BlastSubscription_Set( $this->dbcon );
        $result = ( $result !== FALSE ) && ( $subscribers->deleteData( 'listid='.$list_id) !== FALSE);
        return $result && ( $this->deleteData( 'id='.$list_id ) !== FALSE);
    }

}
?>
