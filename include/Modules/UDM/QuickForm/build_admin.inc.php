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
require_once( 'AMP/Region.inc.php' );

$GLOBALS['regionObj'] = new Region();

function udm_QuickForm_build_admin ( $udm, $options = null ) {
    $admin      = ( isset( $options[ 'admin' ] ) ) ?
                    $options['admin'] : false;

    $frmName    = $udm->name;
    $frmMethod  = ( isset( $options['frmMethod'] ) ) ?
                    $options['frmMethod'] : 'post';
    $frmAction  = ( isset( $options['frmAction'] ) ) ?
                    $options['frmAction'] : null;

    $form = new HTML_QuickForm( $frmName, $frmMethod, $frmAction );

    $form->addElement( 'hidden', 'modin', 'Module Instance' );
    $form->addElement( 'submit', 'btnUdmSubmit', 'Submit' );

    if ( isset( $udm->uid ) ) {
        $form->addElement( 'hidden', 'uid', 'User ID' );
        $form->setConstants( array( 'uid' => $udm->uid ) );
    }

    $tabs = '<tr><td colspan="2" style="border-bottom: 1px solid grey; margin: 0; padding: 0;"><br /><script type="text/javascript">


                  function change(which) {
                          document.getElementById(\'udm_core\').style.display = \'none\';
                          document.getElementById(\'udm_standard\').style.display = \'none\'; 
                          document.getElementById(\'udm_custom\').style.display = \'none\'; 
                          document.getElementById(which).style.display = \'block\';
                  }

                  function changef(which) {
                        var setting = document.getElementById(which).style.display;
                        if ( setting == \'block\' ) {
                            document.getElementById(which).style.display = \'none\';
                            document.getElementById( "arrow_" + which ).src = \'images/arrow-right.gif\';
                        } else {
                              document.getElementById(which).style.display = \'block\';
                document.getElementById( "arrow_" + which ).src = \'images/arrow-down.gif\';
                        }
                  }


                                    </script>
    <ul id="topnav" style="padding: 0px; margin: 0;"><li class="tab1"><a href="#" id="a0" onclick="change(\'udm_core\');" >Settings</a></li>
    <li class="tab2"><a href="#" id="a1" onclick="change(\'udm_standard\');" >Standard Fields</a></li>
    <li class="tab3"><a href="#" id="a2" onclick="change(\'udm_custom\');" >Custom Fields</a></li>
    <li class="tab4"><a href="#" id="a3" onclick="change(\'udm_plugins\');" >Plugins</a></li>
</ul></td></tr>';

    $form->addElement( 'html', $tabs );

    /* Fetch module information.
        this should be moved to some generic AMP class or somesuch.
    */

    $modlist_rs = $udm->dbcon->Execute( "SELECT id, name FROM moduletext ORDER BY name ASC" )
        or die( $udm->dbcon->ErrorMsg() );

    $modules[ '' ] = '--';
    while ( $row = $modlist_rs->FetchRow() ) {
        $modules[ $row['id'] ] = $row['name'];
    }

    /* Get possible sources. Again, should be moved out of here */

    $source_rs = $udm->dbcon->Execute( "SELECT id, title FROM source ORDER BY title ASC" )
        or die( $udm->dbcon->ErrorMsg() );

    while ( $row = $source_rs->FetchRow() ) {
        $sources[ $row[ 'id' ] ] = $row[ 'title' ];
    }

    /* Yet another thing to move outta here */

    $enteredby_rs = $udm->dbcon->Execute( "SELECT id, name FROM users ORDER BY name ASC" );

    while ( $row = $enteredby_rs->FetchRow() ) {
        $users[ $row['id'] ] = $row['name'];
    }

    /* Another one. */
    $MM_listtable = ( $GLOBALS['MM_listtable'] ) ? $GLOBALS['MM_listtable'] : 'lists';
    $lists_rs = $udm->dbcon->Execute( "SELECT id, name FROM $MM_listtable ORDER BY name ASC" ) or die( "Couldn't obtain list information: " . $udm->dbcon->ErrorMsg() );

    $lists[ '' ] = 'none';
    while ( $row = $lists_rs->FetchRow() ) {
        $lists[ $row['id'] ] = $row['name'];
    }

    $fields =& $udm->fields;
    $fields['core_name']          = array( 'label' => 'Name',             'type' => 'text' );
    $fields['core_redirect']      = array( 'label' => 'Redirect URL',         'type' => 'text' );
    $fields['core_publish']       = array( 'label' => 'Publish Data',         'type' => 'checkbox' );
    $fields['core_modidinput']    = array( 'label' => 'Module ID (Input)',    'type' => 'select', 'values' => $modules );
    $fields['core_modidresponse'] = array( 'label' => 'Module ID (Response)', 'type' => 'select', 'values' => $modules );
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
    $fields['core_field_order']   = array( 'label' => 'Field Order',       'type' => 'textarea' );

    $md =& $udm->_module_def;

    $coreFields = array( 'redirect', 'publish', 'modidinput', 'modidresponse', 'sourceid', 'enteredby', 'uselists', 'list1', 'list2', 'list3', 'list4', 'useemail', 'mailto', 'subject', 'field_order', 'name' );
    foreach ( $coreFields as $cf ) {
        $md[ 'core_' . $cf ] = $md[ $cf ];
    }

    $fSep = array( "</td>\n<td>", // down arrow -> field name
           "</td>\n<td>", // field name -> enabled
                   "</td>\n<td>", // enabled -> public
                   "</td>\n<td>", // public -> required
                   "</td></tr><tr><td colspan=\"5\">\n", // required -> hidden stuff, type
                   "</td>\n<td colspan=\"2\">", // type -> label
                   "</td></tr>\n<tr><td>", // label -> region
                   "</td>\n<td>", // region -> default values
                   "</td>\n<td>", // default values -> field size
                   "</td></tr></table></div>\n\n<tr><td width=\"12\">", // field size -> end of row
                 );

    $form->addGroup( array(), 'core', null, '&nbsp;', false );
    $form->addGroup( array(), 'standard', null, $fSep, false );
    $form->addGroup( array(), 'custom', null, $fSep, false );
    $form->addGroup( array(), 'plugins', null, '&nbsp;', false );

    $renderer =& $form->defaultRenderer();
    $renderer->setGroupTemplate( "\n</td></tr></table><div id=\"udm_core\"><table width=\"100%\" class=\"name\">{content}</table></div><table width=\"100%\" class=\"name\"><tr><td>\n", 'core' );

    $renderer->setGroupTemplate( "\n</td></tr></table>\n\n<br clear=\"all\">\n<div id=\"udm_standard\" style=\"display: none\"><!-- begin standard content --><table width=\"100%\" class=\"name\"><tr><td>{content}</td></tr></table></div></td></tr></table></div>\n<table width=\"100%\" class=\"name\"><tr><td width=\"12\">", 'standard' );

    $renderer->setGroupTemplate( "\n</td></tr></table>\n\n<br clear=\"all\">\n<div id=\"udm_custom\" style=\"display: none\"><table width=\"100%\" class=\"name\"><tr><td width=\"12\">{content}</td></tr></table></div></td></tr></table></div>\n<table width=\"100%\" class=\"name\"><tr><td>", 'custom' );

    $renderer->setGroupTemplate( "\n</td></tr></table>\n\n<br clear=\"all\">\n<div id=\"udm_plugins\" style=\"display: none\"><table width=\"100%\" class=\"name\"><tr><td>{content}</td></tr></table></div></td></tr></table></div>\n<table width=\"100%\" class=\"name\"><tr><td>", 'plugins' );
    $renderer->setGroupElementTemplate( "<tr><td>{label}</td><td>{element}</td></tr>\n", 'core' );
    $renderer->setGroupElementTemplate( "<tr><td>{label}</td><td>{element}</td></tr>\n", 'plugins' );
    $renderer->setGroupElementTemplate( "{label}&nbsp;{element}\n", 'standard' );
    $renderer->setGroupElementTemplate( "{label}&nbsp;{element}\n", 'custom' );

    foreach ( $udm->fields as $field => $field_def ) {
        udm_QuickForm_build_admin_addElement( &$form, $field, $field_def, $admin );
    }

    $form->setDefaults( $md );

    $form->setConstants( array( 'modin' => $udm->instance ) );

    if ( !isset( $options[ 'no_validate' ] ) ) {
        if ( !$form->validate() ) {
            $udm->form = $form;
            return false;
        }
    }

    $udm->form = $form;

    return $form;
    
}

function udm_QuickForm_build_admin_addElement( $form, $name, $field_def, $admin = false ) {

    if ( $name == '' ) return;

    $type     = $field_def[ 'type'   ];
    $label    = $field_def[ 'label'  ];
    $defaults = $field_def[ 'values' ];
    foreach ( array_values( $form->getRegisteredTypes() ) as $ftype ) {
        $types[ $ftype ] = $ftype;
    }

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
    $elements[] = &HTML_QuickForm::createElement( 'link', 'arrow', null, '#', '<img src="images/arrow-right.gif" border="0" id="arrow_' . $name . '" />', $jscript );
        $elements[] = &HTML_QuickForm::createElement( 'static', 'title', null, ( $label ) ? $label : 'Unnamed Field' );
        $elements[] = &HTML_QuickForm::createElement( 'checkbox', 'enabled_' . $name, null, 'enabled' );
        $elements[] = &HTML_QuickForm::createElement( 'checkbox', 'public_' . $name, null, 'public' );
        $elements[] = &HTML_QuickForm::createElement( 'checkbox', 'required_' . $name, null, 'required' );
        $elements[] = &HTML_QuickForm::createElement( 'select', 'type_' . $name, "<div id=\"$name\" style=\"display: none;\"><table class=\"name\" width=\"100%\" bgcolor=\"#FFFFCC\"><tr><td>" . 'Type<br/>', $types );
        $elements[] = &HTML_QuickForm::createElement( 'text', 'label_' . $name, 'Label<br/>' );
        $elements[] = &HTML_QuickForm::createElement( 'select', 'region_' . $name, 'Region<br/>', $regions);
        $elements[] = &HTML_QuickForm::createElement( 'textarea', 'values_' . $name, 'Default Values<br/>' );
        $fieldSize = &HTML_QuickForm::createElement( 'text', 'size_' . $name, 'Field Size<br/>' );
        $fieldSize->setSize( '3' );
        $elements[] = $fieldSize;
    } else {
        $elements[] = &HTML_QuickForm::createElement( $type, $name, $label, $defaults );
    }

    return 1;

}

?>
