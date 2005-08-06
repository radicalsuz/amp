<?php

$mod_name = "email";

require_once( "AMP/System/Blast/ComponentMap.inc.php" );
require_once("AMP/System/Page/Blast.inc.php" );
require_once("AMP/System/Blast.inc.php");

$map = &new ComponentMap_Blast();
$page = &new AMPSystemPage_Blast ( $dbcon, $map );
$use_custom_sql = false;

if (isset($_GET['id']) && ($blast_ID = $_GET['id'])) {
    $page->addCallback( 'form', 'setDefaultValue', array( 'id', $blast_ID ));
}
if (isset($_POST['sqlp']) && ($emails_sql = stripslashes($_POST['sqlp']))) {
    $page->addCallback( 'form', 'setDefaultValue', array( 'passedsql', $emails_sql));
    $use_custom_sql = true;
}

if (isset($_REQUEST['modin']) && ($modin = $_REQUEST['modin'])) {
    $page->addCallback( 'form', 'setDefaultValue', array( 'modin', $modin ) );
}

if (isset($_POST['passedsql']) && ($_POST['passedsql'])) {
    $use_custom_sql = true;
}

if ($use_custom_sql) {
    $page->addCallback( 'form', 'addToFieldValueSet', array( 'modin', array('ZZ' => 'Custom Selection' )));
    $page->addCallback( 'form', 'setDefaultValue', array( 'modin', 'ZZ'));
}

$page->execute();

print $page->output();

?>
