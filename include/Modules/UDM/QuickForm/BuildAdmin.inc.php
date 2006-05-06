<?php

/*****
 *
 * AMP UserDataModule HTML_QuickForm builder Plugin
 *
 * Creates an HTML_QuickForm object based on the contents of
 * an UDM object.
 *
 *****/

require_once( 'HTML/QuickForm.php' );
require_once( 'HTML/QuickForm/Renderer/Savant.php' );
require_once( 'Savant/Savant2.php' );
require_once( 'AMP/UserData/Plugin.inc.php' );
require_once( 'AMP/UserData/Lookups.inc.php');

class UserDataPlugin_BuildAdmin_QuickForm extends UserDataPlugin {

    var $fields = Array();
    var $form;
    var $regions;

    var $_field_plugin_def = array( 
        'priority' => array( 
            'type'  => 'text',
            'size'  => '2',
            'default' => null,
            'label' => 'Priority' ),
        'active'    => array( 
            'type'  => 'checkbox',
            'value' => true,
            'label' => 'Active'),
    /*    'remove'    => array( 
            'type'  => 'checkbox',
            'label' => 'Remove'),*/
        'id'        => array( 
            'available' => true,
            'default' => null,
            'type'  => 'hidden' ));
    var $available = false;

    function UserDataPlugin_BuildAdmin_QuickForm ( &$udm ) {
        $this->init( $udm );
    }

    function execute ( $options = null ) {

        $form_name   = $this->udm->name;
        $form_method = ( isset($options['frmMethod']) ) ?
                       $options['frmMethod'] : 'post';
        $form_action = ( isset($options['frmAction']) ) ?
                       $options['frmAction'] : null;

        $this->form = &new HTML_QuickForm( $form_name, $form_method, $form_action );

        $this->form->addElement( 'hidden', 'modin',        'Module Instance' );
        $this->form->addElement( 'submit', 'btnUdmSubmit', 'Submit' );

        $this->form->registerElementType('multiselect','HTML/QuickForm/select.php','HTML_QuickForm_select');
        $this->form->registerElementType('radiogroup','HTML/QuickForm/group.php','HTML_QuickForm_group');
        $this->form->registerElementType('checkgroup','HTML/QuickForm/group.php','HTML_QuickForm_group');
        $this->form->registerElementType('wysiwyg','HTML/QuickForm/textarea.php','HTML_QuickForm_textarea');
        

        $this->_build_core_fields();
        $this->_build_fields();
        $this->_build_plugins();
        $this->_build_preview();

        $this->form->setDefaults( $this->udm->_module_def );
        $this->form->setConstants( array( 'modin' => $this->udm->instance ) );

        $tpl =& new Savant2();
        $tpl->addPath('template', $_SERVER['DOCUMENT_ROOT'] . '/include/templates/UserData');

        $renderer =& new HTML_QuickForm_Renderer_Savant(false, false, $tpl);
        $renderer->setTemplate('admin.php.tpl');

        $this->form->accept($renderer);

        $this->udm->form = $this->form;

    }

    function _build_form ( $fields, $prefix = '' ) {

        if ($prefix) $prefix .= "_";

        foreach ( $fields as $field_name => $field ) {

            $label  = (isset($field['label']))  ? $field['label']  : null;
            $values = (isset($field['values'])) ? $field['values'] : null;

            $el = &$this->form->addElement( $field['type'],
                                     $prefix . $field_name,
                                     $label,
                                     $values );
            if ( isset( $field['size']) && $field['type'] == 'text') $el->setSize( $field['size']);

        }
    }

    function _read_plugins( ){
        $plugins = $this->udm->plugins;
        $option_values = array( );
        $plugin_settings = array( );
        $plugin_ids = array( );
        $plugin_priorities = FormLookup::instance( 'pluginPriorities');
        foreach( $plugins as $action => $plugin_def ){
            foreach( $plugin_def as $namespace => $plugin ){
                $prefix = join( '_', array( 'plugin', $action, $namespace ));

                if ( !$plugin->plugin_instance ) continue;

                $plugin_ids[ $prefix . '_plugin_id'] = $plugin->plugin_instance;
                $plugin_settings[ $prefix . '_plugin_priority'] = $plugin_priorities[ $plugin->plugin_instance ];
                $plugin_settings[ $prefix . '_plugin_active'] = true;
                
            }
        }
        
        $this->form->setDefaults( $plugin_settings );
        $this->form->setDefaults( $plugin_ids );
        $this->form->setConstants( $plugin_ids );
        
        
    }

