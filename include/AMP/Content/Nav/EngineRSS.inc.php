<?php
if (!defined( 'MAGPIE_DIR' )) define('MAGPIE_DIR', 'FeedOnFeeds/magpierss/');
if (!defined( 'AMP_NAVTYPE_RSS_LINK_FORMAT')) define( 'AMP_NAVTYPE_RSS_LINK_FORMAT', '_HTML_link' );

require_once( MAGPIE_DIR.'rss_fetch.inc' );
require_once( 'AMP/Content/Nav/EngineRSS.inc.php' );

class NavEngine_RSS extends NavEngine {

    var $_engine_type = 'RSS';
    var $_feed;

    function NavEngine_RSS( &$nav ) {
        $this->init( $nav );
        $this->_setLimit();
    }

    function execute() {
        if (!($url = $this->nav->getRSS())) return false;
        if (!$this->setFeed( $url )) return false; 
        
        $items = $this->_feed->items;
        $this->nav->setCount( count($items) );
        if ($this->nav->exceedsLimit()) $items = array_slice( $this->_feed->items, 0, $this->nav->getLimit() );

        return $this->_linksGetArray( $items );
    }

    function setFeed( $url ) {
        if (! ( $feed = &$this->_grab( $url ) ) ) return false;
        if (! count( $feed->items ) ) return false;
        $this->_feed = &$feed;
        return true;
    }

    ############################################
    ### public subcomponent creation methods ###
    ############################################

    function processTitle( $title ) {
        if ($title) return $title;
        if (isset($this->feed->channel['title'])) return $this->feed->channel['title'];
    }

    function processMoreLink() {
        if ($link = $this->nav->getMoreLink()) return $link;
    }

    #####################################
    ### private link creation methods ###
    #####################################

    function _linksGetArray( $items ) {
        $result = array();

        foreach( $items as $item ) {
            $new_set = array( 'href'=>$item['link'], 'label' => $item['title']);
            if (isset($item['pubdate'])) $new_set['date'] = $item['pubdate'] ;
            $result[] = $new_set;
        }

        return $result;
    }


    ################################
    ###  private helper methods  ###
    ################################

    function _setLimit() {
        if (!($limit=$this->nav->getLimit())) {
            $this->nav->setLimit( 5 );
        }
    }

    function &_grab( $url ) {
		$error_level_tmp = error_reporting();
		error_reporting( E_ERROR );
		$rss = fetch_rss( $url );
		error_reporting( $error_level_tmp );
        return $rss;
    }

}
?>
