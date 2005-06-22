<?php

require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Text_Output extends UserDataPlugin {

    var $options = array (
        'skip_prefix' => array(
            'type'=>'text',
            'available'=> true,
            'label' => 'Skip Fields Prefixed' )
        );

    function UserDataPlugin_Text_Output ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

    function execute ( $options = null ) {

        $this->_field_prefix=null;
        
        // Ensure we have a form built before proceeding.
        if ( !isset( $this->udm->form ) )
            $this->udm->doPlugin( 'QuickForm', 'Build' );

        return $this->toText( $this->getData() );
    }

    function setSkipPrefix() {
        $options = $this->getOptions();
        if (!isset($options['skip_prefix'])) return false;

        return split("[ ]?,[ ]?", $options['skip_prefix']); 
    }

    function skipPlugins( $data ) {
        if (!($skip_prefixes = $this->setSkipPrefix())) return $data;

        foreach ($data as $key => $value) {
            foreach ($skip_prefixes as $badprefix) {
                if (strpos($badprefix, $key)===0) continue 2;
            }
            $return_data[$key] = $value;
        }

        return $return_data;
    }

    function toText($data) {
        $data = $this->skipPlugins( $data );

        $order = $this->udm->getFieldOrder();
        
        if (count($order)>1) { 
            foreach ($order as $field) {
                $output .= $this->elementToText ($field, $data[$field]);
                $finishedElements[ $field ] = 1;
            }
        }
                
        foreach ($data as $field => $value ) {
            if (isset($finishedElements[$field])) continue;

            $output .= $this->elementToText ($field, $value);

        }

        return $output;
    }

    function elementToText($field, $value) {
        $fDef = $this->udm->fields[$field];

        //if the field is not explicitly enabled, return no data
        //no non-public fields are sent via insecure e-mail
        if (!($fDef['enabled'] && $fDef['public'])) return ''; 

        //if neither the label nor the value
        if (!($fDef['label'].$value)) return '';

        $label = (isset($fDef) ? strip_tags($fDef['label']) : $field);
        if ($label) $label .= ": ";

        switch ($fDef['type']) {
            case 'html':
                return '';
                break;
            case 'static':
            case 'header':
                if ($fDef['values']) $label = $fDef['values'];
                return "\n:: " . strip_tags( $label ) . "\n";
                break;
            case 'checkbox':
                $value = $value?'yes':'no';
                break;
            case 'select':
                $value_set = $this->getValueSet($fDef);
                if ( $fDef['region'] ) {
                    $value = $value_set[$value];
                }
                if (is_array($value_set)) {
                    if (isset($value_set[$value])) {
                        $value = $value_set[$value];
                    }
                }
                break;
            case 'wysiwyg':
                //replace <BR> with \n
                #$value =  preg_replace("/(\r\n|\n|\r)/", "", $value);
                $value =  preg_replace("=<br */?>=i", "\n", $value);
                $value = strip_tags($value)."\n";
                $label.= "\n";
                break;
            case 'textarea':
                $label.= "\n";
                $value.= "\n";
                break;
        }
        return ( $label. $value . "\n" );
    }

        

}


?>