    function _build_core_fields () {

        $dbcon =& $this->udm->dbcon;

        $modules_blank_row[ '' ] = '--';
        $modules = $modules_blank_row + FormLookup_IntroTexts::instance( $this->udm->instance );

        $lists_blank_row[ '' ] = 'none';
        $lists = $lists_blank_row + AMPSystem_Lookup::instance( 'lists' );

        $fields =& $this->fields;

        $fields['core'] = Array( 'tab' => array( 'type' => 'header', 'label' => 'Settings', 'values' => 'Settings' ) );

        $fields['core']['name']          = array( 'label' => 'Name',                 'type' => 'text' );
        $fields['core']['publish']       = array( 'label' => 'Publish Data',         'type' => 'checkbox' );
        $fields['core']['modidinput']    = array( 'label' => 'Intro Text',           'type' => 'select', 'values' => $modules );
        $fields['core']['modidresponse'] = array( 'label' => 'Response Text',        'type' => 'select', 'values' => $modules );
        $fields['core']['uselists']      = array( 'label' => 'Use Lists',            'type' => 'checkbox' );
        $fields['core']['list1']         = array( 'label' => 'List #1',              'type' => 'select', 'values' => $lists );
        $fields['core']['list2']         = array( 'label' => 'List #2',              'type' => 'select', 'values' => $lists );
        $fields['core']['list3']         = array( 'label' => 'List #3',              'type' => 'select', 'values' => $lists );
        $fields['core']['list4']         = array( 'label' => 'List #4',              'type' => 'select', 'values' => $lists );
        $fields['core']['useemail']      = array( 'label' => 'Use E-Mail',           'type' => 'checkbox' );
        $fields['core']['mailto']        = array( 'label' => 'Mail to',              'type' => 'text' );
        $fields['core']['subject']       = array( 'label' => 'E-mail Subject',       'type' => 'text' );
        $fields['core']['field_order']   = array( 'label' => 'Field Order',          'type' => 'textarea' );

        // Fixup the module definition to account for different names.
        $md = &$this->udm->_module_def;
        foreach ( array_keys( $fields['core'] ) as $field ) {
            if ( isset( $md[$field] ) ) {
                $md[ "core_$field" ] = $md[ $field ];
            }
        }

        $this->_build_form( $fields['core'], 'core' );

    }

    function _build_fields () {

        $fields =& $this->fields;

        $fields['standard'] = Array( 'standard_tab' => array( 'type' => 'header', 'label' => 'Standard Fields', 'values' => 'Standard Fields' ) );
        $fields['custom']   = Array( 'custom_tab'   => array( 'type' => 'header', 'label' => 'Custom Fields',   'values' => 'Custom Fields' ) );

        foreach ( $this->udm->fields as $field_name => $field ) {

            if ( strpos($field_name, "custom") === 0 ) {
                $group = 'custom';
            } elseif ( strpos($field_name, "plugin") === 0 ) {
                # Plugin fields are handled elsewhere.
                continue;
            } else {
                $group = 'standard';
            }
            $fields[$group] += $this->_build_field( $field_name, $field );
        }

        $this->_build_form( $fields['standard'] );
        $this->_build_form( $fields['custom']   );
    }

