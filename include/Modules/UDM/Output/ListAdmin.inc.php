<?php
require_once( 'Modules/UDM/Output/List.inc.php');

class UserDataPlugin_ListAdmin_Output extends UserDataPlugin_List_Output {

    var $options = array( 
        'components'    => array( 
            'type'      => 'text',
            'default'   => 'Messages,SearchForm,Pager,Actions,TableHTML,Pager',
            'values'    => 'Messages,SearchForm,Pager,Actions,DisplayHTML,Index,TableHTML',
            'label'     => 'List Components',
            'available' => true ),
        'component_order' =>  array( 
            'type'      => 'text',
            'default'   => 'Messages,SearchForm,Pager,Actions,TableHTML,Pager',
            'label'     => 'List Component Order',
            'available' => true ),
        'qty_default'   => array (
            'available' =>  true,
            'label'     =>  'Number of results per page',
            'type'      =>  'text',
            'default'   =>  200), 
        'search_form_display'   =>  array(
            'label'     =>  'Show search form',
            'type'      =>  'checkbox',
            'available' => true,
            'default'   =>  1),
        'sort_name'     =>  array(
            'default'   =>  "Name",
            'available' =>  true,
            'type'      =>  'text',
            'label'     =>  'Text name of default sort'),
        'sort_select'   =>  array(
            'available' =>  true,
            'label'     =>  'SELECT SQL phrase for sorting',
            'type'      =>  'textarea',
            'default'   =>  "Concat(First_Name,' ',Last_Name) as Name"),
        'sort_orderby'  =>  array(
            'available' =>  true,
            'label'     =>  'ORDER BY SQL phrase for sorting',
            'type'      =>  'textarea',
            'default'   =>  "Last_Name,First_Name"),
        'list_columns'    =>  array( 
            'label'   => 'Select SQL phrase for display fields',
            'type'    => 'textarea',
            'available' => true,
            'default' => 'id,Name,Company,State,Phone,Status'),
        'list_editlink'          =>  array( 
            'label' => 'System page to link to',
            'available' => true,
            'type'  => 'text',
            'default' => 'modinput4_view.php')
        );

    var $_options_translations = array( 
                'DisplayHTML' => array( 
                    'format_list_item'  =>   'display_format',
                    'subheader'         =>   'subheader',
                    'intro_id_list'     =>   'header_text_list'),
                'Pager'         => array( 
                    'qty_default'   =>  'max_qty'),
                'SearchForm'    => array( 
                    'search_form_display' => 'search_form_display'),
                'Sort'          => array( 
                    'sort_name'     =>  'default_sortname_admin',
                    'sort_select'   =>  'default_select_admin',
                    'sort_orderby'  =>  'default_orderby_admin'),
                'TableHTML'     =>  array( 
                    'list_columns'  =>  'display_fields',
                    'list_editlink' =>  'editlink')
                );

    function UserDataPlugin_ListAdmin_Output( &$udm, $plugin_instance = null ){
        $this->init( $udm, $plugin_instance );
    }
}

?>
