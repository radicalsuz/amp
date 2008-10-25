<?php

require_once( 'AMP/BaseDB.php');
require_once( 'AMP/Content/Article/Trackback.php');
AMP_config_load('urls' );

if ( !AMP_CONTENT_TRACKBACKS_ENABLED ) {
    print 'Sorry, trackbacks are currently disabled due to abuse.';
    exit;
}


$article_id = 0;
if ( isset( $_GET['id']) && is_numeric( $_GET['id']) && $_GET['id']) {
    $article_id = intval( $_GET['id'] );
}

$headers = apache_request_headers();
if(!isset($headers['Content-Type']) || strpos($headers['Content-Type'], 'application/x-www-form-urlencoded') !== 0) {
  $response = &new ArticleTrackback_Response(1, "Content-Type header must be 'application/x-www-form-urlencoded'");
  print $response->execute( );
  exit;
}

if(!isset($_POST['url'])) {
  $response = &new ArticleTrackback_Response(1, "No URL given");
  print $response->execute( );
  exit;
}

$trackback = &new ArticleTrackback( $dbcon );
$trackback->setDefaults();
$allowed_tags = $trackback->getAllowedTags( );
$trackback_data = array_combine_key( $allowed_tags, $_POST );
$trackback_data['article_id'] = $article_id;
if(isset($headers['charset'])) {
  $trackback_data['charset'] = $headers['charset'];
}
//content-type should be something like "application/x-www-form-urlencoded; charset=utf-8"
$content_type = explode(';', $headers['Content-Type']);
foreach($content_type as $param) {
  if(strpos($param, 'charset=') === 0) {
    $charset = explode('=', $param);
    $trackback_data['charset'] = trim($charset[1]);
  }
}

// no trackback data received, redirect to article display page
if (  !( 
       ( isset( $trackback_data['title'])       && $trackback_data['title']     )
    && ( isset( $trackback_data['url'])         && $trackback_data['url']       )
    && ( isset( $trackback_data['blog_name'] )  && $trackback_data['blog_name'] ) )) {
    if ( $article_id ) ampredirect( AMP_Url_AddVars( AMP_CONTENT_URL_ARTICLE, "id=".$article_id ));
}

if ( $trackback->setPingData( $trackback_data )) {

    //if ( $trackback->validate( )){
    //    $trackback->publish( );
    //}

    $trackback->save( );
}

$display = &$trackback->getResponse( );

print $display->execute( );


?>
