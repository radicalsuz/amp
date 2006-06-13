<?
$docroot = $_SERVER["DOCUMENT_ROOT"];
$settingsfile = $docroot."/../upload_settings.inc";
if(file_exists($settingsfile)) {
        eval(file_get_contents($settingsfile));
}
?>