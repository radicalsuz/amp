<?php

function amp_badge_related_form( $data ) {
    $data = array_merge( $data, $_GET );
    $modin = ( isset( $data['modin'] ) && $data['modin']) ? $data['modin'] : false;
    $related_index = ( isset( $data['related_index'] ) && $data['related_index']) ? $data['related_index'] : false;
    
    if ( !$modin ) return false;
    if ( !AMP_authenticate( 'admin')) {
        $live_forms = AMP_lookup( 'formsPublic');
        if ( !isset( $live_forms[ $modin ])) {
            ob_start( );
            AMP_varDump( $live_forms );
            $include_value = ob_get_contents();
            ob_end_clean();
            return $include_value;
        }


    }

    require_once( 'AMP/UserData.php');
    $udm = &new UserData( AMP_Registry::getDbcon( ), $modin );
    $udm->registerPlugin( 'Output', 'Text');
    $udm->setData( $data );

    $renderer = AMP_get_renderer( );
    $delete_button = $renderer->form( 
            $renderer->input( 'delete_' . $related_index, AMP_TEXT_DELETE_ITEM, 
                                array(  'type' => 'image', 
                                        'src' => AMP_SYSTEM_ICON_DELETE, 
                                        'class' => 'icon', 
                                        'alt' => AMP_TEXT_DELETE_ITEM, 
                                        'title' => AMP_TEXT_DELETE_ITEM, 
                                        //'onClick' => 'related_delete( document.forms["'.$udm->name.'"], Array( "'.join( '", "', $affected_fields ).'"), '.$related_index.' );' 
                                        'onClick' => '$( "form_related_item_'.$related_index.'").remove( );'
                                        ))
            );
    $result =  $renderer->div( 
                    $renderer->tag( 'pre', 
                        $delete_button 
                        . $udm->doPlugin( 'Output', 'Text'))
                , array( 'class' => 'form_related_item', 'id' => 'form_related_item_' . $related_index ));
    return $result;
}

function amp_badge_related_form_render_delete( $source ) {
    $renderer = AMP_get_renderer( );
    return
            $renderer->input( 'action', 'delete', array( 'type' => 'hidden' ))
            . $renderer->input( 'id', $source->id, array( 'type' => 'hidden' ));
}

?>