    function _build_plugins () {

        $actions = $this->udm->getPlugins();

        $fields =& $this->fields;

        $this->_build_form( Array( 'plugins_tab' => Array( 'type' => 'header', 'values' => 'Plugins' ) ) );

        // because it's a hierselect, it just adds the field to the form.
        $this->_build_plugins_add();

        // We use this to fill in default values.
        $md = &$this->udm->_module_def;
        $plugin_priorities = FormLookup::instance( 'pluginPriorities');

        foreach ( $actions as $action => $plugins ) {

            foreach ( $plugins as $namespace => $plugin ) {

                # Skip plugins that aren't available for modification.
                if (!$plugin->available) continue;

                $plugin_fields =& $fields['plugins'][$action][$namespace];

                $plugin_name   = "plugin_$action" . "_$namespace";
                $plugin_fields = Array();
                $option_values = $plugin->getOptions( );

                if (isset( $plugin->options )) {
                    foreach ( $plugin->options as $option_name => $option ) {
                        if (!isset($option['available']) || !$option['available']) continue;
                        $plugin_fields[$option_name] = $option;
                        if ( isset( $option_values[$option_name])) {
                            $md[$plugin_name . "_$option_name"] = $option_values[ $option_name ];
                        }
                        /*
                        print $option_name;
                        AMP_varDump( $option );
                        */
                    }
                }

                foreach( $this->_field_plugin_def as $short_name => $field_def ){
                    $field_name =   'plugin_' . $short_name ;
                    $plugin_fields[ $field_name ] = $field_def ;

                }
                $md[$plugin_name . "_plugin_active"] = true;
                if ( $plugin->plugin_instance ) {
                    $md[$plugin_name . "_plugin_id"] = $plugin->plugin_instance; 
                    $md[$plugin_name . "_plugin_priority"] = $plugin_priorities[ $plugin->plugin_instance ];

                    $plugin_fields[ 'plugin_id']['default'] = $plugin->plugin_instance;
                    $plugin_fields[ 'plugin_priority']['default'] = $plugin_priorities[ $plugin->plugin_instance ];

                }
                
                /* this is deprecated, i hope we never implement it
                if (isset( $plugin->fields )) {
                    foreach ( $plugin->fields as $field_name => $field ) {
                        $plugin_fields[$field_name] = $this->_build_field( $field_name, $field );
                    }
                }
                */

                if (count($plugin_fields) > 0) {
                    $header = Array("heading" => Array( 'type' => 'static', 'values' => "<h3>$namespace: $action</h3>" ));
                    $plugin_fields = $header + $plugin_fields;
                    $this->_build_form( $plugin_fields, $plugin_name );
                }

            }
        }
    }

    function _build_plugins_add() {

        $plugins = $this->_find_available_plugins();

        $i = 0;
        foreach ( $plugins as $namespace => $actions ) {

            $plugin_add_namespaces[$i] = $namespace;
            $plugin_add_actions[$i] = array_keys( $actions );

            $i++;

        }

        $this->_build_form( Array("plugin_add_notice" => Array('type' => 'static', 'values' => '<h3>Add A Plugin</h3>')));

        $plugin_hierselect =& $this->form->addElement('hierselect', 'plugin_add', 'Add a Plugin:');
        $plugin_hierselect->setOptions( array($plugin_add_namespaces, $plugin_add_actions) );

        $add_button  =& $this->form->addElement( 'button', 'plugin_add_btn', null, 'onclick="addPlugin()" id="plugin_add_btn"' );
        $add_button->setType( 'button' );
        $add_button->setValue( 'Add It' );

    }

    function _find_available_plugins() {

        $available_plugins = Array();

        #$udm_plugin_path_base = preg_replace( "/QuickForm/i", "", dirname( realpath( __FILE__ ) ) );
        $udm_plugin_path_base = AMP_BASE_INCLUDE_PATH . 'Modules/UDM/';
        $udm_plugin_path_local = AMP_LOCAL_PATH . '/lib/Modules/UDM/';

        $dh_set['base'] = opendir( $udm_plugin_path_base );
        $dh_set['local'] = ( file_exists( $udm_plugin_path_local ))? opendir( $udm_plugin_path_local ) : false;

        foreach( $dh_set as $dh_key => $dh ){
            while ($dh && ($namespace = readdir($dh)) !== false) {

                if (strpos($namespace, ".") === 0) continue;
                $subfolder = 'udm_plugin_path_' . $dh_key;

                $nsdh = opendir( $$subfolder . $namespace );

                while (($action_file = readdir($nsdh)) !== false) {

                    if (strpos($action_file, ".") === 0) continue;

                    // include the file, suppress error messages, and don't let it
                    // output anything. To my knowledge, there's no way to stop php4
                    // from dying on fatal error, so be careful out there.
                    ob_start();
                    if(!include_once( "Modules/UDM/" . ucfirst($namespace) . "/" . ucfirst($action_file) )) continue;
                    ob_end_clean();

                    $action = preg_replace( "/\.inc\.php$/", "", $action_file );
                    $class_vars = get_class_vars( "UserDataPlugin_" . $action . "_$namespace" );

                    if ($class_vars['available'] != true) continue;

                    $available_plugins[$namespace][$action] = $class_vars;
                }
            }
        }

        return $available_plugins;
    }

