<?php

require_once( 'AMP/BaseDB.php');
require_once( 'AMP/Content/Article/Trackback.php');

$article_id = 0;
if ( isset( $_GET['id']) && is_numeric( $_GET['id'] && $_GET['id'])) {
    $article_id = $_GET['id'];
}

$trackback = &new ArticleTrackback( $dbcon );
$allowed_tags = $trackback->getAllowedTags( );
$trackback_data = array_combine_key( $allowed_tags, $_POST );
$trackback_data['article_id'] = $article_id;

// no trackback data received, redirect to article display page
if ( !( isset( $trackback_data['title']) && isset( $trackback_data['url']) && isset( $trackback_data['blog_name'] ))){
    require_once( 'AMP/Content/Page/Urls.inc.php');
    if ( $article_id ) ampredirect( AMP_Url_AddVars( AMP_CONTENT_URL_ARTICLE, "id=".$article_id ));
}

if ( $trackback->setPingData( $trackback_data )) {
    $trackback->save( );
}

$display = &$trackback->getResponse( );

print $display->execute( );

?>
