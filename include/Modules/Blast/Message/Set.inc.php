<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'Modules/Blast/ComponentMap.inc.php');

class BlastMessage_Set extends AMPSystem_Data_Set {
    var $datatable = PHPLIST_TABLE_MESSAGE;

    function BlastMessage_Set ( &$dbcon ) {
        $this->init( $dbcon );
    }

    function addCriteriaList( $list_id ) {
        require_once( 'Modules/Blast/SentMessage/Set.inc.php');
        $search = $this->getSearch( );
        $messageSet = &new BlastSentMessage_Set( $this->dbcon );
        $messageSet->addCriteriaList( $list_id );
        $this->addCriteria( $search->getRelatedSetCriteria( $messageSet, 'messageid'));

    }

    function addCriteriaUnsent( ) {
        $this->addCriteria( 'sent!=NULL');
    }

    function deleteMessage( $message_id ) {
        require_once( 'Modules/Blast/SentMessage/Set.inc.php');
        $messageRecord = &new BlastSentMessage_Set( $this->dbcon );
        $result = $messageRecord->deleteData( 'messageid='.$message_id);

        return $result && ( $this->deleteData( 'id='.$message_id ) !== FALSE);
    }

}
?>
