<?php

function amp_badge_articles( $options ) {
    $section = ( isset( $options['section']) && $options['section'] ) ? $options['section'] : false;
    $class = ( isset( $options['class']) && $options['class'] ) ? $options['class'] : false;
    $tag = ( isset( $options['tag']) && $options['tag'] ) ? $options['tag'] : false;
    $new = ( isset( $options['new']) && $options['new'] ) ? $options['new'] : false;
    $frontpage = ( isset( $options['frontpage']) && $options['frontpage'] ) ? $options['frontpage'] : false;
    if ( !( $section || $class || $tag || $new || $frontpage )) return false; 

    $header_output = false;
    $header = ( isset( $options['header'] )) ? $options['header'] : null;
    $display = ( isset( $options['display']) && $options['display'] ) ? $options['display'] : false;
    $display_header = ( isset( $options['display_header']) && $options['display_header'] ) ? $options['display_header'] : false;
    $display_footer = ( isset( $options['display_footer']) && $options['display_footer'] ) ? $options['display_footer'] : false;

    $limit = ( isset( $options['limit']) && $options['limit'] ) ? $options['limit'] : false;
    $morelink = ( isset( $options['morelink']) ) ? $options['morelink'] : null;
    $suppress_morelink = ( isset( $morelink ) && ( $morelink == false ));

    $criteria = array( );
    if ( $class ) {
        $criteria['class'] = $class;
        if ( !isset( $header ) && !is_array( $class )) {
            $class_names = AMP_lookup( 'classes');
            $header =  isset( $class_names[$class]) ? AMP_pluralize( $class_names[$class] ) : false;
        }
    }
    if ( $section ) {
        $criteria['section'] = $section;
        if ( !isset( $header ) && !is_array( $section )) {
            $section_names = AMP_lookup( 'sectionsLive');
            $header =  isset( $section_names[$section]) ? $section_names[$section] : false;
        }
    }
    if ( $tag ) {
        $criteria['tag'] = $tag;
        if ( !isset( $header ) && !is_array( $tag )) {
            $tag_names = AMP_lookup( 'tagsLive');
            $header =  isset( $tag_names[$tag]) ? $tag_names[$tag] : false;
        }
    }


    if ( $new ) $criteria['new'] = $new; 
    if ( $frontpage ) $criteria['frontpage'] = $frontpage; 

    if ( $header ) {
        $renderer = AMP_get_renderer( );
        $image_path = AMP_LOCAL_PATH . 'img/original/' . $header;
        if ( file_exists( $image_path )) {
            $header = $renderer->image( 'img/original/'.$header );
        }
        $header_output = $renderer->span( $header, array( 'class' => AMP_CONTENT_CSS_CLASS_BADGE_HEADER ));
    }

    require_once( 'AMP/Content/Badge/Public/Article/List.php');
    $list = &new AMP_Content_Badge_Public_Article_List( false, $criteria, $limit );
    if ( $display ) $list->set_display_method( $display );
    if ( $display_header ) $list->set_display_header_method( $display_header );
    if ( $display_footer ) $list->set_display_footer_method( $display_footer );
    if ( $morelink )  {
		$list->set_pager_target( AMP_url_update( $morelink, $criteria ));
	}
    if ( $suppress_morelink ) $list->suppress( 'pager' );
    return $header_output . $list->execute( );

}

function AMP_criteria_join($criteria) {
	$result_criteria = array();
	foreach($criteria as $var => $value ) {
		if (is_array($value)) $value = join(',', $value);
	    $result_criteria[$var] = $value;	
	}
	return $result_criteria;
}



?>
