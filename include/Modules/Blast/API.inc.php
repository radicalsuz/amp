<?php
/* $Id: phplist.module,v 1.21 2005/09/28 16:51:23 killes Exp $ */

/**
 * PHPlist API
 *
 * With deep gratitude to original source acquired from http://civicspacelabs.org and authored by crunchywelch
 *
 * @module Blast 
 * @package Email
 * @author crunchywelch (welch@c-wsolutions.com)
 *
 */

/**
 * @defgroup phplist_core core functions for phplist.
 */

/**
 * Returns settings information for this module.
 *
 * @ingroup phplist_core
 * @return the content for a settings page.
 */

require_once( 'Modules/Blast/ComponentMap.inc.php');
require_once( 'Modules/Blast/Message/Set.inc.php');
require_once( 'Modules/Blast/Message.inc.php');
require_once( 'Modules/Blast/Subscriber/Set.inc.php');
require_once( 'Modules/Blast/Subscriber.inc.php');
require_once( 'Modules/Blast/Subscription.inc.php');
require_once( 'Modules/Blast/Subscription/Set.inc.php');
require_once( 'Modules/Blast/List/Set.inc.php');
require_once( 'Modules/Blast/List.inc.php');

class PHPlist_API {
    function PHPlist_API ( &$dbcon ) {
        $this->_dbcon = &$dbcon;
    }
    function init_settings() {
      $domain = explode('/', AMP_SITE_URL);
      $website = explode('/', AMP_SITE_URL, 3);
      $inits = array( 'website'=>$website[2],
                      'domain'=>$domain[3],
                      //'report_address'=>variable_get('massmailer_report_email', variable_get("site_mail", "")),
                      //'admin_address'=>variable_get('massmailer_report_email', variable_get("site_mail", "")),
                      'public_baseurl'=>AMP_Url_AddVars( AMP_SITE_URL, 'q=phplist'));
      foreach($inits as $key=>$value) {
        $this->_dbcon->Execute(sprintf( ( 'REPLACE INTO '. PHPLIST_TABLE_CONFIG .' (item, value) VALUES (\'%s\', \'%s\')'), $key, $value));
      }

      //variable_set('phplist_version', '2.8.11');

      //  $group = form_item('', t('If you are on a shared host, it will probably be appreciated if you don\'t send out loads of emails in one go. To do this, you can configure batch processing. Please note, the following two values can be overridden by your ISP by using a server wide configuration. So if you notice these values to be different in reality, that may be the case'));
      //  $group .= form_textfield(t('Batch email size'), 'phplist_batch_size', variable_get('phplist_batch_size', 0), 40, 40, t('Define the amount of emails you want to send per period. If 0, batch processing is disabled'));
      //  $group .= form_textfield(t('Batch email period'), 'phplist_batch_period', variable_get('phplist_batch_period', 3600), 40, 40, t('Define the length of one batch processing period, in seconds (3600 is an hour)'));
      //  $group .= form_checkbox(t('Cron queue processing'), 'phplist_cron_enable', 1, variable_get('phplist_cron_enable', 0), t('Checked: when cron is called the message queue will be processed.'));
      //  $output .= form_group(t('Batch Processing'), $group);


      return $output;
    }

    function pageFooter( ) {
        return 'PHPlist powered by <a class="urhere" href="http://www.phplist.com" target="_blank">PHPlist</a>';

    }



    /**
     * Returns default values for massmailer settings.
     *
     * @ingroup phplist_core
     * @return the default value for massmailer settings.
     */
    function settings_description($type) {
      switch ($type) {
        case 'message_footer' :
            return 'Use the following keys to include the various preference and subscription urls in the email footer:<br/>[UNSUBSCRIBE] : The unsubscribe url for the user<br/>[PREFERENCES] : The list preferences url for the user<br/>';
          break;
      }
      
    }

    /**
     * Provides some the phplist menu items.
     * Generally, mailing engines should not have menu items,
     * but in this case we have to redirect and clean the unsubscribe urls.
     *
     * @ingroup massmailer_core
     */
    function phplist_menu($may_cache) {
      $items = array();
      if($may_cache) {
        $items[] = array('href' => AMP_URL_MAILER_ADMIN, 'label' => 'PHPlist Subscription Link', 'callback' => 'phplist_page', 'access' => TRUE, 'type' => MENU_CALLBACK);
      }
      return $items;
    }

