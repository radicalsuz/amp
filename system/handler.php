<?php
require_once( 'AMP/System/Base.php');
$request_vars = AMP_URL_Read( );

$map_name = ( isset( $request_vars['component'] ) && $request_vars['component']) ? $request_vars['component'] :false;
if ( !$map_name ){
    ampredirect( AMP_SYSTEM_URL_HOME );
    exit;
}
$map_folders = array( AMP_SYSTEM_INCLUDE_PATH, AMP_CONTENT_INCLUDE_PATH, AMP_MODULE_INCLUDE_PATH );
$map_class = false;
foreach( $map_folders as $folder ){
    $test_path = $folder . DIRECTORY_SEPARATOR . $map_name . DIRECTORY_SEPARATOR . AMP_COMPONENT_MAP_FILENAME  ; 
    if ( !file_exists_incpath( $test_path )) continue;
    include_once( $test_path );
    if ( !class_exists( AMP_COMPONENT_MAP_CLASSNAME . '_' . $map_name )) continue;
    $map_class = AMP_COMPONENT_MAP_CLASSNAME . '_' . $map_name;
}

if ( !$map_class ){
    ampredirect( AMP_SYSTEM_URL_HOME );
    exit;
}

$map = &new $map_class();
$controller = &$map->get_controller( );

print $controller->execute( );

?>
