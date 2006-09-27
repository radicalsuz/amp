<?php

$base_path = AMP_BASE_PATH;

// Not Quite Sure what these do.
$MX_type = "type";
$MX_top = AMP_CONTENT_MAP_ROOT_SECTION;

$PHP_SELF = $_SERVER['PHP_SELF'];

#load menu class	
if (file_exists($base_path."Connections/menu.class.php")) {
    require_once($base_path."Connections/menu.class.php");
    $obj = new Menu;
} 

$MM_USERNAME = AMP_DB_USER;
$MM_HOSTNAME = AMP_DB_HOST;
$MM_PASSWORD = AMP_DB_PASS;
$MM_DATABASE = AMP_DB_NAME;

//Article Custom Fields

AMP_defineLegacyCustomField( 1 );
AMP_defineLegacyCustomField( 2 );
AMP_defineLegacyCustomField( 3 );
AMP_defineLegacyCustomField( 4 );

?>