    /**
     * Provides some phplist menu rewriting for handling unsubscribe urls.
     *
     * @ingroup massmailer_core
     */
    function phplist_page() {
      #drupal_goto('massmailer/'.$_GET['p'] .'/0/'. $_GET['uid']);
    }

    /**
     * @defgroup phplist_lists list related functions for phplist.
     */

    /**
     * Retrieves a list.
     *
     * @ingroup phplist_lists
     * @param $lid id value of the list
     * @return list object
     */
    function &get_list($list_id) {
        return new BlastList( $this->_dbcon, $list_id );
    }

    /**
     * Retrieves all the lists for a message
     *
     * @ingroup phplist_lists
     * @return array of list object
     */
    function &get_lists($message_id = null) {
        $lists = &new BlastList_Set( $this->_dbcon );
        if(isset( $messeage_id )) {
            $lists->addCriteriaMessage( $message_id ) ;
        }
        $lists->readData( );
        return $lists;
    }

    /**
     * Retrieves all public lists
     *
     * @ingroup phplist_lists
     * @return array of list objects
     */
    function &get_public_lists() {
        $lists = &new BlastList_Set( $this->_dbcon );
        $lists->addCriteriaPublic( );
        $lists->readData( );
        return $lists;
    }

    /**
     * Deletes a list.
     *
     * @ingroup phplist_lists
     * @param $lid id value of the list.
     * @return boolean true if successful.
     */
    function delete_list($list_id) {
        $listSet = &new BlastList_Set( $this->_dbcon );
        return $listSet->deleteList( $list_id );
    }
    /**
     * Creates a list.
     *
     * @ingroup phplist_lists
     * @param $edit The form data submitted from the create list form.
     * @return value of the new list if successful. null if not successful.
     */
    function create_list( $data ) {
        return $this->save_list( $data );
    }
    function save_list( $data ) {
        $list = &new BlastList( $this->_dbcon );
        $list->setData( $data );
        $list->save( );
    }

    /**
     * Updates a list.
     *
     * @ingroup phplist_lists
     * @param $edit The form data submitted from the create list form.
     * @return boolean true if successful.
     */
    function update_list($data) {
        return $this->save_list( $data );
    }

    /**
     * Retrieves the list create form.
     *
     * @param $lid Optional. List id value to populate the form with.
     * @ingroup phplist_lists
     * @return form with the list creation fields.
     */
    function &list_form( $list_id=null) {
        require_once( 'Modules/Blast/List/Form.inc.php' ); 
        return new BlastList_Form( ); 
    }

    /**
     * @defgroup phplist_subscribers subscriber related functions for phplist.
     */

    /**
     * Subscribes or unsubscribes a user to a list.
     *
     * @ingroup phplist_subscribers
     * @param $id The id value of the subscriber.
     * @param $lid The id value of the list.
     * @param $value Boolean true to subscribe, false to unsubscribe.
     * @return boolean true if successful.
     */
    function set_subscriber($user_id, $list_id, $save_flag) {
        $subscriber = &new BlastSubscriber( $this->_dbcon, $user_id );
        if ( $save_flag ) return $subscriber->subscribe( $list_id );
        return $subscriber->unsubscribe( $list_id );
    }

    /**
     * Updates a subscribers information.
     *
     * @ingroup phplist_subscribers
     * @param $user The user object to update
     * @return boolean true if successful.
     */
    function update_subscriber($data) {
        $subscriber = &new BlastSubscriber( $dbcon );
        $subscriber->setData( $data );
        return $subscriber->save( );
    }

    function &create_subscriber( $email, $htmlflag, $foreignkey ) {
        
        if( $sub = $this->get_subscriber_by_email( $email ) ) return $sub;
        $sub = &new BlastSubscriber( $this->_dbcon );
        $sub->create( $email, $htmlflag, $foreignkey );
        return $sub;

    }

    /**
     * Retrieves a subscriber.
     *
     * @ingroup phplist_subscribers
     * @param $id The id value of the subscriber.
     * @return subscriber object.
     */
    function &get_subscriber($id) {
      return new BlastSubscriber( $this->dbcon, $id );
    }
    function &get_subscriber_by_email($email) {
        $userSet = &new BlastSubscriber_Set( $this->_dbcon );
        $userSet->addCriteriaEmail( $email );
        $userSet->readData( );

        if ( !$userSet->hasData( )) return false;
        $user = &new BlastSubscriber( $this->_dbcon );
        $user->setData( $userSet->getData( ));
        return $user;
    }

