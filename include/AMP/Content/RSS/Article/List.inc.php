<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/RSS/Article/Article.php');

class RSS_Article_List extends AMP_System_List_Form {
    var $name = "RSS_Article";
    var $col_headers = array( 
        'Content' => '_contentBox',
        AMP_TEXT_SOURCE => 'FeedNameText',
        'Received' => 'timestamp',
        'ID'    => 'id');
    var $editlink = 'rss_content.php';
    var $name_field = 'title';
    var $_source_object = 'RSS_Article';
    var $suppress = array( 'header' => true, 'editcolumn' => true, 'addlink' => true );
    var $_pager_active = true;
    var $_observers_source = array( 'AMP_System_List_Observer' );
    var $_url_add = 'rss_content.php?action=update';
    var $_actions = array( 'publish', 'delete');
    var $_action_args = array( 'publish' => array( 'section_id', 'class_id'));

    function RSS_Article_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }

    function _after_init ( ){
        $this->addTranslation( 'timestamp', '_makePrettyDateTime' );
    }


    function _sourceLink( &$source ){
        $renderer = &$this->_getRenderer( );
        return $renderer->link( 
                    $source->getLinkURL( ),
                    $renderer->image( AMP_SYSTEM_ICON_PREVIEW, 
                                      array('alt'    => AMP_TEXT_VIEW_SOURCE, 
                                            'width'  => 16,
                                            'height' => 16,
                                            'align'  => 'left',
                                            'border' => 0)),
                    array( 'target' => '_blank') )
                . '&nbsp;'  
                . $renderer->link( 
                    $source->getLinkURL( ),
                            AMP_TEXT_SOURCE . ': '
                            . AMP_trimText( $source->getLinkURL( ), 45 ),
                    array( 'target' => '_blank') );
    }

    function _contentBox( &$source, $column_name ){
        $renderer = &$this->_getRenderer( );
        $content = AMP_trimText( $source->getBody( ), 700, false);
        if ( $content ) $content = $renderer->newline( 2 ) . $content;
        return  $renderer->inDiv(  
                    $renderer->bold( $source->getName( )) 
                        . $renderer->newline() 
                        . $this->_sourceLink( $source )
                        . $content
                , array( 'style' => 'padding: 1em;'));

    }

    function _setSort_contentBox( $sort_direction ){
        $itemSource = &new $this->_source_object ( AMP_Registry::getDbcon( ));
        if( $itemSource->sort( $this->source, 'title', $sort_direction )){
            $this->_sort = 'title';
        }
    }

    function renderPublish( &$toolbar ){
        $renderer = &$this->_getRenderer( );
        $section_options = &AMPContent_Lookup::instance( 'sectionMap' );
        $section_options = array( '' => 'Select Section') + $section_options;
        $class_options = &AMPContent_Lookup::instance( 'classes');
                
        $toolbar->addEndContent( 
                $renderer->inDiv( 
                        '<a name="publish_targeting"></a>'
                        . $renderer->inSpan( AMP_TEXT_PUBLISH_TO  . ':&nbsp;' , array( 'class' => 'searchform_label'))
                        . AMP_buildSelect( 'section_id', $section_options, null, $renderer->makeAttributes( array( 'class' => 'searchform_element')))
                        . '&nbsp;'
                        . AMP_buildSelect( 'class_id', $class_options, 1, $renderer->makeAttributes( array( 'class' => 'searchform_element')))
                        . '&nbsp;'
                        . $toolbar->renderDefault( 'publish')
                        . '&nbsp;'
                        . "<input type='button' name='hidePublish' value='Cancel' onclick='window.change_any( \"publish_targeting\");'>&nbsp;",
                        array( 'class' => 'AMPComponent_hidden', 'id' => 'publish_targeting')
                    ), 'publish_targeting');

        return "<input type='button' name='showPublish' value='Publish' onclick='window.change_any( \"publish_targeting\");window.scrollTo( 0, document.anchors[\"publish_targeting\"].y );'>&nbsp;";

    }

}
?>