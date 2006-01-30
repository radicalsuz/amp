<?php
require_once("AMP/BaseDB.php");

#decalre what is needed for each request
if ($_REQUEST['author']) {
    $value = $_REQUEST['author'];
    $table = 'articles';
    $field ='author';
}

if ($_REQUEST['source']) {
    $value = $_REQUEST['source'];
    $table = 'articles';
    $field ='source';
}
$sql = "SELECT Distinct $field FROM $table WHERE $field LIKE '".$value."%'";   $R= $dbcon->CacheExecute($sql)or DIE($sql.$dbcon->ErrorMsg());

echo '<ul>';
while (!$R->EOF){ 
    echo '<li>'.$R->Fields($field).'</li>';
    $R->MoveNext();
}
echo '</ul>';
?>