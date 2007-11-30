<?php
require_once( 'AMP/Content/Article.inc.php');
require_once( 'Modules/Rating/List.php');

class Rating_Display extends AMP_Display_Detail {
    var $_css_class_container_item = 'item_detail tabs_container';
    var $tabs_active = array( 'ratings' => false, 'comments' => false, 'emails' => false );

    function Rating_Display( $source = null ) {
        $this->__construct( $source );
    }

    function renderItem( $source ) {
        $content = 
            $this->render_ratings( )
            . $this->render_commented( )
            . $this->render_emailed( );
        return
            $this->render_tabs( )
            . $content;
        ;
    }

    function _renderFooter( ) {
        $tab_add = "PopularDisplay.add( '%1\$s_display', '%1\$s_trigger');";
        $tab_script = array( );
        $tab_trigger = '';
        foreach( array_filter( $this->tabs_active ) as $type => $active ) {
            $tab_script[] = sprintf( $tab_add, $type );
            if ( !$tab_trigger ) $tab_trigger = "PopularDisplay.show( $( '".$type."_trigger'))";
        }
        if( count( $tab_script ) < 2 ) return;

        $tab_script[] = $tab_trigger;

        $script = "PopularDisplay = AMP_simple_tabs( );\n" . join( "\n", $tab_script );

        $header = &AMP_get_header( );
        $header->addJavaScript( 'scripts/tabs.js', 'tabs');
        $header->addJavascriptOnLoad( $script, 'tabs_load');
    }

    function render_tabs( ) {
        $tabs = array( );
        foreach ( array_filter( $this->tabs_active ) as $type => $active ) {
            $tabs [] = $this->_renderer->link( 'NOLINK', ucfirst( $type ), array( 'id' => $type . '_trigger', 'onclick' => 'PopularDisplay.show( this );' ));
        }
        if( count( $tabs ) < 2 ) return false;
        return $this->_renderer->UL( $tabs, array( 'class' => 'tabs'));
    }

    function render_ratings( ) {

        $output = '';
        if( $list = $this->render_list( AMP_lookup( 'article_ratings'))) {
            $list->prioritize( 'rating');
            $output .= $this->render_header( 'All Time - Top Rated' );
            $output .= $list->execute( );
        }

        if( $list_weekly = $this->render_list( AMP_lookup( 'article_ratings_last_week'))){
            $list_weekly->prioritize( 'rating');
            $output .= $this->render_header( 'Last 7 days - Top Rated' );
            $output .= $list_weekly->execute( );
        }

        if ( $list_monthly = $this->render_list( AMP_lookup( 'article_ratings_last_month'))){
            $list_monthly->prioritize( 'rating');
            $output .= $this->render_header( 'Last 30 days - Top Rated');
            $output .= $list_monthly->execute( );
        }
        if( !$output ) return false;
        $this->tabs_active['ratings'] = true;
        return $this->_renderer->div( $output, array( 'id' => 'ratings_display'));

    }

    function render_commented( ) {
        $output = '';
        if ( $list_commented = $this->render_list( AMP_lookup( 'most_commented_articles'))){
            $list_commented->prioritize( 'comments' );
            $output .= $this->render_header( 'All Time - Most Commented' );
            $output .= $list_commented->execute( );
        }

        if ( $list_commented_by_week = $this->render_list( AMP_lookup( 'most_commented_articles_last_week'))){
            $list_commented_by_week->prioritize( 'comments');
            $output .= $this->render_header( 'Last 7 Days - Most Commented');
            $output .= $list_commented_by_week->execute( );
        }

        if ( $list_commented_by_month = $this->render_list( AMP_lookup( 'most_commented_articles_last_month'))){
            $list_commented_by_month->prioritize( 'comments');
            $output .= $this->render_header( 'Last 30 Days - Most Commented');
            $output .= $list_commented_by_month->execute( );
        }

        if( !$output ) return false;
        $this->tabs_active['comments'] = true;
        return $this->_renderer->div( $output, array( 'id' => 'comments_display'));

    }

    function render_emailed( ) {
        $output = '';
        if ( $list_emailed = $this->render_list( AMP_lookup( 'most_emailed_articles')) ){
            $list_emailed->prioritize( 'emails');
            $output .= $this->render_header( 'All Time - Most Emailed');
            $output .= $list_emailed->execute( );
        }

        if ( $list_emailed_by_week = $this->render_list( AMP_lookup( 'most_emailed_articles_last_week'))){
            $list_emailed_by_week->prioritize( 'emails');
            $output .= $this->render_header( 'Last 7 Days - Most Emailed');
            $output .= $list_emailed_by_week->execute( );
        }

        if ( $list_emailed_by_month = $this->render_list( AMP_lookup( 'most_emailed_articles_last_month'))) {
            $list_emailed_by_month->prioritize( 'emails');
            $output .= $this->render_header( 'Last 30 Days - Most Emailed');
            $output .= $list_emailed_by_month->execute( );
        }

        if( !$output ) return false;
        $this->tabs_active['emails'] = true;
        return $this->_renderer->div( $output, array( 'id' => 'emails_display'));

    }

    function render_list( $article_ids ) {
        if( !$article_ids ) return  false;
        $top_20 = array_slice( array_keys( $article_ids), 0, 20 );
        $top_articles = array_map( array( $this, 'make_article' ), $top_20 );
        $list = new Rating_List( $top_articles );
        $list->article_ids = $article_ids;
        return $list;
    }

    function make_article( $article_id ) {
        return new Article( AMP_Registry::getDbcon( ), $article_id );
    }

    function render_header( $text, $css_class = 'banner' ) {
        return $this->_renderer->div( $text, array( 'class' => $css_class ));
    }

}

?>
