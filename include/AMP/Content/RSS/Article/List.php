<?php

require_once( 'AMP/Display/System/List.php');
require_once( 'AMP/Content/RSS/Article/Article.php');

class AMP_Content_RSS_Article_List extends AMP_Display_System_List {
    var $columns = array( 'select', 'summary', 'feed', 'timestamp', 'id' );
    var $_source_object = 'RSS_Article';
    var $_pager_active = true;
    var $_actions = array( 'publish', 'delete');
    var $_action_args = array( 'publish' => array( 'section_id', 'class_id'));

    var $_sort_sql_default = 'date';
    var $_sort_sql_translations = array( 
        'date' => 'dcdate DESC, id',
        'summary' => 'title',
//        'feed'   => 'feed_id'
    );

    function AMP_Content_RSS_Article_List( $source = false, $criteria = array( ) ) {
        $this->__construct( $source, $criteria );
    }

    function render_source_link( &$source ) {
        return $this->_renderer->link( 
                    AMP_validate_url( $source->getLinkURL( )),
                    $this->_renderer->image( AMP_SYSTEM_ICON_PREVIEW, 
                                      array('alt'    => AMP_TEXT_VIEW_SOURCE, 
                                            'width'  => 16,
                                            'height' => 16,
                                            'align'  => 'left',
                                            'border' => 0)),
                    array( 'target' => '_blank') )
                . '&nbsp;'  
                . $this->_renderer->link( 
                    AMP_validate_url( $source->getLinkURL( )),
                            AMP_TEXT_SOURCE . ': '
                            . AMP_trimText( $source->getLinkURL( ), 45 ),
                    array( 'target' => '_blank') );

    }

    function render_summary( &$source ) {
        $content = utf8_decode( AMP_trimText( $source->getBody( ), 700, false ) );
        if ( AMP_CONTENT_RSS_CUSTOMFORMAT ) $content = $this->render_chevron_display( $source );

        if ( $content ) $content = $this->_renderer->newline( 2 ) . $content;
        return  $this->_renderer->inDiv(  
                    $this->_renderer->bold( utf8_decode( $source->getName( ))) 
                        . $this->_renderer->newline() 
                        . $this->render_source_link( $source )
                        . $this->render_date( $source )
                        . $content
                , array( 'style' => 'padding: 1em;'));

    }

    function render_date( $source ) {
        $item_date = $source->getItemDate( );
        $this->_renderer->span( $this->format_date( $source->getItemDate( )), array( 'class'=>'photocaption'))
        . $this->_renderer->newline() ;
    }


    function render_chevron_display( &$source ){
        $content = utf8_decode( AMP_trimText( $source->getBody( ), 700, false ) );

        if ( $subtitle = $source->getSubtitle( )){
            $content = $this->_renderer->italics( AMP_TEXT_SUBTITLE . ': ' . $subtitle) 
                        . $this->_renderer->newline( )
                        . $content;
        }
        if ( $contacts = $source->getContacts( )){
            $content .= $this->_renderer->newline( 2 )
                        . $this->_renderer->bold( AMP_TEXT_CONTACTS. ': ' . $contacts );
        }
        return $content;

    }

    function render_toolbar_publish( &$toolbar ){
        $section_options = &AMPContent_Lookup::instance( 'sectionMap' );
        $section_options = array( '' => 'Select Section') + $section_options;
        $class_options = &AMPContent_Lookup::instance( 'classes');
        $content = $this->_renderer->span( AMP_TEXT_PUBLISH_TO  . ':&nbsp;' , array( 'class' => 'searchform_label'))
                        . AMP_buildSelect( 'section_id', $section_options, null, $this->_renderer->makeAttributes( array( 'class' => 'searchform_element')))
                        . '&nbsp;'
                        . AMP_buildSelect( 'class_id', $class_options, 1, $this->_renderer->makeAttributes( array( 'class' => 'searchform_element')))
                        . '&nbsp;';
        return $toolbar->addTab( 'publish', $content );
                

    }

    function _renderFooter( ) {
        return $this->_renderPager( );
    }

    function render_feed( $source ) {
        return $source->getFeedNameText( );
    }

}
?>
