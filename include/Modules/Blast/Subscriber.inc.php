<?php

require_once( 'AMP/System/Data/Item.inc.php');

class BlastSubscriber extends AMPSystem_Data_Item {
    var $datatable = PHPLIST_TABLE_USER;

    function BlastSubscriber ( &$dbcon, $id=null ) {
        $this->init( $dbcon, $id );
    }

    function unsubscribe( $list_id ) {
        $subscription_set = &new BlastSubscription_Set( $this->dbcon );
        return $subscription_set->deleteData(join( " AND " , array( 'listid='.$list_id, 'userid='.$this->id )) );
    }

    function subscribe( $list_id ) {
        $subscription = &new BlastSubscription( $this->dbcon );
        $subscription->setData( array( 'listid'=> $list_id, 'userid' => $this->id, 'entered' => true ));
        return $subscription->save( );
    }

    function getEmail( ) {
        return $this->getData( 'email' );
    }

    function getHtmlFlag( ){
        return $this->getData( 'htmlemail');
    }
    function setHtmlFlag( $value  ){
        return $this->mergeData( array( 'htmlemail' => $value ));
    }

    function create( $email, $html_email = false, $foreign_key = null ) {
        $new_user = array( 
            'email'     => $email,
            'confirmed' => true,
            'uniqid'    => md5( uniqid( mt_rand( ))),
            'htmlemail' => $html_email,
            'entered'   => time( ),
            'foreignkey'=> $foreign_key 
            );
        $this->setData( $new_user );
        if ( $this->save( )) return $this->id;
        return false;
        
    }

    function clearAttributes( ) {
        require_once( 'Modules/Blast/Subscriber/AttributeSet.inc.php');
        $attSet = &new BlastSubscriber_AttributeSet( $this->dbcon );
        return $attSet->deleteData( 'userid='.$this->id);
    }

    function setAttributes( $data ) {
        $this->dbcon->StartTrans( );
        $this->clearAttributes( );
        
        require_once( 'Modules/Blast/Subscriber/Attribute.inc.php');
        foreach( $data as $key => $value ) {
            if ( !$value ) continue;
            $attribute = &new BlastSubscriber_Attribute( $this->dbcon );
            if ( !( $attribute_id = $attribute->getAttributeId( $key ))) continue;
            $attribute->setAttribute( $value, $attribute_id, $this->id );
            $attribute->save( );
        }

        return $this->dbcon->CompleteTrans( );
    }

}
?>
