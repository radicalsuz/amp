<?php

require_once( 'AMP/Content/Article/Set.inc.php' );
require_once( 'AMP/Content/Display/List.inc.php' );

class ArticleSet_Display extends AMPContent_DisplayList_HTML {
    var $_css_class_author = "bodygreystrong";
    var $_css_class_source = "bodygreystrong";

    function ArticleSet_Display( &$articleSet, $read_data = true ) {
        $this->init( $articleSet, $read_data );
    }

    function _HTML_listItemDescription( &$article ) {
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
            $output_author =  $this->_HTML_inSpan( 'by&nbsp;' . converttext($author), $this->_css_class_author );
            if (!$source) return $output_author . $this->_HTML_newline();
        }

        if ($source) $output_source = $this->_HTML_inSpan( $this->_HTML_link( $url, $source  ), $this->_css_class_source );

        if ($output_author && $output_source) return $output_author . ',&nbsp;' . $output_source . $this->_HTML_newline();

        return $output_source . $this->_HTML_newline();
    }

}
?>