    function _build_preview () {

        $udm_copy = &new UserDataInput( $this->dbcon, $this->udm->instance, true );
        $udm_copy->form = null;
        $udm_copy->showForm = true;

        $html = $udm_copy->output('html');

        preg_replace( "/<[^>]*form[^>]*>/", "", $html );

        $this->fields['preview'] = Array(
                    'tab'      => Array( 'type' => 'header', 'values' => 'Preview' ),
                    'rendered' => Array( 'type' => 'static', 'values' => $html     )
        );

        $this->_build_form( $this->fields['preview'], 'preview' );

    }

    function _build_field ( $field_name, $field ) {

        $fn = $field_name;

        $jscript = "onclick=\"changef('$fn');\"";
        $fa_js = "<img src=\"images/arrow-right.gif\" border=\"0\" class=\"field_arrow\" id=\"arrow_$fn\" $jscript />";

        $label    = $field[ 'label'  ];
        $flabel = ( $label ) ? "$label <span class=\"fieldname\">(<em>$fn</em>)</span>" :
                               "Unnamed Field <span class=\"fieldname\">(<em>$fn</em>)</span>";

        // Do this in the absence of array_combine.
        $available_types = $this->form->getRegisteredTypes();
        foreach ( $available_types as $ftype ) {
            $types[ $ftype ] = $ftype;
        }

        $lookups = array( '' => '--' ) + AMPSystem_Lookup::instance( 'lookups');
        #$regions = array( '' => '--' ) + $this->regions->getTLRegions();

        $elements = Array(
            "arrow_$fn"    => Array( 'type' => 'static',   'values' => $fa_js     ),
            "title_$fn"    => Array( 'type' => 'static',   'values' => $flabel    ),
            "enabled_$fn"  => Array( 'type' => 'checkbox', 'values' => 'enabled'  ),
            "public_$fn"   => Array( 'type' => 'checkbox', 'values' => 'public'   ),
            "required_$fn" => Array( 'type' => 'checkbox', 'values' => 'required' ),
            "type_$fn"     => Array( 'type' => 'select',   'values' => $types,    'label' => 'Type' ),
            "label_$fn"    => Array( 'type' => 'text',     'values' => null,      'label' => 'Label' ),
            "lookup_$fn"   => Array( 'type' => 'select',   'values' => $lookups,  'label' => 'Dynamic Lookup' ),
            "values_$fn"   => Array( 'type' => 'textarea', 'values' => null,      'label' => 'Default Values' ),
            "size_$fn"     => Array( 'type' => 'text',     'values' => null,      'label' => 'Field Size', 'size' => 3 ),
        );

        return $elements;

    }

}

/*
function udm_QuickForm_build_admin ( &$udm, $options = null ) {

    $frmName    = $udm->name;
    $frmMethod  = ( isset( $options['frmMethod'] ) ) ?
                    $options['frmMethod'] : 'post';
    $frmAction  = ( isset( $options['frmAction'] ) ) ?
                    $options['frmAction'] : null;

    $form = new HTML_QuickForm( $frmName, $frmMethod, $frmAction );

    $form->addElement( 'hidden', 'modin', 'Module Instance' );
    $form->addElement( 'submit', 'btnUdmSubmit', 'Save' );

    /* Fetch module information.
        this should be moved to some generic AMP class or somesuch.
    *//*

    $modlist_rs = $udm->dbcon->CacheExecute( "SELECT moduletext.id, moduletext.name FROM moduletext, modules" .
                                             " WHERE modules.id=moduletext.modid AND " .
                                             " modules.userdatamodid=" . $udm->dbcon->qstr($udm->instance) .
                                             " ORDER BY name ASC" )
        or die( $udm->dbcon->ErrorMsg() );

    $modules[ '' ] = '--';
    while ( $row = $modlist_rs->FetchRow() ) {
        $modules[ $row['id'] ] = $row['name'];
    }

    /* Get possible sources. Again, should be moved out of here */
