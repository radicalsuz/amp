<?php
require_once( 'AMP/Display/Detail.php');

class Rating_Public_Display extends AMP_Display_Detail {

    var $max = AMP_MODULE_RATING_SCALE_MAX;

    function default_rating( ) {
        if( $this->max < 3 ) return 0;
        return ceil( $this->max / 2 );
    }

    function _init_javascript( ) {
        $header = &AMP_get_header( );
        $header->addJavascript( 'scripts/rating.js', 'rating');
    }

    function renderItem( $source ) {
        return $this->render_stars( $source ) 
                . $this->_renderer->newline( )
                . sprintf( AMP_TEXT_STARS, $active ) 
                . $this->_renderer->space( )
                . $this->render_text_stars( $source );
    }

    function render_text_stars( $source ) {
        $active = $this->user_rating( $source );
        $text_stars = array( '' => AMP_TEXT_RATE_THIS );
        $text_stars = $text_stars + array_fill( 1, $this->max , AMP_TEXT_STARS );
        foreach( $text_stars as $key => $star ) {
            $text_stars[$key] = sprintf( $text_stars[$key], $key);
        }
        return $this->_renderer->select( 'make_rating', $active, $text_stars, array( 'onchange' => 'AMP_rate_item( this.value, '.$source->id .' )') );

    }

    function render_current_rating( $source ) {
        $rating = $this->avg_rating( $source );
        if ( !$rating ) return false;
        $current_text = sprintf( "Average Rating: %.1f" , $rating );
        $current_stars = $this->_renderer->div( ' ', array( 'style' => 'width: ' . ceil( $rating * 15 ) . 'px;', 'class' => 'visual'));
        return $this->_renderer->div( $current_stars . $current_text, array( 'class' => 'rating_current_value'));

    }

    function render_block( $source ) {
        return $this->_renderer->div( 
                $this->_renderer->label( 'make_rating', 'Rate this Article:') 
                . $this->_renderer->div( 
                                        $this->_renderer->image( '/img/ajax-loader.gif', array( 'class' => 'icon'))
                                        . $this->renderItem( $source )
                                        , array( 'id' => 'rating' ))
                . $this->render_current_rating( $source ),
                array( 'class' => 'rating_block')
                );


    }

    function _renderBlock( $html ) {
        return $html;
    }

    function render_stars( $source ) {
        $active = $this->user_rating( $source );

        $active_star = $this->_renderer->div( '', array( 'class' => 'star_on', 'onclick' => 'AMP_rate_item( %1$s, %2$s )', 'title' => AMP_TEXT_STARS)); 
        $passive_star = $this->_renderer->div( '', array( 'onclick' => 'AMP_rate_item( %1$s, %2$s )', 'title' => AMP_TEXT_STARS));
        $stars = array_fill( 0, $active, $active_star );
        $stars = array_pad( $stars, $this->max, $passive_star );
        foreach( $stars as $key => $star ) {
            $stars[$key] = sprintf( $star, $key+1, $source->id ) ;
        }
        return join( "\n", $stars ) ;

    }

    function avg_rating( $source ) {
       return ArticleRating::average( $source->id );

    }

    function user_rating( $source ) {
       $value = ArticleRating::current( $source->id );
       if ( $value ) return $value;
       return $this->default_rating( );
    }
}

?>
