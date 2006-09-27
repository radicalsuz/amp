<?php
require_once( 'AMP/UserData/Plugin.inc.php');
require_once( 'AMP/Content/Article/Comment/Public/Display/List.php');
require_once( 'AMP/Content/Article/Comment/ArticleComment.php');
require_once( 'AMP/Content/Page/Urls.inc.php');

class UserDataPlugin_Comments_Output extends UserDataPlugin {
    var $options = array( 
        '_linked_uid' => array( 
            'type' => 'text',
            'available' => false 
            ),
        'format_detail' => array( 
            'type'      => 'text',
            'available' => true,
            'label'     => 'Format Function',
            'default'   => ''
            ),
        'format_list' => array( 
            'type'      => 'text',
            'available' => true,
            'label'     => 'Format Function',
            'default'   => '_listLink'
            )
        );
    var $available = true;
    var $_renderer;

    var $_fields;

    function UserDataPlugin_Comments_Output ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

    function execute( $options=array( )) {
        $options=array_merge($this->getOptions(), $options);

        if (!( isset($this->udm->uid) && $this->udm->uid )) {
            $list_format_function = $options['format_list'];
            if ( is_callable( array( $this, $list_format_function ))) {
                return $this->$list_format_function( $options );
            }
            if ( is_callable( $list_format_function )) {
                return $list_format_function( $options, $this );
            }
            trigger_error( sprintf( AMP_TEXT_ERROR_METHOD_NOT_SUPPORTED, 'AMP', $list_format_function, get_class( $this ) ));

        } else {
            $options['_linked_uid'] = $this->udm->uid;
            $output = $this->_listLink( $options );
            $comment_source = & new ArticleComment( $this->dbcon );
            $comments = & $comment_source->search( $comment_source->makeCriteria( array( 'userdata_id' => $this->udm->uid )));
            if ( !$comments ) return $output; 
            $display = &new AMP_Content_Article_Comment_Public_Display_List( $comments );
            return $output . $display->execute( $options );

        }

    }

    function _register_fields_dynamic( ){
        if ( $this->udm->admin ) {
            $this->udm->registerPlugin( 'Comments', 'Read' );
        }
    }

    function _listLink( $options = array( )) {
        if ( !isset( $options['_linked_uid'] )) {
            return false;
        }

        $comment_count_lookup = FormLookup::instance( 'commentCounts');
        $comment_count = isset( $comment_count_lookup[ $options['_linked_uid']]) ? 
                                $comment_count_lookup[ $options['_linked_uid']] : 0; 

        $link_text = ( $comment_count ? $comment_count : AMP_TEXT_NO ) 
                        . '&nbsp;'
                        . AMP_pluralize( AMP_TEXT_COMMENT ); 

        $renderer = &$this->_get_renderer( );

        //current comment count
        $comments =  $renderer->link( 
                        AMP_Url_AddAnchor( 
                                AMP_Url_AddVars( 
                                    PHP_SELF_QUERY( ), 
                                    array( 'uid='.$options['_linked_uid'])), 
                                'comments'),
                        $link_text );

        //add comment link
        $comments .=    $renderer->separator( ) 
                        . $renderer->link( 
                                AMP_Url_AddVars( AMP_CONTENT_URL_COMMENT_ADD , array( 'userdata_id=' . $options['_linked_uid'])),
                                AMP_TEXT_ADD
                                );

        return $comments . $renderer->newline( 2 );

    }

    function &_get_renderer( ){
        if ( isset( $this->_renderer )) return $this->_renderer;
        $this->_renderer = &new AMPDisplay_HTML;
        return $this->_renderer;
    }

}

?>
