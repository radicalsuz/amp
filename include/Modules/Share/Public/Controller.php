<?php

require_once( 'AMP/System/Component/Controller/Public.php');
#require_once( '/home/amp/public_html/includes/emaillist_functions.php');

class Share_Public_Controller extends AMP_System_Component_Controller_Public {

    function Share_Public_Controller( ) {
        $this->__construct( );
    }

    function display_default( ){
        //do nothing
    }

    function commit_add( ){
        $intro = &$this->_map->getPublicPage( 'input' );
        if ( $intro )  {
            $this->_set_public_page( $intro );
        }
        return parent::commit_add( ); 
    }

    function commit_save( ) {

        if ( !$this->_form->validate( )) {
            $this->_display->add( $this->form );
            return false;
        }

        $message_data = $this->_form->getValues( );
        if ( $sender_name = $message_data['sender_name'] ) {
            unset( $message_data['sender_name']);
        }

        $target = $this->make_address( $message_data, 'recipient' );
        $sender = $this->make_address( $message_data, 'sender' );
        if ( !( $target && $sender )) return false;

        require_once( 'AMP/System/Email.inc.php');
        $emailer = &new AMPSystem_Email( );
        
        $emailer->setTarget( $target );
        $emailer->setSender( $sender );
        if ( $sender_name ) $emailer->setSenderName( $sender_name );

        $emailer->setSubject( $message_data['subject']);
        $emailer->setMessage( $message_data['message']);

        $result = $emailer->execute( );

        if ( $result ) {
            $message = new AMP_Content_Buffer( );
            $message->add( "<center>Message successfully sent!<br>Thank you!<br><br>[ <a href=\"javascript:window.close()\">Close this window</a> ]</center>" );
            $this->_display->add( $message );
        }

        return $result;
    }

    function make_address( $message_data, $type='recipient' ) {
        if ( !( isset( $message_data[ $type.'_email']) && $message_data[$type.'_email'])) return false;
        $regex = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';
        if (!preg_match($regex, $message_data[$type.'_email'])) {
            return false;
        }

        if ( isset( $message_data[$type.'_name']) && $message_data[$type.'_name']) {
            return $message_data[$type.'_name'] . ' <' . $message_data[$type.'_email'].'>';
        }
        return $message_data[$type.'_email'];
    }

    function commit_cancel( ) {
        $url = $this->assert_var( 'source_url');
        if ( !$url ) $url = AMP_CONTENT_URL_INDEX;
        ampredirect( $url );
        return true;
    }

}

?>
