<?php
require_once( 'AMP/Content/Article/Comment/ArticleComment.php');

class AMP_Content_Article_Comment_Public_Display_List {

    var $_renderer;
    var $_source;

    function AMP_Content_Article_Comment_Public_Display_List( &$comments ){
        $this->__construct( $comments );
    }

    function __construct( &$source ) {
        $this->_source = &$source;
    }

    function &_get_renderer( ) {
        if ( isset( $this->_renderer )) return $this->_renderer;
        
        require_once( 'AMP/Content/Article/Comments.inc.php');
        $fake_set = &new AMPSystem_Data_Set( AMP_Registry::getDbcon( ));
        $this->_renderer = &new ArticleCommentSet_Display( $fake_set );

        return $this->_renderer;
    }

    function execute( $options = array( )) {
        if ( !$this->_source ) return false;
        if ( !( isset( $options['format_detail'] ) && $options['format_detail'] )) {
            return $this->output( );
        } else {
            $detail_format = $options['format_detail'];
            if ( is_callable( array( $this, $detail_format))) {
                return $this->$detail_format( $options );
            }
            if ( is_callable( $detail_format)) {
                return $detail_format( $this->_source, $options );
            }
            trigger_error( sprintf( AMP_TEXT_ERROR_METHOD_NOT_SUPPORTED, 'AMP', $detail_format, get_class( $this ) ));

        }
    }

    function output( ){
        $output = false;
        $renderer = &$this->_get_renderer( );

        foreach( $this->_source as $comment ) {
            $output .= 
                $this->_renderer->comment( 
                    $this->_renderer->p_commaJoin( 
                        array( 
                            $this->_renderer->author( $comment->getAuthor( ), $comment->getAuthorUrl( )),
                            $this->_renderer->date( $comment->getItemDate( ))
                        )
                    ) 
                    . $this->_renderer->commentBody( $comment->getBody( ))
                );
        }
        $output = "<ol>\n" . $output . "\n</ol>\n\n";
        return $output;


    }

}

?>