/*
    $source_rs = $udm->dbcon->CacheExecute( "SELECT id, title FROM source ORDER BY title ASC" )
        or die( $udm->dbcon->ErrorMsg() );

    while ( $row = $source_rs->FetchRow() ) {
        $sources[ $row[ 'id' ] ] = $row[ 'title' ];
    }

    /* Yet another thing to move outta here */
/*
    $enteredby_rs = $udm->dbcon->CacheExecute( "SELECT id, name FROM users ORDER BY name ASC" );

    while ( $row = $enteredby_rs->FetchRow() ) {
        $users[ $row['id'] ] = $row['name'];
    }

    /* Another one. */
 /*   $MM_listtable = ( isset($GLOBALS['MM_listtable']) ) ? $GLOBALS['MM_listtable'] : 'lists';
    $lists_rs = $udm->dbcon->Execute( "SELECT id, name FROM $MM_listtable ORDER BY name ASC" ) or die( "Couldn't obtain list information: " . $udm->dbcon->ErrorMsg() );

    $lists[ '' ] = 'none';
    while ( $row = $lists_rs->FetchRow() ) {
        $lists[ $row['id'] ] = $row['name'];
    }

    $fields =& $udm->fields;
    $fields['core_name']          = array( 'label' => 'Name',                 'type' => 'text' );
    $fields['core_redirect']      = array( 'label' => 'Redirect URL',         'type' => 'text' );
    $fields['core_publish']       = array( 'label' => 'Publish Data',         'type' => 'checkbox' );
    $fields['core_modidinput']    = array( 'label' => 'Intro Text',           'type' => 'select', 'values' => $modules );
    $fields['core_modidresponse'] = array( 'label' => 'Response Text',        'type' => 'select', 'values' => $modules );
    $fields['core_sourceid']      = array( 'label' => 'Source',               'type' => 'select', 'values' => $sources );
    $fields['core_enteredby']     = array( 'label' => 'Entered By',           'type' => 'select', 'values' => $users );
    $fields['core_uselists']      = array( 'label' => 'Use Lists',            'type' => 'checkbox' );
    $fields['core_list1']         = array( 'label' => 'List #1',              'type' => 'select', 'values' => $lists );
    $fields['core_list2']         = array( 'label' => 'List #2',              'type' => 'select', 'values' => $lists );
    $fields['core_list3']         = array( 'label' => 'List #3',              'type' => 'select', 'values' => $lists );
    $fields['core_list4']         = array( 'label' => 'List #4',              'type' => 'select', 'values' => $lists );
    $fields['core_useemail']      = array( 'label' => 'Use E-Mail',           'type' => 'checkbox' );
    $fields['core_mailto']        = array( 'label' => 'Mail to',              'type' => 'text' );
    $fields['core_subject']       = array( 'label' => 'E-mail Subject',       'type' => 'text' );
    $fields['core_field_order']   = array( 'label' => 'Field Order',          'type' => 'textarea' );

    $md =& $udm->_module_def;

    $coreFields = array( 'redirect', 'publish', 'modidinput', 'modidresponse', 'sourceid', 'enteredby', 'uselists', 'list1', 'list2', 'list3', 'list4', 'useemail', 'mailto', 'subject', 'field_order', 'name' );
    foreach ( $coreFields as $cf ) {
        $md[ 'core_' . $cf ] = $md[ $cf ];
    }

/*    $fSep = array( "</td><td>", // down arrow -> field name
                   "</td><td>", // field name -> enabled
                   "</td><td>", // enabled -> public
                   "</td><td>", // public -> required
                   "</td></tr><tr><td colspan=\"5\">", // required -> hidden stuff, type
                   "</td><td colspan=\"2\">", // type -> label
                   "</td></tr><tr><td>", // label -> region
                   "</td><td>", // region -> default values
                   "</td><td>", // default values -> field size
                   "</td></tr></table></div></td></tr></table>\n\n", // field size -> end of row
                 ); */
/*                 $fSep = '';

    $panels = array( 'core', 'standard', 'custom', 'plugins', 'preview' );
/*    foreach ( $panels as $panel ) {
        $renderer->setGroupTemplate("\n\n<div id=\"udm_$panel\" style=\"display: none;\">{content}</div>", $panel);
        $renderer->setGroupElementTemplate( "{label}&nbsp;{element}\n", $panel );
        $renderer->setElementTemplate("{label}&nbsp;{element}</div>", $panel);
    } */
