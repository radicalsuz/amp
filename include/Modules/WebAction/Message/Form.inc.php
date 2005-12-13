<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/WebAction/Message/ComponentMap.inc.php');

class WebActionMessage_Form extends AMPSystem_Form_XML {

    var $_field_type_targets = array( 
        'all'   => 'static',
        'choose_single' => 'radiogroup',
        'choose_multiple' => 'checkgroup');

    var $_required_display_stuff = array( 
        'enabled'   =>  true,
        'public'    =>  true);

    function WebActionMessage_Form( ){
        $name = "WebActionMessage";
        $this->init( $name );
    }

    function getTargetFieldType( $method ){
        if ( !isset( $this->_field_type_targets[ $method ])) return 'static';
        return $this->_field_type_targets[ $method ];
    }

    function setTargets( $target_list, $target_method = 'all' ){
        $target_field['type'] = $this->getTargetFieldType( $target_method );
        if ( $target_field['type']== 'static' ) $target_field['default'] = $this->_HTML_targets( $target_list );
        $target_field += $this->_required_display_stuff;
        $this->addField( $target_field, 'targets');
    }

    function _HTML_targets( $list ){
        if ( !is_array( $list )) $list = split( ',', $list );
        require_once( 'Modules/WebAction/Lookups.inc.php');
        $targetNames = WebAction_Lookup::instance( 'targets');
        $output = "";
        foreach( $list as $target_id ){
            if ( !isset( $targetNames[ $target_id ])) continue;
            $output .= $targetNames[ $target_id ] . '<BR>';
        }
        return $output;
    }
}
?>
