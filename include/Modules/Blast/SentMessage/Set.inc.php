<?php
define( 'PHPLIST_TABLE_USER_MESSAGE');
require_once( 'AMP/System/Data/Set.inc.php');
class EmailSentMessage_Set extends AMPSystem_Data_Set {
    var $datatable = PHPLIST_TABLE_LIST_MESSAGE;

    function EmailSentMessage_Set ( &$dbcon ) {
        $this->init( $dbcon );
    }

    function addCriteriaMessage( $message_id ) {
        $this->addCriteria( 'messageid='.$message_id );
    }

    function addCriteriaList( $list_id ) {
        $this->addCriteria( 'listid='.$list_id );
    }

    function deleteMessage( $message_id ) {
        $userRecords = &new AMPSystem_Data_Set( $this->dbcon );
        $userRecords->setSource( PHPLIST_TABLE_USER_MESSAGE );
        return (   $this->deleteData( 'messageid='.$message_id)
                && $userRecords->deleteData( 'messageid=' . $message_id)
                );
    }
}
?>
