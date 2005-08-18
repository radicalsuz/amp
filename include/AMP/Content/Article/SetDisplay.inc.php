<?php

require_once( 'AMP/Content/Article/Set.inc.php' );
require_once( 'AMP/Content/Display/List.inc.php' );

class ArticleSet_Display extends AMPContent_DisplayList_HTML {

    function ArticleSet_Display( &$articleSet ) {
        $this->init( $articleSet );
    }

    function _HTML_listItemDescription( $article ) {
        return
            $this->_HTML_listItemTitle( $article ) . 
            $this->_HTML_listItemSource( $article->getAuthor(), $article->getSource(), $article->getSourceURL()) .
            $this->_HTML_listItemDate( $article->getItemDate() ) .
            $this->_HTML_listItemBlurb( $article->getBlurb() );
    }


    function _HTML_listItemSource( $author, $source, $url ) {
        if (!(trim($author) || $source || $url)) return false;
        $output_author = FALSE;
        $output_source = FALSE;

        if (trim($author)) {
            $output_author =  $this->_HTML_inSpan( 'by&nbsp;' . converttext($author), 'bodygreystrong');
            if (!$source) return $output_author . $this->_HTML_newline();
        }

        if ($source) $output_source = $this->_HTML_inSpan( $this->_HTML_link( $url, $source  ), 'bodygreystrong' );

        if ($output_author && $output_source) return $output_author . ',&nbsp;' . $output_source . $this->_HTML_newline();

        return $output_source . $this->_HTML_newline();
    }

}
?>
