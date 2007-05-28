<?php
require_once( 'AMP/Display/List.php');
require_once( 'Modules/Housing/Post.php');

class Housing_Public_List extends AMP_Display_List {
    var $name = 'HousingPosts';
    var $_source_object = 'Housing_Post';
    var $_suppress_messages = true;

    var $_pager_active = true;
    var $_pager_limit = 100;
    var $_class_pager = 'AMP_Display_Pager_Content';
    var $_path_pager = 'AMP/Display/Pager/Content.php';

    var $_sort_sql_default = 'default';
    var $_sort_sql_translations = array( 
        'default' => 'custom1,State,City,created_timestamp',
    );

    var $_subheader_methods = array( 'getLegacyType' );

    var $_display_headings = array( 
        'name' => 'Contact',
        ''
    );

    var $_source_criteria = array( 'live' => 1 );

    function Housing_Public_List( $source = false, $criteria = array( ) ) {
        $source = false;
        $this->__construct( $source, $criteria );
    }

    function _renderHeader( ) {
        return $this->_renderer->p( 
            $this->_renderer->link( '#have', 'View Available Housing' )
            . $this->_renderer->separator( )
            . $this->_renderer->link( '#need', 'View Requested Housing')
            . $this->_renderer->newline( )
            . $this->_renderer->link( AMP_CONTENT_URL_BOARD_HOUSING_ADD, 'Offer/Request Housing on the Housing Board'),
            array( 'class' => 'text' ));
    }

    function _renderItem( &$source ) {
        $item_output =  ( $source->isType( AMP_TEXT_OFFER ) )   
                         ? $this->render_item_offer( $source )
                         : $this->render_item_request( $source );
        return $this->_renderSubheader( $source ) . $item_output ;
    }

    function render_subheader_format( $item_header, $depth=0 ) {
        $anchor_name = strtolower( substr( $item_header, 0, 4 )) ;
        return $this->_renderer->p( 
                    $this->_renderer->anchor_named( $anchor_name )
                    . $item_header,
                    array( 'class' => 'title' )
                );
    }

    function render_item_offer( &$source ) {
        $output = '';
        $table_content = '';
        $output .= $this->_renderer->newline( );
        $contact_info = $this->render_contact( $source ) ;
        $table_content .= $this->_renderer->tr( $this->_renderer->td( $contact_info, array( 'colspan' => 5, 'class' => 'text') ));

        $availability = $this->render_availability( $source );
        $table_content .= $this->_renderer->tr( $this->_renderer->td( $availability, array( 'colspan' => 5, 'class' => 'text') ));

        $headers  = array( AMP_TEXT_LOCATION, AMP_TEXT_TRANSIT, AMP_TEXT_PARKING, AMP_TEXT_MEALS, AMP_TEXT_ACCESSIBILITY );
        $row_headers_1 = $this->render_headers( $headers );
        $content = array( $source->getBaseLocation( ), $source->getTransit( ), $source->getParking( ), $source->getMeals( ), $source->getAccessibility( ));
        $row_content_1 = $this->render_row_content( $content );

        $headers  = array( AMP_TEXT_BEDS, AMP_TEXT_FLOOR, AMP_TEXT_TENT, AMP_TEXT_SMOKING, AMP_TEXT_CHILDREN );
        $row_headers_2 = $this->render_headers( $headers );
        $content = array( $source->getBeds( ), $source->getFloor( ), $source->getTent( ), $source->getSmoking( ), $source->getChildren( ));
        $row_content_2 = $this->render_row_content( $content );

        $table_content .=
                  $row_headers_1 . $row_content_1
                . $row_headers_2 . $row_content_2;

        $table_content .= $this->render_comments( $source );


        $output .= $this->_renderer->table( $table_content, array( 'width' => '100%', 'border' => 0, 'cellpadding' => 2, 'bordercolor' => '#000', 'class' => 'boardbg'));

        return $output;
        

    }

    function render_comments( $source ) {
        if ( !( $item = $source->getComments( ))) return false;
        $comments_header = $this->_renderer->td( $this->_renderer->strong( ucwords( AMP_TEXT_OTHER_COMMENTS )), array( 'class' => 'board', 'colspan' => 5 ));

        $comments_data = $this->_renderer->td( converttext( $item ), array( 'class' => 'board', 'colspan' => 5 ));
        return 
              $this->_renderer->tr( $comments_header, array( 'class' => 'board'))
            . $this->_renderer->tr( $comments_data );
    }

    function render_row_content( $content ) {
        $output = '';
        foreach( $content as $item_content ) {
            $output .= $this->_renderer->td(  $item_content , array( 'class' => 'text', 'valign' => 'top' ));
        }

        return $this->_renderer->tr( $output );
    }

    function render_headers( $headers ) {
        $output = '';

        foreach( $headers as $item_header ) {
            $output .= $this->_renderer->td( $this->_renderer->strong( ucwords( $item_header ) ), array( 'class' => 'board' ));
        }

        return $this->_renderer->tr( $output, array( 'class' => 'board'));

    }

    function render_availability( $source) {
        $availability_header = $this->_renderer->strong( ucwords( AMP_TEXT_AVAILABLE ) .':'. $this->_renderer->space( ));
        return $availability_header . $source->getAvailability( );
    }

    function render_contact( $source ) {
        $contact_header = $this->_renderer->strong( ucwords( AMP_TEXT_CONTACT ) .':'. $this->_renderer->space( ));
        $all_items = array( );
        if ( $item = $source->getName( ) ) {
            $all_items[] = $item;
        }
        if ( $item = $source->getData( 'Company' )) {
            $all_items[] = $item;
        }
        if ( $item = $source->getData( 'Email' )) {
            $all_items[] = AMP_protect_email( $item );
        }
        if ( $item = $source->getData( 'Phone' )) {
            $all_items[] = $item;
        }
        return $contact_header . join( $this->_renderer->space( 2 ), $all_items );

    }

    function render_item_request( &$source ) {
        $output = '';
        $table_content = '';
        $table_content .= $this->_renderer->tr( $this->_renderer->td( $this->render_contact( $source ), array( 'class' => 'text')));
        $table_content .= $this->_renderer->tr( $this->_renderer->td( $this->render_dates_requested( $source ), array( 'class' => 'text')));
        $table_content .= $this->_renderer->tr( $this->_renderer->td( $this->render_qty_requested( $source ), array( 'class' => 'text')));

        $output .= $this->_renderer->table( $table_content, array( 'width' => '100%', 'border' => 0, 'cellpadding' => 2, 'bordercolor' => '#000', 'class' => 'boardbg'));
        return $output;
    }

    function render_dates_requested( $source ) {
        $dates_header = $this->_renderer->strong( AMP_TEXT_DATES_NEEDED . ':') . $this->_renderer->space( );
        return $dates_header . $source->getDatesRequested( );
        
    }

    function render_qty_requested( $source ) {
        $qty_header = $this->_renderer->strong( AMP_TEXT_NUMBER_OF_PEOPLE. ':') . $this->_renderer->space( );
        return $qty_header . $source->getQtyRequested( );

    }


}

?>
