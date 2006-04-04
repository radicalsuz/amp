<?php
require_once( 'AMP/System/Data/Item.inc.php' );
require_once( 'AMP/Content/Article/Set.inc.php' );
require_once( 'AMP/Content/Section.inc.php' );
require_once( 'RSSWriter/AMP_RSSWriter.php' );
require_once( 'AMP/Content/Page/Urls.inc.php' );

class AMPContent_RSSFeed extends AMPSystem_Data_Item {

    var $datatable = 'rssfeed';
    var $name_field =  'title';
    var $sourceSet;

    var $feed_metaData = array(
        'language' => 'en-us',
        'webmaster' => AMP_SITE_ADMIN );
    var $sourceItem_class = 'Article';

    function AMPContent_RSSFeed ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
        if ( isset( $id )) $this->getDataSource();
    }

    function getDescription() {
        return $this->getData( 'description' );
    }
    function getTitle() {
        return $this->getName();
    }
    function getLimit() {
        if(!$limit = $this->getData('sqllimit')) return 15;
        return $limit;
    }
    function getSort() {
        if(!($sortdef= $this->getData('orderbysql'))) return 'date desc';
        return $sortdef ." ". $this->getData('orderbyorder');
    }
    function getCombineLogic() {
        return $this->getData( 'combine_logic' );
    }
    function getCriteria() {
        $total_crit = array();
        if ($crit = $this->getData('sqlwhere')) {
            if ($bad_spot =  strpos( $crit, "or typeid" )) {
                $crit = substr( $crit, 0, $bad_spot ) . ")"; 
            }
            $total_crit[] = $crit;
        }
        if ( $id = $this->getData('class_id')) {
            $total_crit[] = "class =".$id;
        }
        if ( $id = $this->getData('section_id')) {
            $section = &new Section( $this->dbcon, $id );
            $crit = $section->getCriteriaforContent();
            $total_crit[] = $crit;
        }
        if (empty( $total_crit)) return false;
        return "( ". join( " ".$this->getCombineLogic()." ", $total_crit ). " )";

    }

    function getDataSource() {
        $articles = &new ArticleSet( $this->dbcon );
        $articles->addCriteria( $this->getCriteria() );
        $articles->addCriteria( 'publish=1' );
        $articles->setSort ( $this->getSort() );
        $articles->setLimit( $this->getLimit() );
        $articles->readData();
        $this->sourceSet = &$articles;
    }

    function &getDisplay() {
        $feed_name = ($name = $this->getTitle()) ? $name : AMP_SITE_NAME;
        $this->feed_metaData['pubDate'] = date('r');
        $this->feed_metaData['generator'] = 'Activist Mobilization Platform '.AMP_SYSTEM_VERSION_ID;
        if ( ! $this->sourceSet->isReady() ) return false;
        $display = &new AMP_RSSWriter( AMP_SITE_URL, $feed_name, AMP_SITE_META_DESCRIPTION, $this->feed_metaData ); 
        $articleSet = $this->sourceSet->instantiateItems( $this->sourceSet->getArray(), $this->sourceItem_class );
        foreach( $articleSet as $article ) {
            $articledMeta=array();

            $url = $article->getURL();
            if ( strpos( $url, "http://" ) === FALSE ) $url = AMP_SITE_URL . $url;

            $articleMeta['description'] = $this->_makeDescription( $article );
            if ($itemdate = $article->getItemDate() ) {
                $articleMeta['pubDate'] = strtotime( $itemdate );
            }
            $articleMeta['guid'] = $url;

            $display->addItem( $url, $article->getTitle(), $articleMeta);
        }
        return $display;
    }

    function _makeDescription( &$source ) {
        if ($this->getData('include_full_content')) {
            return $source->getBody();
        }
        return AMP_trimText( $source->getBlurb(), 9000 );
    }

    function setSection( $section_id ){
        return $this->mergeData( array( 'section_id' => $section_id ) );
    }

    function setClass( $class_id ){
        return $this->mergeData( array( 'class_id' => $class_id ) );
    }

    function setCombineLogic( $logic_value = 'AND'){
        return $this->mergeData( array( 'combine_logic' => $logic_value ) );
        
    }

}
?>
