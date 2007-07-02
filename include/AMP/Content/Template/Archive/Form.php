<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Template/Archive/ComponentMap.inc.php');

class AMP_Content_Template_Archive_Form extends AMPSystem_Form_XML {

    var $id_field   = 'archive_id';
    var $name_field = 'name';
    var $allow_copy = false;

    var $submit_button = array( 'submitAction' => array(
        'type' => 'group',
        'elements'=> array(
            'restore' => array(
                'type' => 'submit',
                'label' => 'Restore this Version'),
            'cancel' => array(
                'type' => 'submit',
                'label' => 'Cancel'),
            )
    ));

    function AMP_Content_Template_Archive_Form( ) {
        $name = 'template_archives';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_TEMPLATE_ARCHIVE );
    }
    function _after_init( ) {
        $this->addTranslation( 'name', '_show_labeled_diff_value', 'set');
        $this->addTranslation( 'css', '_show_labeled_diff_value', 'set');
        $this->addTranslation( 'imgpath', '_show_labeled_diff_value', 'set');
        $this->addTranslation( 'header2', '_create_diff', 'set');

        $this->addTranslation( 'lnav3', '_show_labeled_diff_value', 'set');
        $this->addTranslation( 'lnav4', '_show_labeled_diff_value', 'set');
        $this->addTranslation( 'lnav7', '_show_labeled_diff_value', 'set');
        $this->addTranslation( 'lnav8', '_show_labeled_diff_value', 'set');
        $this->addTranslation( 'lnav9', '_show_labeled_diff_value', 'set');

        $this->addTranslation( 'rnav3', '_show_labeled_diff_value', 'set');
        $this->addTranslation( 'rnav4', '_show_labeled_diff_value', 'set');
        $this->addTranslation( 'rnav7', '_show_labeled_diff_value', 'set');
        $this->addTranslation( 'rnav8', '_show_labeled_diff_value', 'set');
        $this->addTranslation( 'rnav9', '_show_labeled_diff_value', 'set');
    }

    function adjustFields( $fields ) {
        foreach( $fields as $field_name => $field_def ) {
            if ( $field_def['type'] == 'textarea' || $field_def['type'] == 'text') {
                $field_def['type'] = 'static';
            }
            $new_fields[ $field_name ] = $field_def;
        }
        $new_fields['template_header2']['type'] = 'hidden';
        $new_fields['archive_id']['type'] = 'hidden';

        $diff_fields = array( 'diff_header' => array( 'type' => 'header', 'default' => 'Displaying Differences with Current Version' ));
        return array_merge( $diff_fields, $new_fields );
    }

    function _create_diff( $data, $fieldname ) {
        $old_value = $this->getCurrentValue( $data['id'], $fieldname );
        return $this->render_diff_text( $data[$fieldname], $old_value );

    }

    function render_diff_text( $new, $old ) {
        require_once( 'Text/Diff.php' );
        require_once( 'Text/Diff/Renderer/inline.php' );
        $diff = new Text_Diff( 'auto', array( split( "\n", $old ), split( "\n", $new )));
        $diff_renderer = new Text_Diff_Renderer_inline( );
        $diff_value = $diff_renderer->render( $diff );

        $form_renderer = AMP_get_renderer( );
        if( !$diff_value ) $diff_value = htmlentities( $new );
        if ( !$diff_value ) return false;

        return $form_renderer->div( $form_renderer->tag( 'pre', $diff_value ), array( 'class' => 'diff' ));
    }

    function getCurrentValue( $template_id, $fieldname ) {
        require_once( 'AMP/Content/Template.inc.php');
        $template = &new AMPContent_Template( AMP_Registry::getDbcon( ), $template_id );
        return $template->getData( $fieldname );
    }

    function _show_name_diff( $data, $fieldname ) {
        return 'Name: ' . $this->_create_diff( $data, $fieldname ) ;
    }

    function _show_labeled_diff_value( $data, $fieldname ) {
        $field_def = $this->getField( $fieldname );
        return $field_def['label'] . ': ' . $this->_create_diff( $data, $fieldname );
    }

    function _init_submit( ){
        //do nothing
    }

}
?>
