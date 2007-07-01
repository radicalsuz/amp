<?php

function amp_badge_articles( $options ) {
    $section = ( isset( $options['section']) && $options['section'] ) ? $options['section'] : false;
    $class = ( isset( $options['class']) && $options['class'] ) ? $options['class'] : false;
    if ( !( $section || $class )) return false; 

    $header_output = false;
    $header = ( isset( $options['header'] )) ? $options['header'] : null;
    $limit = ( isset( $options['limit']) && $options['limit'] ) ? $options['limit'] : false;

    $criteria = array( );
    if ( $class ) {
        $criteria['class'] = $class;
        if ( !isset( $header )) {
            $class_names = AMP_lookup( 'classes');
            $header =  isset( $class_names[$class]) ? AMP_pluralize( $class_names[$class] ) : false;
        }
    }
    if ( $section ) {
        $criteria['section'] = $section;
        if ( !isset( $header )) {
            $section_names = AMP_lookup( 'sectionsLive');
            $header =  isset( $section_names[$section]) ? $section_names[$section] : false;
        }
    }

    if ( $header ) {
        $renderer = AMP_get_renderer( );
        $header_output = $renderer->span( $header, array( 'class' => AMP_CONTENT_CSS_CLASS_BADGE_HEADER ));
    }

    require_once( 'AMP/Content/Article/Public/List.php');
    require_once( 'AMP/Content/Badge/Public/Article/List.php');
    $list = &new AMP_Content_Badge_Public_Article_List( false, $criteria, $limit );
    return $header_output . $list->execute( );

}


?>
