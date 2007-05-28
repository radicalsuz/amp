<?php
require_once( 'AMP/UserData/Plugin.inc.php');

class UserDataPlugin_List_Output extends UserDataPlugin {

    var $options = array( 
        'components'    => array( 
                'type'      => 'text',
                'default'   => 'Pager,DisplayHTML,SearchForm',
                'label'     => 'List Components',
                'available' => true ),
        'component_order' =>  array( 
                'type'      => 'text',
                'default'   => 'Messages,SearchForm,Pager,Actions,DisplayHTML,Pager',
                'label'     => 'List Component Order',
                'available' => true ),
        'intro_id_list' => array( 
                'type'      => 'select',
                'default'   => '',
                'label'     => 'Intro Text',
                'available' => true ),
        'format_list_item'  => array(
                'label'     =>  'List Display Function Name',
                'default'   =>  'groups_layout_display',
                'available' =>  true,
                'type'      =>  'text'),
        'subheader' =>  array(
                'available' =>  true, 
                'label'     =>' Show subheadings for',
                'default'   =>  '',
                'type'      =>  'text'),
        'qty_default'   => array (
            'available' =>  true,
            'label'     =>  'Number of results per page',
            'type'      =>  'text',
            'default'   =>  50), 
        'search_form_display'   =>  array(
            'label'     =>  'Show search form',
            'type'      =>  'checkbox',
            'available' => true,
            'default'   =>  0),
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
            'default'   =>  "Last_Name,First_Name")
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
                    'sort_name' => 'default_sortname',
                    'sort_select'   =>  'default_select',
                    'sort_orderby'  =>  'default_orderby')
                );

    var $_alias_fields = array( 
            'Name'=>array(
                'f_alias'=>'Name',
                'f_orderby'=>'Last_Name,First_Name',
                'f_type'=>'text',
                'f_sqlname'=>"Concat(if(!isnull(First_Name), First_Name, ''), ' ', if(!isnull(Last_Name), Last_Name, '') )"
             ),
             'Location'=>array(
                'f_alias'=>'Location',
                'f_sqlname'=>"Concat( if(!isnull(Country), Concat(Country, ' - '),''), if(!isnull(State), Concat(State, ' - '),''), if(!isnull(City), City,''))",
                'f_orderby'=>'(if(Country="USA",1,if(Country="CAN",2,if(isnull(Country),3,Country)))),State,City,Company',
                'f_type'=>'text'),
             'Status'=>array(
                'f_alias'=>'Status',
                'f_orderby'=>'publish',
                'f_type'=>'text',
                'f_sqlname'=>'if(publish=1,"Live","Draft")'
              ));

    var $_components_after_error = 'Messages,SearchForm,Index';
    var $available = true;

    function UserDataPlugin_List_Output( &$udm, $plugin_instance = null ){
        $this->init( $udm, $plugin_instance );
        $this->_verifyBasics( );
    }

    function _register_options_dynamic () {
        if (!$this->udm->admin) return  ;

        require_once( 'AMP/UserData/Lookups.inc.php');
        $introtexts = &FormLookup_IntroTexts::instance( $this->udm->instance );
        $this->options['intro_id_list']['values']    = array( '' => 'None selected') + $introtexts;
    }

    function execute( $options = array( )){
        $options = array_merge( $this->getOptions( ), $options );

        $this->setHeaderTextId( $options['intro_id_list'] );
        $this->initTranslations( );
        $this->_initComponents( );
        $this->readData( $options );

        return $this->_outputComponents( $options );
       
    }

    function _initComponents( ){
        $options = $this->getOptions( );
        $sort_plugin = $this->udm->registerPlugin( 'AMP', 'Sort');
        $this->_translateOptions( $options, 'Sort', $sort_plugin );
        $search_plugin = $this->udm->registerPlugin( 'AMP', 'Search');
        $this->_translateOptions( $options, 'Sort', $search_plugin );
        if ( isset( $options['components'])){
            $component_set = $options['components'];
            if ( !is_array( $options['components'])) $component_set = preg_split( '/\s?,\s?/', $options['components']);
        }
        foreach( $component_set as $action ){
            $result = &$this->udm->registerPlugin( 'Output', $action );

            if ( !$result ){
                trigger_error( sprintf( AMP_TEXT_ERROR_USERDATA_PLUGIN_REGISTRATION_FAILED, 'Output', $action ));
                continue;
            }

            $this->_translateOptions( $options, $action, $result );
        }
    }

    function _outputComponents( $options ){

        if ( $this->udm->hasErrors( )) $options['component_order'] = $this->_getErrorOptions( );
        
        $order = preg_split( '/\s?,\s?/', $options['component_order']);
        $plugins_set = $this->udm->getPlugins( );
        $active_components = array_combine_key( $order, $plugins_set ) ;

        $output_html = "";
        foreach( $order as $action ){
            $component_set = & $plugins_set[$action] ;
            if ( !is_array( $component_set )) continue;
            foreach( $component_set as $namespace => $component ){
                $output_html .= $component->execute();

            }
        }
        return $output_html;
    }

    function readData( $options ){
        $this->udm->doAction( 'Search') ;
    }

    function _translateOptions( $options, $plugin_action, &$plugin ){
        $result_options = null;
        if ( !isset( $this->_options_translations[ $plugin_action ])) return $result_options;

        $result_options = array( );
        $translation_set = $this->_options_translations[ $plugin_action ];
        $sub_options = $plugin->getOptions( );

        foreach( $translation_set as $global_key => $legacy_key ){
            if ( !isset( $options[ $global_key ])) continue;
            if ( isset( $plugin->plugin_instance ) 
                && isset( $sub_options[$legacy_key]) 
                && ( $options[$global_key] == $this->options[ $global_key]['default'])) 
                    continue;
            $result_options[ $legacy_key ] = $options[ $global_key ];
        }

        $plugin->setOptions( $result_options );
        return $result_options;

    }

    function _getErrorOptions( ){
        return $this->_components_after_error;
    }

    function setHeaderTextId( $id ){
        return $this->udm->setIntrotextId( $id );
    }

    function initTranslations( ) {
        //DisplayHTML;
        //used to be setAliases;
        $this->udm->alias = $this->_alias_fields;
    }
}
?>