    /**
     * Retrieves a subscriber by drupal system id.
     *
     * @ingroup phplist_subscribers
     * @param $id The id value of the subscriber.
     * @return subscriber object.
     */
    function &get_subscriber_by_system_id($uid) {
        $subscriberSet = &new BlastSubscriber_Set( $this->_dbcon );
        $subscriberSet->addCriteriaUid( $uid );
        $subscriberSet->readData( );
        if ( !$subscriberSet->hasData( )) return false;
        return $subscriberSet->instantiateItems( array( $subscriberSet->getData( )), 'BlastSubscriber');
    }

    /**
     * Retrieves a subscriber by hash id.
     *
     * @ingroup phplist_subscribers
     * @param $hash The hash value of the subscriber.
     * @return subscriber object.
     */
    function &get_subscriber_by_unique_id($hash) {
        $subscriberSet = &new BlastSubscriber_Set( $this->_dbcon );
        $subscriberSet->addCriteriaUnique( $hash );
        $subscriberSet->readData( );
        if ( !$subscriberSet->hasData( )) return false;
        return $subscriberSet->instantiateItems( array( $subscriberSet->getData( )), 'BlastSubscriber');
    }


    /**
     * Retrieves a list's subscribers.
     *
     * @ingroup phplist_subscribers
     * @param $lid The id value of the list.
     * @return array of subscriber objects.
     */
    function &get_subscribers($list_id ) {
        return new BlastSubscriber_Set( $this->_dbcon, $list_id );
    }

    /**
     * Retrieves all active subscribers.
     *
     * @ingroup phplist_subscribers
     * @return array of subscriber objects.
     */
    function &get_active_subscribers() {
        $list = new BlastSubscriber_Set( $this->_dbcon );
        $list->addCriteriaActive( );
        return $list;
    }

    /**
     * Retrieves all subscribers.
     *
     * @ingroup phplist_subscribers
     * @return array of subscriber objects.
     */
    function &get_all_subscribers() {
        return new BlastSubscriber_Set( $this->_dbcon );
    }

    /**
     * Retrieves a subscriber's lists.
     *
     * @ingroup phplist_subscribers
     * @param $id The id value of the subscriber.
     * @return array of list objects.
     */
    function &get_subscriber_lists($user_id) {
        $listSet = &new BlastList_Set( $this->_dbcon );
        $listSet->addCriteriaSubscriber( $user_id );
        return $listSet;
    }

    /**
     * Checks to se if a subscriber is subscribed to a list.
     *
     * @ingroup phplist_subscribers
     * @param $id The id value of the subscriber.
     * @param $lid The id value of the list.
     * @return boolean true is the user is subscribed to the list.
     */
    function is_subscribed($user_id, $list_id) {
        $subSet = &new BlastSubscription_Set( $this->_dbcon );
        $subSet->addCriteriaUser( $user_id );
        $subSet->addCriteriaList( $list_id );
        $subSet->readData( );
        return $subSet->hasData( );
    }

    /**
     * Deletes a subscriber.
     *
     * @ingroup phplist_subscribers
     * @param $id The id value of the subscriber.
     * @return boolean.
     */
    function delete_subscriber($user_id) {
        $subscriber = &new BlastSubscriber( $this->_dbcon );
        $subSet = &new BlastSubscription_Set( $this->_dbcon );
        return      (   $subscriber->deleteData( $user_id )
                    &&  $subSet->deleteData( 'userid='.$user_id )
                    );
    }

    /**
     * Retrieves a subscriber's statistics.
     *
     * @ingroup phplist_subscribers
     * @param $id The id value of the subscriber.
     * @return themed subscriber statistics content.
     */
    function get_subscriber_stats($id) {
      return array();
    }

    /**
     * Adds an array of subscribers to a list.
     *
     * @ingroup phplist_subscribers
     * @param $subscribers An array of subscriber objects.
     * @param $lid The id value of the list.
     * @return boolean true if successful.
     */
    function add_subscribers($subscribers, $list_id) {
        $result_count = 0;
        foreach ($subscribers as $subscriber_id => $subscriber_email) {
            if ( !$subscriber = &$this->get_subscriber_by_email( $subscriber_email )){
                if( !$subscriber = &$this->create_subscriber( $subscriber_email, $use_html = true, $subscriber_id )) continue;
            }
            if ( $this->is_subscribed( $subscriber->id, $list_id )) continue;
            $this->set_subscriber( $subscriber->id, $list_id, true );
            ++$result_count;
        }
        return $result_count;
    }

    /**
     * @defgroup phplist_message message related functions for phplist.
     */

