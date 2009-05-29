<?php

require_once('AMP/Base/Config.php');
require_once('AMP/Content/RouteSlug/RouteSlug.php');

if (!($raw_slug_name = AMP_params('slug_name') )) exit;

$finder = new AMP_Content_RouteSlug(AMP_dbcon());
$slug_name = $finder->clean($raw_slug_name);
$raw_matches = $finder->find( array( 'name' => $slug_name ));
$exceptions = AMP_params('ignore');

$matches = array();
if (!empty($raw_matches) && $exceptions) {
    foreach($exceptions as $ignore) {
        foreach($raw_matches as $match_key => $match) {
            if (!($match->getData('owner_type') == $ignore['owner_type'] and $match->getData('owner_id') == $ignore['owner_id'] )) {
                $matches[] = $match;
            } 
        }
    }
} else {
    $matches = $raw_matches;
}

if( empty($matches)) {
    print AMP_to_json( array( 'clean_url' => $slug_name, 'conflicts' => array() ) );
    exit;
}

$match_results = array();
foreach( $matches as $route ) {
   $owner = $route->getOwner();
   $match_results[] = array_merge($route->getData(), array('owner_edit_url' => $owner->get_url_edit() ) ); 
}

print AMP_to_json( array( 'clean_url' => $finder->find_valid_slug($slug_name), 'conflicts' => $match_results ) );


?>
