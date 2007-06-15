<?php

require_once( 'AMP/Display/System/List.php');
require_once( 'AMP/User/Profile/Profile.php');
require_once( 'AMP/User/List/Request.php');

class AMP_User_Profile_List extends AMP_Display_System_List {

    var $_pager_active = true;
    var $_pager_index = 'Concat( Last_Name, ", ", First_Name ) as name';
    var $_class_pager = 'AMP_Display_System_Pager';
    var $_path_pager = 'AMP/Display/System/Pager.php';

    var $_source_object = 'AMP_User_Profile' ;
    var $_source_criteria = array( 'modin' => 20 );

    var $columns = array( 'select', 'controls', 'name', 'org', 'location', 'contact', 'status' );
    var $_actions = array( 'publish', 'unpublish', 'delete', 'export', 'subscribe', 'email' );
    var $_action_args = array( 'subscribe' => array( 'list_id' ));
    var $_request_class = 'AMP_User_List_Request';

    var $_sort_sql_default = 'name';
    var $_sort_sql_translations = array ( 
        'name'      => 'Last_Name, First_Name',
        'status'    => 'publish, Last_Name, First_Name',
        'org'       => 'Company, Last_Name',
        'location'  => 'Country, State, City, Zip, Last_Name, First_Name',
        'contact'   => 'Email, Phone'
    );

    function AMP_User_Profile_List( $source = false, $criteria = array( )) {
        $this->__construct( $source, $criteria );
    }

    function render_toolbar_subscribe( &$toolbar ) {
        $list_options = AMP_lookup( 'lists');
        if ( !$list_options ) {
            $list_options = array( '' => AMP_TEXT_NONE_AVAILABLE ) ;
        } else {
            $list_options = array( '' => 'Select List' ) + $list_options;
        }

        $list_select = array( $this->_renderer->select( 'list_id', null, $list_options  ) );
        return $toolbar->addTab( 'subscribe', $list_select );
    }

    function render_toolbar_email( &$toolbar ) {
        $default_sender = AMP_BLAST_EMAIL_SENDER ? AMP_BLAST_EMAIL_SENDER : AMP_SITE_ADMIN_EMAIL;

        $custom_labels = array( 
            'message_html' => 'email_message_(HTML_version_)',
            'message_text' => 'email_message_(_text_version)',
            'replyto_email' => 'reply-to_email',
        );

        $fields['subject'] = $this->_renderer->input( 'subject', false, array( 'size' => 60 ) );
        $fields['from_name'] = $this->_renderer->input( 'from_name', AMP_SITE_NAME, array( 'size' => 60) );
        $fields['from_email'] = $this->_renderer->input( 'from_email', $default_sender, array( 'size' => 60 ));
        $fields['replyto_email'] = $this->_renderer->input( 'replyto_email', $default_sender, array( 'size' => 60 ) );
        $fields['message_html'] = $this->_renderer->wysiwyg( 'message_html', false, array( 'rows' => 30, 'cols' => 60 ));
        $fields['message_text'] = $this->_renderer->textarea( 'message_text', false, array( 'rows' => 30, 'cols' => 60 ));

        foreach( $fields as $key => $content ) {
            $base_label = isset( $custom_labels[$key]) ? $custom_labels[$key] : $key;
            $label = ucwords( str_replace( '_', ' ', $base_label ));
            $fields_formatted[$key] = 
                $this->_renderer->div( 
                    $this->_renderer->label( $key, $label )
                    . $this->_renderer->div( $content, array( 'class' => 'element'))
                    , array( 'class' => 'row')
                );
        }

        $tab_content = $this->_renderer->div( 
                            join( $this->_renderer->newline( ), 
                                    $fields_formatted )
                            . $this->_renderer->div( $this->_renderer->space( ), array( 'class' => 'spacer' )),
                            array( 'class' => 'block'));

        return $toolbar->addTab( 'email', $tab_content );
        /*
        require_once( 'AMP/System/Blast/Form.inc.php');
        $email_form = new Blast_Form( );
        $email_form->Build( );
        $email_form->applyDefaults( );
        return $toolbar->addTab( 'email', $email_form->execute( ));
        */

    }

    /*
    function _renderFooter( ) {
        return $this->_renderer->wysiwyg( 'text_item');
    }
    */


}


?>
