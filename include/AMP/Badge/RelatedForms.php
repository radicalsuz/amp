<?php

function amp_badge_related_form( $data ) {
    $data = array_merge( $data, $_GET );
    foreach( $data as $key => $value ) {
        if ( is_array( $value ) && count( $value ) == 1 ) {
            $quick_val = array_values( $value );
            $data[ $key ] = $quick_val[0];
        }
    }

    $modin = ( isset( $data['modin'] ) && $data['modin']) ? $data['modin'] : false;
    $related_index = ( isset( $data['related_index'] ) && $data['related_index']) ? $data['related_index'] : false;
    
    if ( !$modin ) return false;
    if ( !AMP_authenticate( 'admin')) {
        $live_forms = AMP_lookup( 'formsPublic');
        if ( !isset( $live_forms[ $modin ])) {
            return 'Please publish the related form';
        }


    }

    require_once( 'AMP/UserData.php');
    $udm = &new UserData( AMP_Registry::getDbcon( ), $modin );
    $udm->registerPlugin( 'Output', 'Text');
    $udm->setData( $data );

    $renderer = AMP_get_renderer( );
#    $delete_button = $renderer->form( 
    $delete_button = $renderer->link( '#', 
                      $renderer->image( AMP_SYSTEM_ICON_DELETE, 
                        array( 'class' => 'icon', 'style' => 'border: 0;' )),
                      array( 'alt' => AMP_TEXT_DELETE_ITEM, 
                             'title' => AMP_TEXT_DELETE_ITEM, 
                             'onClick' => "$('form_related_item_$related_index').remove( ); $('form_{$modin}_related_custom_fields_$related_index').remove(); return false;"
                           )
                     );
    $content = $delete_button . $renderer->tag( 'pre', $udm->doPlugin( 'Output', 'Text'));
    $result =  $renderer->div($content, array( 'class' => 'form_related_item', 'id' => 'form_related_item_' . $related_index ));
    return $result;
}

function amp_badge_related_form_render_delete( $source ) {
    $renderer = AMP_get_renderer( );
    return
            $renderer->input( 'action', 'delete', array( 'type' => 'hidden' ))
            . $renderer->input( 'id', $source->id, array( 'type' => 'hidden' ));
}

?>
