<?php

require_once( 'AMP/System/Observer.php');

class AMP_System_Permission_Observer_Section extends AMP_System_Observer {

    var $_saved_section;

    function AMP_System_Permission_Observer_Section( ) {

    }

    function onInitForm( &$controller ) {
        $model = $controller->get_model( );
        if ( !isset( $model->id )) {
            $model_id = $controller->get_model_id( );
            if ( !$model_id ) return false;
            $model->readData( $model_id );
        }
        $section = $model->getSection( );
        if ( !$section ) return false;
        $allowed_sections = AMP_lookup( 'sectionMap');
        if ( !isset( $allowed_sections[ $section ]) && $section != AMP_CONTENT_MAP_ROOT_SECTION) {
            $this->_saved_section = $section;
            $form = $controller->get_form( );
            $form->dropField( 'section') ;
            $form->dropField( 'section_id') ;
            $form->dropField( 'type') ;
        }
    }

    function onBeforeSave( &$controller ) {
        if ( !isset( $this->_saved_section )) return;
        $model = $controller->get_model( );
        $model->mergeData( array( 'section' => $this->_saved_section, 'section_id' => $this->_saved_section ));

    }
}


?>
