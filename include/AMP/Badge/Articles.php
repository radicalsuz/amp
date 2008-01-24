<?php

function amp_badge_articles( $options ) {
    $section = ( isset( $options['section']) && $options['section'] ) ? $options['section'] : false;
    $class = ( isset( $options['class']) && $options['class'] ) ? $options['class'] : false;
    $tag = ( isset( $options['tag']) && $options['tag'] ) ? $options['tag'] : false;
    $author = ( isset( $options['author']) && $options['author'] ) ? $options['author'] : false;
    $new = ( isset( $options['new']) && $options['new'] ) ? $options['new'] : false;
    $frontpage = ( isset( $options['frontpage']) && $options['frontpage'] ) ? $options['frontpage'] : false;
    $id = ( isset( $options['id']) && $options['id'] ) ? $options['id'] : false;
    $not_id = ( isset( $options['not_id']) && $options['not_id'] ) ? $options['not_id'] : false;
    if ( !( $section || $class || $tag || $new || $frontpage || $id || $author )) return false; 

    $header_output = false;
    $header = ( isset( $options['header'] )) ? $options['header'] : null;
    $display = ( isset( $options['display']) && $options['display'] ) ? $options['display'] : false;
    $display_header = ( isset( $options['display_header']) && $options['display_header'] ) ? $options['display_header'] : false;
    $display_footer = ( isset( $options['display_footer']) && $options['display_footer'] ) ? $options['display_footer'] : false;
    $display_subheader = ( isset( $options['display_subheader']) && $options['display_subheader'] ) ? $options['display_subheader'] : false;
    $display_morelink_url = ( isset( $options['display_morelink_url']) && $options['display_morelink_url'] ) ? $options['display_morelink_url'] : 'AMP_url_update';

    $limit = ( isset( $options['limit']) && $options['limit'] ) ? $options['limit'] : false;
    $morelink = ( isset( $options['morelink']) ) ? $options['morelink'] : null;
    $suppress_morelink = ( isset( $morelink ) && ( $morelink == false ));

    $criteria = array( );
    if ( $class )  $criteria['class'] = $class; 
    if ( $section )  $criteria['section'] = $section; 
    if ( $tag )  $criteria['tag'] = $tag; 
    if ( $new ) $criteria['new'] = $new; 
    if ( $frontpage ) $criteria['frontpage'] = $frontpage; 
    if ( $id ) $criteria['id'] = $id; 
    if ( $not_id ) $criteria['not_id'] = $not_id; 
    if ( $author ) $criteria['author'] = $author; 

    if ( $header ) {
        $renderer = AMP_get_renderer( );
        $header_output = $renderer->span( $header, array( 'class' => AMP_CONTENT_CSS_CLASS_BADGE_HEADER ));
    }

    require_once( 'AMP/Content/Badge/Public/Article/List.php');
    $list = &new AMP_Content_Badge_Public_Article_List( false, $criteria, $limit );
    if ( $display ) $list->set_display_method( $display );
    if ( $display_header ) $list->set_display_header_method( $display_header );
    if ( $display_footer ) $list->set_display_footer_method( $display_footer );
    if ( $display_subheader ) $list->set_display_subheader_method( $display_subheader );
    
    if ( $display_morelink_url && function_exists( $display_morelink_url ))  {
		$list->set_pager_target( $display_morelink_url( $morelink, $criteria, $options ));
	}
    if ( $suppress_morelink ) $list->suppress( 'pager' );
    return $header_output . $list->execute( );

}

function amp_render_article_for_nav( $article, &$list ) {
    return  $list->render_title( $article )
            . $list->render_date( $article );
}

?>
