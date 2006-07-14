<?php
require_once( 'AMP/Content/Article/Display.inc.php');

class ArticleDisplay_Info extends Article_Display {

    var $_renderer;

    function ArticleDisplay_Info( &$article ){
        $this->init( $article );
        $this->_renderer = &new AMPDisplay_HTML( );
    }

    function _HTML_Header( ){
        return $this->_renderStart( );
    }

    function _HTML_Content( ){
          return  $this->_renderImage( )
                . $this->_renderTitle( AMP_TEXT_DOCUMENT_INFO )
                . $this->_renderArticleId( )
                . $this->_renderRedirects( )
                . $this->_renderAttachments( )
                . $this->_renderCreated( )
                . $this->_renderUpdated( );

    }

    function _HTML_Footer( ){
        return $this->_renderEnd( );
    }

    function _renderStart( ){
        return '<table width="100%"><tr><td class="info_block">'; 
    }

    function _renderEnd( ){
        return '</td></tr></table>';
    }

    function _renderTitle( $title ){
        return    $this->_renderer->inSpan( $title, array( 'class' => 'doc_info' ))
                . $this->_renderer->newline( );
    }

    function _renderArticleId( ){
        if ( strtolower( get_class( $this->_article)) == 'article_version' ) return $this->_renderArticleVersionId( );
        return $this->_renderer->inSpan( AMP_TEXT_ID . ': ' . $this->_article->id )
                . $this->_renderer->newline( );
    }

    function _renderArticleVersionId( ){
        return $this->_renderer->inSpan( AMP_TEXT_ID . ': ' . $this->_article->getArticleId( ) )
                . $this->_renderer->newline( )
                . $this->_renderer->inSpan( sprintf( AMP_TEXT_VERSION_ID, $this->_article->id ), array( 'class' => 'red'))
                . $this->_renderer->newline( );

    }

    function _renderImage( ){
        if ( !( $image = &$this->_article->getImageRef( ))) return false;
        $image_url = AMP_Url_AddVars( AMP_SYSTEM_URL_IMAGE_VIEW, 
                        array( 'filename='.$image->getName( ),
                                'action=resize',
                                'image_class='. AMP_IMAGE_CLASS_THUMB,
                                'height=70' ));
        return $this->_renderer->image( 
                    $image_url, array( 'align' => 'right', 'border'=> 1));
    }

    function _renderCreated( ){
        $date_created = $this->_article->getItemDateCreated( );
        $creator_id = $this->_article->getCreatorId( );
        $output = "";
        if ( $date_created && array_search( $date_created, AMPConstant_Lookup::instance( 'nullDatetimes')) !== FALSE ){
            $output = $this->_renderer->inSpan( ': ' . $date_created );
        }

        if ( $creator_id ){
            require_once( 'AMP/System/User/User.php');
            $user = &new AMPSystem_User( AMP_Registry::getDbcon( ), $creator_id );
            $output .= $this->_renderer->inSpan( ' '. AMP_TEXT_BY . ' ' . $user->getName( ));
            
        }
        if ( $output ) $output = AMP_TEXT_CREATED . $output . $this->_renderer->newline( );
        return $output;

    }

    function _renderUpdated( ) {
        $date_updated = $this->_article->getItemDateChanged( );
        $last_editor_id = $this->_article->getLastEditorId( );
        $output = "";
        if ( $date_updated && AMP_verifyDateTimeValue( $date_updated ) !== FALSE ){
            $output = $this->_renderer->inSpan( ': ' . $date_updated );
        }

        if ( $last_editor_id ){
            require_once( 'AMP/System/User/User.php');
            $user = &new AMPSystem_User( AMP_Registry::getDbcon( ), $last_editor_id );
            $output .= $this->_renderer->inSpan( ' '. AMP_TEXT_BY . ' ' . $user->getName( ));
        }

        if ( $output ) $output = AMP_TEXT_UPDATED . $output . $this->_renderer->newline( ) ;
        return $output;
    }

    function _renderRedirects( ){
        $redirect_url = $this->_article->getRedirect( );
        $aliases = &$this->_article->getExistingAliases( );
        $output = '';
        if ( $redirect_url ){
            $output .= $this->_renderer->inSpan( AMP_TEXT_REDIRECTED_TO . ': ' . $redirect_url, array( 'class' => 'red' ))
                        . $this->_renderer->newline( );
        }
        if ( $aliases ) {
            foreach( $aliases as $id => $alias ) {
                $output .= $this->_renderer->inSpan( AMP_TEXT_ALIAS . ': ' . AMP_SITE_URL .  $alias->getName( ))
                            . $this->_renderer->newline( );
            }
        }
        return $output;
    }

    function _renderAttachments( ){
        $filename = $this->_article->getDocumentLink( );
        if ( !$filename ) return false;
        return $this->_renderer->inSpan( 
                    AMP_TEXT_ATTACHED_FILE . ': ' . 
                    $this->_renderer->link( AMP_SITE_URL . AMP_CONTENT_URL_DOCUMENTS . $filename, $filename ) )
                . $this->_renderer->newline( );
    }

}

?>
