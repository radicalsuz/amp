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
          return  
                  $this->_renderPreviewLink( )
                . $this->_renderImage( )
                . $this->_renderTitle( AMP_TEXT_DOCUMENT_INFO )
                . $this->_renderArticleId( )
                . $this->_renderRedirects( )
                . $this->_renderSectionHeader( )
                . $this->_renderAttachments( )
                . $this->_renderCreated( )
                . $this->_renderUpdated( )
                . $this->_renderRevisionNotes( );

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
        return    
                 $this->_renderer->inSpan( $title, array( 'class' => 'doc_info' ))
                . $this->_renderer->newline( );
    }

    function _renderPreviewLink( ) {
        return  
            $this->_renderer->link( 
                    AMP_URL_AddVars( AMP_SITE_URL . $this->_article->getURL_default( ), 'preview=1', 'cache=0' ),
                    $this->_renderer->image( AMP_SYSTEM_ICON_PREVIEW, array( 'width' => '16', 'height' => '16', 'border' =>'0', 'align' => 'right' )),
                    array( 'target' => 'blank', 'title' => AMP_TEXT_PREVIEW_ITEM )
                );
    }

    function _renderArticleId( ){
        if ( strtolower( get_class( $this->_article)) == 'article_version' ) return $this->_renderArticleVersionId( );
        $article_url = AMP_Url_AddVars( AMP_SITE_URL . $this->_article->getURL_default( ), array( 'preview=1', 'cache=0' ));
        return $this->_renderer->inSpan( AMP_TEXT_ID . ': ' . $this->_article->id ) . $this->_renderer->space( 2 )
                . $this->_renderer->link( $article_url, '[ ' . ucfirst( AMP_TEXT_VIEW ) . ' ]' , array( 'target' => 'blank'))
                . $this->_renderer->newline( );
    }

    function _renderArticleVersionId( ){
        $article_url = AMP_SITE_URL . $this->_article->getURL( );
        return $this->_renderer->inSpan( AMP_TEXT_ID . ': ' . $this->_article->getArticleId( ) )
                . $this->_renderer->newline( )
                . $this->_renderer->inSpan( sprintf( AMP_TEXT_VERSION_ID, $this->_article->id ), array( 'class' => 'red')) . $this->_renderer->space( 2 )
                . $this->_renderer->link( $article_url, '[ ' . ucfirst( AMP_TEXT_VIEW ) . ' ]' )
                . $this->_renderer->newline( );

    }

    function _renderImage( ){
        if ( !( $image = &$this->_article->getImageRef( ))) return $this->_renderMediaThumbnail( );
        $image_url = AMP_Url_AddVars( AMP_SYSTEM_URL_IMAGE_VIEW, 
                        array( 'filename='.$image->getName( ),
                                'action=resize',
                                'image_class='. AMP_IMAGE_CLASS_THUMB,
                                'height=70' ));
        return $this->_renderer->div( 
                    $this->_renderer->image( $image_url ),
                        array( 'class' => 'thumb', 'id' => 'info_block_thumb' ));
                    # array( 'align' => 'right', 'border'=> 1)
    }

    function _renderMediaThumbnail( ) {
        if ( !( $image_url = $this->_article->getMediaThumbnailUrl( ))) return $this->_renderer->div( "&nbsp;", array( 'id' => 'info_block_thumb'));
        $media_url = $this->_article->getMediaUrl( );
        if ( !$media_url ) {
            return $this->_renderer->div( 
                        $this->_renderer->image( $image_url ),
                        array( 'class' => 'thumb', 'id' => 'info_block_thumb' ));
        }

        return $this->_renderer->div( 
            $this->_renderer->link( 
                $media_url, 
                $this->_renderer->image( $image_url ), 
                array( 'target' => '_blank')),
            array( 'class' => 'thumb', 'id' => 'info_block_thumb' ));
    }

    function _renderCreated( ){
        $date_created = $this->_article->getItemDateCreated( );
        $creator_id = $this->_article->getCreatorId( );
        $output = "";
        if ( $date_created && array_search( $date_created, AMPConstant_Lookup::instance( 'nullDatetimes')) !== FALSE ){
            $output = $this->_renderer->inSpan( ': ' . $date_created );
        }

        if ( $creator_id ){
            $user_names = AMPSystem_Lookup::instance( 'users' );
            $user_name = isset( $user_names[ $creator_id ]) ? $user_names[ $creator_id ] : "";
            $output .= $this->_renderer->inSpan( ' '. AMP_TEXT_BY . ' ' . $user_name);
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
            $user_names = AMPSystem_Lookup::instance( 'users' );
            $user_name = isset( $user_names[ $last_editor_id ]) ? $user_names[ $last_editor_id ] : "";
            $output .= $this->_renderer->inSpan( ' '. AMP_TEXT_BY . ' ' . $user_name );
        }

        if ( $output ) $output = AMP_TEXT_UPDATED . $output . $this->_renderer->newline( ) ;
        return $output;
    }

    function _renderRedirects( ){
        $redirect_url = $this->_article->getRedirect( );
        $aliases = $this->_article->getExistingAliases( );
        $output = '';
        if ( $redirect_url ){
            $output .= $this->_renderer->inSpan( 
                                        AMP_TEXT_REDIRECTED_TO . ': ' 
                                        . $this->_renderer->link( $redirect_url, $redirect_url, array( 'target' => 'blank' ) ), 
                                array( 'class' => 'red' ))
                        . $this->_renderer->newline( );
        }
        if ( $aliases ) {
            foreach( $aliases as $id => $alias ) {
                $alias_url = AMP_SITE_URL . $alias->getName( );
                $output .= $this->_renderer->inSpan( 
                                    AMP_TEXT_ALIAS . ': ' 
                                    . $this->_renderer->link( $alias_url, $alias_url, array( 'target' => 'blank' ) ))
                            . $this->_renderer->newline( );
            }
        }
        return $output;
    }

    function _renderSectionHeader( ){
        $section_header_lookup = AMPContent_Lookup::instance( 'sectionHeaders');
        $section_id = array_search( $this->_article->id, $section_header_lookup );
        if ( !$section_id ) return false;

        $section_url = AMP_SITE_URL . AMP_Url_AddVars( AMP_CONTENT_URL_ARTICLE, array( 'list=type', 'type='.$section_id ));

        $section_names_lookup = AMP_lookup( 'sections');
        $section_name = isset( $section_names_lookup[ $section_id ]) ? $section_names_lookup[ $section_id ] : false ;

        return $this->_renderer->inSpan( 
                        AMP_TEXT_SECTION_HEADER . ': ' 
                        . $this->_renderer->link( $section_url, $section_name, array(  'target' => 'blank')))
                    . $this->_renderer->newline( );
    }

    function _renderAttachments( ){
        $filename = $this->_article->getDocumentLink( );
        if ( !$filename ) return false;
        return $this->_renderer->inSpan( 
                    AMP_TEXT_ATTACHED_FILE . ': ' . 
                    $this->_renderer->link( AMP_SITE_URL . AMP_CONTENT_URL_DOCUMENTS . $filename, $filename ) )
                . $this->_renderer->newline( );
    }

    function _renderRevisionNotes( ) {
        if ( $this->_article->getStatus( ) != AMP_CONTENT_STATUS_REVISION ) {
            return false;
        }
        $notes = $this->_article->getData( 'notes' );
        preg_match( '/^.*?-{30}/s', $notes, $rev_matches );
        if ( empty( $rev_matches )) return false;
        $revision_notes = $rev_matches[0];
        return $this->_renderer->div( converttext( $revision_notes ), array( 'class' => 'revision_notes', 'onclick' => "change_any( 'article_fancy', 'article_tab');Tabs_highlight_mirror( $( 'tab_2_mirror'));AMP_show_panel( 'editor_notes');"));
    }

}

?>
