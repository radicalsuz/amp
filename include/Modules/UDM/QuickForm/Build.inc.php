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
require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Build_QuickForm extends UserDataPlugin {

    function UserDataPlugin_Build_QuickForm ( &$udm ) {
        $this->init( $udm );
    }

    function execute ( $options = null ) {

        return udm_QuickForm_build( $this->udm, $this->options );

    }

}

$GLOBALS['regionObj'] = new Region();

function udm_QuickForm_build ( &$udm, $options = null ) {

	if ( $udm->admin ){
		$admin = true;
	} else { 
		$admin=false;
	}
    $frmName    = $udm->name;
    $frmMethod  = ( isset( $options['frmMethod'] ) ) ?
                    $options['frmMethod'] : 'post';
    $frmAction  = ( isset( $options['frmAction'] ) ) ?
                    $options['frmAction'] : null;

    $form = new HTML_QuickForm( $frmName, $frmMethod, $frmAction );
	//include publish checkbox on admin form
    if ($admin) { 
        $pub_val = ( isset($udm->fields['publish']) && isset($udm->fields['publish']['value']) ) ? $udm->fields['publish']['value'] : null;
		$publish_field = array('type'=>'checkbox', 'label'=>'PUBLISH', 'required'=>false, 'public'=>false,  'values'=>0, 'size'=>null, 'value' => $pub_val);
		$udm->fields['publish']=$publish_field;
	}

	
	
	if ( isset( $udm->_module_def[ 'field_order' ] ) ) {
    
        $fieldOrder = split( ',', $udm->_module_def[ 'field_order']  );
        
        foreach ( $fieldOrder as $field ) {
            $field = trim( $field );
            udm_quickform_addElement( $form, $field, $udm->fields[ $field ], $admin );
            $finishedElements[ $field ] = 1;
        }
    }
    
    foreach ( $udm->fields as $field => $field_def ) {
        // Skip fields that have already been added.
        if ( isset( $finishedElements[ $field ] ) ) continue;
        udm_quickform_addElement( $form, $field, $field_def, $admin );
    
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

    if ( $udm->submitted ) {
        if ( !$form->validate() ) {
            $udm->form =& $form;
            return false;
        }
    }

    $udm->form =& $form;

    return $form;
    
}

function udm_quickform_setupLookup( $tablename, $displayfield, $valuefield, $restrictions=null) {
	global $dbcon;
	$lookup_sql="Select $valuefield, $displayfield from $tablename";
	if (isset($restrictions)&&$restrictions) {
		$lookup_sql.=" WHERE $restrictions";
	}
	$lookup_sql.=" ORDER BY $displayfield";
	return $dbcon->GetAssoc($lookup_sql);
}

function udm_quickform_addElement( &$form, $name, &$field_def, $admin = false ) {

    if ( !$admin ) {
       if (!isset($field_def['public']) || $field_def['public'] != 1) return false;
    } else {
       if (!isset($field_def['enabled']) || $field_def['enabled'] != 1) return false;
    }

    $type     = (isset($field_def['type'])) ? $field_def['type'] : null;
    $label    = (isset($field_def['label'])) ? $field_def[ 'label'  ] : null;
    $defaults = (isset($field_def['values'])) ? $field_def[ 'values' ] : null;
    $size     = (isset($field_def['size'])) ? $field_def[ 'size' ] : null;

    $renderer =& $form->defaultRenderer();

	//Check for defined Lookup in selectbox defaults
	//format is Lookup(table_name, display_column, value_column, restrictions);
	if ($type=='select' && is_string( $defaults ) && ( substr($defaults,0,7) == "Lookup(" ) ) {

		$just_values = str_replace(")", "", substr($defaults, 7));
		$valueset = split(", ", $just_values );
		$defaults = udm_quickform_setupLookup($valueset[0], $valueset[1], $valueset[2], $valueset[3]);
	
	} elseif ( is_array( $defaults ) ) {

		$defArray = $defaults;

	} else {
		
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

	//static items now span both columns
    if ($type=='static') {
        $renderer->setElementTemplate("\n\t<tr>\n\t\t<td align=\"left\" valign=\"top\" colspan=\"2\"><table class=\"form_span_col\"><tr><td>\t{element}</td></tr></table></td>\n\t</tr>", $name);
    }




    if ( isset( $field_def[ 'required' ] ) && $field_def[ 'required' ] && !$admin )
        $form->addRule( $name, $label . ' is required.', 'required' );
    
//    if ( isset( $field_def[ 'size' ] ) && $field_def['size'] )
//        $form->addRule( $name, $label . ' must be less than ' . $field_def[ 'size' ] + 1 . ' characters long.', 'maxlength', $field_def[ 'size' ] );
        
    if ( $name == 'Email' && !$admin )
        $form->addRule( $name, 'Must be a valid email address.', 'emailorblank' );
        
    return 1;

}

?>