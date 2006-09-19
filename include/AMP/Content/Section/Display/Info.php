<?php

class AMP_Content_Section_Display_Info {

    var $_renderer;
    var $_section;

    function AMP_Content_Section_Display_Info( &$section ){
        $this->__construct( $section );
    }

    function __construct( &$section ) {
        $this->_section = &$section;
        $this->_renderer = &AMP_get_renderer( );
    }

    function execute() {
        if ( !isset( $this->_section) || !isset( $this->_section->id )) {
            return false;
        }
        return  $this->_render_header().
                $this->_render_content().
                $this->_render_footer();
    }


    function _render_header( ){
        return $this->_renderStart( );
    }

    function _render_content( ){
          return  
                  $this->_renderPreviewLink( )
                . $this->_renderTitle( AMP_TEXT_DOCUMENT_INFO )
                . $this->_renderItemId( )
                . $this->_renderRedirects( )
                . $this->_renderSectionHeader( )
                . $this->_renderContentLink( )
                . $this->_renderer->newline( );

    }

    function _render_footer( ){
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

    function _renderContentLink( ) {
        return AMP_TEXT_CONTENT_PAGES. ': ' 
                . $this->_renderer->space( )
                . $this->_renderer->link( 
                    AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE, array( 'section=' . $this->_section->id )),
                    '[ ' . AMP_TEXT_VIEW. ' ]' 
                    . $this->_renderer->space( )
                    . $this->_renderer->image( AMP_SYSTEM_ICON_VIEW, array( 'height' => 16, 'width' => 16, 'border' => 0) ) 
                );
    }

    function _renderPreviewLink( ) {
        return  
            $this->_renderer->link( 
                    AMP_URL_AddVars( AMP_SITE_URL . $this->_section->getURL_default( ), array( 'preview=1', 'cache=0' ) ),
                    $this->_renderer->image( AMP_SYSTEM_ICON_PREVIEW, array( 'width' => '16', 'height' => '16', 'border' =>'0', 'align' => 'right' )),
                    array( 'target' => 'blank', 'title' => AMP_TEXT_PREVIEW_ITEM )
                );
    }

    function _renderItemId( ){
        $section_url = AMP_Url_AddVars( AMP_SITE_URL . $this->_section->getURL_default( ), array( 'preview=1', 'cache=0' ));
        return $this->_renderer->inSpan( AMP_TEXT_ID . ': ' . $this->_section->id ) . $this->_renderer->space( 2 )
                . $this->_renderer->link( $section_url, '[ ' . ucfirst( AMP_TEXT_VIEW ) . ' ]', array( 'target' => 'blank') )
                . $this->_renderer->newline( );
    }

    function _renderRedirects( ){
        $redirect_url = $this->_section->getRedirect( );
        $aliases = $this->_section->getExistingAliases( );
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
        $section_header = &$this->_section->getHeaderRef( );
        if ( !$section_header ) return false;

        $section_header_url = AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE, array( 'id=' . $section_header->id ));

        return AMP_TEXT_SECTION_HEADER . ': '
                . AMP_trimText( $section_header->getName( ), 30, false )
                . $this->_renderer->space( 2 )
                . $this->_renderer->link( 
                    $section_header_url,
                        '[ ' . AMP_TEXT_EDIT . ' ]'
                        . $this->_renderer->space( )
                        . $this->_renderer->image( AMP_SYSTEM_ICON_EDIT, array( 'width' => '16', 'height' => '16', 'border' => '0') )
                        )
                    . $this->_renderer->newline( );
    }

}

?>
