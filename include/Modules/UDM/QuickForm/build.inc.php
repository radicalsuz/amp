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

function udm_QuickForm_build ( &$udm, $options = null ) {

    if ( $udm->admin ) $admin = true;

    $frmName    = $udm->name;
    $frmMethod  = ( isset( $options['frmMethod'] ) ) ?
                    $options['frmMethod'] : 'post';
    $frmAction  = ( isset( $options['frmAction'] ) ) ?
                    $options['frmAction'] : null;

    $form = new HTML_QuickForm( $frmName, $frmMethod, $frmAction );

    if ( isset( $udm->_module_def[ 'field_order' ] ) ) {
    
        $fieldOrder = split( ',', $udm->_module_def[ 'field_order']  );
        
        foreach ( $fieldOrder as $field ) {
            $field = trim( $field );
            udm_quickform_addElement( &$form, $field, $udm->fields[ $field ], $admin );
            $finishedElements[ $field ] = 1;
        }
    }
    
    foreach ( $udm->fields as $field => $field_def ) {
    
        // Skip fields that have already been added.
        if ( isset( $finishedElements[ $field ] ) ) continue;
        udm_quickform_addElement( &$form, $field, $field_def, $admin );
    
    }
    
    $form->setDefaults( $udm->getStoredValues() );

    $form->addElement( 'submit', 'btnUdmSubmit', 'Submit' );
    $form->addElement( 'hidden', 'modin', 'Module Instance' );
    $consts['modin'] = $udm->instance;

    if ( $udm->authorized ) {
        $form->addElement( 'hidden', 'uid', 'User ID' );
        $form->addElement( 'hidden', 'otp', 'Passphrase' );
        $consts['uid'] = $udm->uid;
        $consts['otp'] = $udm->pass;
    }   

    $form->setConstants( $consts );

    if ( !isset( $options[ 'no_validate' ] ) ) {
        if ( !$form->validate() ) {
            $udm->form = $form;
            return false;
        }
    }

    $udm->form = $form;

    return $form;
    
}

function udm_quickform_addElement( $form, $name, &$field_def, $admin = false ) {

    if ( $field_def[ 'public' ] != 1 && !$admin ) return false;

    $type     = $field_def[ 'type'   ];
    $label    = $field_def[ 'label'  ];
    $defaults = $field_def[ 'values' ];
    $size     = $field_def[ 'size' ];
    $renderer =& $form->defaultRenderer();

    // Check to see if we have an array of values.
    $defArray = explode( ",", $defaults );
    if (count( $defArray ) > 1) {
        $defaults = array();
        foreach ( $defArray as $option ) {
            $defaults[ $option ] = $option;
        }
    } else {
        $defaults = $defArray[0];
    }

    // Get region information if it's needed.
    if ( isset( $field_def[ 'region' ] )
         && strlen( $field_def[ 'region' ] ) > 1
         && $type == 'select' ) {

        $defaults = $GLOBALS['regionObj']->getSubRegions( $field_def[ 'region' ] );
        $selected = $field_def[ 'values' ];
    }

    // Add a default blank value to the select array.
    if ( $type  == 'select' && is_array( $defaults ) ) $defaults = array('' => 'Select one') + $defaults;
    
    $form->addElement( $type, $name, $label, $defaults );

    if ( isset( $selected ) ) {
        $fRef =& $form->getElement( $name );
        $fRef->setSelected( $selected );
    }

    if ( isset( $size ) && $size && ( $type == 'text' ) ) {
        if ($size > 40) $size = 40;
        $fRef =& $form->getElement( $name );
        $fRef->setSize( $size );
    }

    if ( isset( $size ) && $size && ( $type == 'textarea' ) ) {
        if ( strpos( $size, ':' ) ) {
            $tmpArray = split( ':', $size );
            $rows = $tmpArray['0'];
            $cols = $tmpArray['1'];
        } else {
            $cols = $size;
        }

        $fRef =& $form->getElement( $name );
        if ( isset( $rows ) ) $fRef->setRows( $rows );
        if ( isset( $cols ) ) $fRef->setCols( $cols );
    }

    if ( $type == 'checkbox' ) {
        $fRef =& $form->getElement( $name );
        $fRef->setText( null );
    }
    /*

        print "I'm trying here: <pre>";

        $fRef =& $form->getElement( $name );
        print_r( $fRef );
        print "</pre>";
        $fRef->setText( false );
        if ( $defaults == "1"  ) {
            $fRef->setValue( 1 );
            $fRef->setChecked( 1 );
        } else {
            $fRef->setValue( 1 );
            $fRef->setChecked( 1 );
        }

    }
*/
    
    //OUTPUT TEMPLATE MODIFICATIONS
    //Default output template (with classes defined)
    $renderer->setElementTemplate("\n\t<tr>\n\t\t<td align=\"right\" valign=\"top\" class=\"form_label_col\"><!-- BEGIN required --><span style=\"color: #ff0000\">*</span><!-- END required --><b>{label}</b></td>\n\t\t<td valign=\"top\" align=\"left\" class=\"form_data_col\"><!-- BEGIN error --><span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->\t{element}</td>\n\t</tr>");

    if ($type=='checkbox') {
        $renderer->setElementTemplate("\n\t<tr>\n\t\t<td align=\"right\" valign=\"top\" class=\"form_label_col\"><!-- BEGIN required --><span style=\"color: #ff0000\">*</span><!-- END required -->{element}</td>\n\t\t<td valign=\"top\" align=\"left\" class=\"form_data_col\"><!-- BEGIN error --><span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->\t<b>{label}</b></td>\n\t</tr>", $name);
    }

    //textareas have a table they sit within for CSS-controlled positioning
    if ($type=='textarea') {
        $renderer->setElementTemplate("\n\t<tr>\n\t\t<td align=\"left\" valign=\"top\" colspan=\"2\"><table class=\"form_span_col\"><tr><td><!-- BEGIN required --><span style=\"color: #ff0000\">*</span><!-- END required --><b>{label}</b><br>\n\t\t<!-- BEGIN error --><span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->\t{element}</td></tr></table></td>\n\t</tr>", $name);
    }

    if ( isset( $field_def[ 'required' ] ) && $field_def[ 'required' ] )
        $form->addRule( $name, $label . ' is required.', 'required' );
    
//    if ( isset( $field_def[ 'size' ] ) && $field_def['size'] )
//        $form->addRule( $name, $label . ' must be less than ' . $field_def[ 'size' ] + 1 . ' characters long.', 'maxlength', $field_def[ 'size' ] );
        
    if ( $name == 'Email' )
        $form->addRule( $name, 'Must be a valid email address.', 'emailorblank' );
        
    return 1;

}

?>