    /**
     * Retrieves a message.
     *
     * @ingroup phplist_message
     * @param $mid The id value of the message.
     * @return message object.
     */
    function &get_message($message_id) {
        return new BlastMessage( $this->_dbcon, $message_id );
    }

    /**
     * Retrieves messages sent to a list.
     *
     * @ingroup phplist_message
     * @param $lid The id value of the list.
     * @param $unsent Boolean true retrieves only unsent messages.
     * @return assoc array of keys and replacement value descriptions.
     */
    function phplist_get_messages($lid=null, $unsent = false) {
        $messageSet = &new BlastMessage_Set( $this->_dbcon );
        if(!isset( $list_id)) return $messageSet ;

        $messageSet->addCriteriaList( $list_id );
        if ( $unsent ) $messageSet->addCriteriaUnsent( );
        return $messageSet;
      
    }

    /**
     * Deletes a message.
     *
     * @ingroup phplist_lists
     * @param $mid id value of the message.
     * @return boolean true if successful.
     */
    function delete_message($message_id) {
        $messages = &new BlastMessage_Set( $this->dbcon );
        return $messages->deleteMessage( $message_id );
    }

    /**
     * Retrieves a message's statistics.
     *
     * @ingroup phplist_message
     * @param $mid The id value of the list.
     * @return themed message statistics content.
     */
    function get_message_stats($mid) {
      return null;
    }

    /**
     * Returns the replacement key values available for email messages.
     *
     * @ingroup phplist_message
     * @return assoc array of keys and replacement value descriptions.
     */
    function get_email_keys() {
      //echo '_get_email_keys';
      return array();
    }

    /**
     * @defgroup phplist_mail mailing related functions for phplist.
     */

    /**
     * Queues a email message fro delivery.
     *
     * @ingroup phplist_mail
     * @param $lids An array of id values of the lists to send message to.
     */
    function phplist_send_mail( $lids, $mid, $subject, $message_type, $body_header, $body, $footer, $from, $timestamp=null ) {
        $message = &new BlastMessage( $this->_dbcon );
        if ( $mid ) $message->readData( $mid );
        $message_data = array( 
            'subject'   =>  mime_header_encode($subject) , 
            'fromfield' =>  $from,
            'owner'     =>  $_SERVER['REMOTE_USER'], 
            'entered'   =>  date( 'Y-m-d'), 
            'modified'  =>  date( 'YmdHis')
            ) ;
        $message->setFormat( $message_type );
        $message->setEmbargo( $timestamp );
        $message->setMessage( $body, $body_header, $footer );
        $message->submit( );
        foreach($lids as $list_id) {
            $message->queue( $list_id );
        }
    }

    /**
     * Processes the email queue.
     *
     * search for email, if a match is found on a contact, then contact->uid is checked. If it exists then the subscriber.drupalid value is set, allowing the user to maintain their subscription.
     * @ingroup phplist_mail
     * @return html formatted return message.
     */
    function process_queue() {

      return '<pre>'.shell_exec('env -i phplist/bin/phplist -p processqueue -c ../../config.php -d '. $_SERVER['PHP_SELF'] .' -h '. $_SERVER['HTTP_HOST']).'</pre>';
    }


    function phplist_cron() {

      $this->process_queue();
    }

    #if (!function_exists('mime_header_decode')) {
        function mime_header_decode($header) {
          // First step: encoded chunks followed by other encoded chunks (need to collapse whitespace)
          $header = preg_replace_callback('/=\?([^?]+)\?(Q|B)\?([^?]+|\?(?!=))\?=\s+(?==\?)/', '_mime_header_decode', $header);
          // Second step: remaining chunks (do not collapse whitespace)
          return preg_replace_callback('/=\?([^?]+)\?(Q|B)\?([^?]+|\?(?!=))\?=/', '_mime_header_decode', $header);
        }
    #}

    #if (!function_exists('_mime_header_decode')) {
        function _mime_header_decode($matches) {
          // Regexp groups:
          // 1: Character set name
          // 2: Escaping method (Q or B)
          // 3: Encoded data
          $data = ($matches[2] == 'B') ? base64_decode($matches[3]) : str_replace('_', ' ', quoted_printable_decode($matches[3]));
          if (strtolower($matches[1]) != 'utf-8') {
            $data = utf8_encode($data);
          }
          return $data;
        }
    #};

    function __sleep( ){
        $this->dbcon = false;
    }

    function __wakeup( ){
        $this->dbcon = &AMP_Registry::getDbcon( );
    }
}
?>