/*
    $form->addGroup( array(), 'core', null, '&nbsp;', false );
    $form->addGroup( array(), 'standard', null, $fSep, false );
    $form->addGroup( array(), 'custom', null, $fSep, false );
    $form->addGroup( array(), 'plugins', null, '&nbsp;', false );
    $form->addGroup( array(), 'preview', null, '&nbsp;', false );


/*
    $renderer =& $form->defaultRenderer();
    $renderer->setGroupTemplate( "\n</td></tr></table><div id=\"udm_core\"><table width=\"100%\" class=\"name\">{content}</table></div><table width=\"100%\" class=\"name\"><tr><td>\n", 'core' );

    $renderer->setGroupTemplate( "\n</td></tr></table>\n\n<br clear=\"all\">\n<div id=\"udm_standard\" style=\"display: none\"><!-- begin standard content --><table width=\"100%\" class=\"name\"><tr><td>{content}</td></tr></table></div></td></tr></table></div>\n<table width=\"100%\" class=\"name\"><tr><td width=\"12\">", 'standard' );

    $renderer->setGroupTemplate( "\n</td></tr></table>\n\n<br clear=\"all\">\n<div id=\"udm_custom\" style=\"display: none\"><table width=\"100%\" class=\"name\"><tr><td width=\"12\">{content}</td></tr></table></div></td></tr></table></div>\n<table width=\"100%\" class=\"name\"><tr><td>", 'custom' );

    $renderer->setGroupTemplate( "\n</td></tr></table>\n\n<br clear=\"all\">\n<div id=\"udm_plugins\" style=\"display: none\"><table width=\"100%\" class=\"name\"><tr><td>{content}</td></tr></table></div></td></tr></table></div>\n<table width=\"100%\" class=\"name\"><tr><td>", 'plugins' );
    $renderer->setGroupTemplate( "\n</td></tr></table>\n\n<br clear=\"all\">\n<div id=\"udm_preiew\" style=\"display: none\"><table width=\"100%\" class=\"name\"><tr><td>{content}</td></tr></table></div></td></tr></table></div>\n<table width=\"100%\" class=\"name\"><tr><td>", 'preview' );
    $renderer->setGroupElementTemplate( "<tr><td>{label}</td><td>{element}</td></tr>\n", 'core' );
    $renderer->setGroupElementTemplate( "<tr><td>{label}</td><td>{element}</td></tr>\n", 'plugins' );
    $renderer->setGroupElementTemplate( "{label}&nbsp;{element}\n", 'standard' );
    $renderer->setGroupElementTemplate( "{label}&nbsp;{element}\n", 'preview' );
    $renderer->setGroupElementTemplate( "{label}&nbsp;{element}\n", 'custom' );
*/
/*
    foreach ( $udm->fields as $field => $field_def ) {
        udm_QuickForm_build_admin_addElement( $form, $field, $field_def );
    }

    $preview =& $form->getElement('preview');
    $pr_elem =& $preview->getElements();


    $plugin_gr =& $form->getElement('plugins');
    $pl_elem =& $plugin_gr->getElements();

    $plugins = $udm->getPlugins();

    udm_quickform_build_admin_plugins( $plugins, $pl_elem );

    ob_start();
    $plug_view = array();
    foreach ($plugins as $plugin => $namespace) {
        foreach ($namespace as $ns => $pl) {
            $plug_view[$ns][$plugin] = $pl->options;
        }
    }
    print_r($pl_elem);
    $pl_raw = ob_get_contents();
    ob_end_clean();

    $tmpUdm = $udm;
    $tmpUdm->form = null;
    $tmpUdm->showForm = true;

    ob_start();
    $tmpUdm->output('html');
    $preview_html = ob_get_contents();
    ob_end_clean();

    $pr_elem[] = &HTML_QuickForm::createElement( 'html', $preview_html );

    $form->setDefaults( $md );

    $form->setConstants( array( 'modin' => $udm->instance ) );

    $tpl =& new Smarty;
    $tpl->template_dir = $_SERVER['DOCUMENT_ROOT'] . '/include/templates/UserData';
    $tpl->compile_dir  = AMP_LOCAL_PATH . '/cache';

    $tpl->assign('static', $pl_raw);

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($tpl, true);
    $renderer->setTemplate('admin.tpl');

    $form->accept($renderer);

    if ( !isset( $options[ 'no_validate' ] ) ) {
        if ( !$form->validate() ) {
            $udm->form = $form;
            return false;
        }
    }

    $udm->form = $form;

    return $form;
    
}

function udm_QuickForm_build_admin_plugins ( $plugins, &$elements ) {

    foreach ( $plugins as $action => $plugin ) {

        $elements[] = &HTML_QuickForm::createElement( 'static', $action );

        foreach ( $plugin as $namespace => $plugin_obj ) {

            if (!$plugin_obj->available ) continue;
            $elements[] = &HTML_QuickForm::createElement( 'static', $namespace );
            udm_QuickForm_build_admin_plugins_options( $namespace, $plugin, $elements );
        }
    }
}

function udm_QuickForm_build_admin_plugins_options( $namespace, $plugin, &$elements ) {

    foreach ( $plugin['options'] as $option => $option_def ) {
        print "I'm doing something here....";
        $element = &HTML_QuickForm::createElement( $option_def['type'],
                                                   $namespace . $plugin['short_name'] . $option,
                                                   $option_def['description'],
                                                   $option_def['default'] );

        if ( isset($option_def['size']) ) $element->setSize( $option_def['size'] );
        
        $elements[] = $element;
    }
}

function udm_QuickForm_build_admin_addElement( &$form, $name, $field_def ) {

    if ( $name == '' ) return;

    $type     = $field_def[ 'type'   ];
    $label    = $field_def[ 'label'  ];
    $defaults = ( isset($field_def['values']) ) ? $field_def[ 'values' ] : null;
    foreach ( array_values( $form->getRegisteredTypes() ) as $ftype ) {
        $types[ $ftype ] = $ftype;
    }
    $types[ 'HTML_QuickForm_select' ] = 'HTML_QuickForm_select';

    if ( substr( $name, 0, 6 ) == "custom" ) {
        $groupName = "custom";
    } elseif ( substr( $name, 0, 4 ) == "core" ) {
        $groupName = "core";
    } else {
        $groupName = "standard";
    }

    $group =& $form->getElement( $groupName );
    $elements =& $group->getElements();

    $jscript = "onclick=\"changef('$name');\"";
    $regions = array( '' => '--' ) + $GLOBALS['regionObj']->getTLRegions();

    if ( $groupName != "core" ) {
        $elements[] = &HTML_QuickForm::createElement( 'static', 'arrow_' . $name, null, '<img src="images/arrow-right.gif" border="0" class="field_arrow" id="arrow_' . $name . "\" $jscript />" );
        $elements[] = &HTML_QuickForm::createElement( 'static', 'title_' . $name, null, ( $label ) ? $label . " <span class=\"fieldname\">(<em>$name</em>)</span>" : 'Unnamed Field' . " <span class=\"fieldname\">(<em>$name</em>)</span>" );
        $elements[] = &HTML_QuickForm::createElement( 'checkbox', 'enabled_' . $name, null, 'enabled' );
        $elements[] = &HTML_QuickForm::createElement( 'checkbox', 'public_' . $name, null, 'public' );
        $elements[] = &HTML_QuickForm::createElement( 'checkbox', 'required_' . $name, null, 'required' );
        $elements[] = &HTML_QuickForm::createElement( 'select', 'type_' . $name, 'Type', $types );
        $elements[] = &HTML_QuickForm::createElement( 'text', 'label_' . $name, 'Label' );
        $elements[] = &HTML_QuickForm::createElement( 'select', 'region_' . $name, 'Region', $regions);
        $elements[] = &HTML_QuickForm::createElement( 'textarea', 'values_' . $name, 'Default Values' );
        $fieldSize = &HTML_QuickForm::createElement( 'text', 'size_' . $name, 'Field Size' );
        $fieldSize->setSize( '3' );
        $elements[] = $fieldSize;
    } else {
        $elements[] = &HTML_QuickForm::createElement( $type, $name, $label, $defaults );
    }

    return 1;

}
*/

?>
