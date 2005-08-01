<?php 
$state=$dbcon->CacheExecute("SELECT id, title FROM region order by title asc") or DIE($dbcon->ErrorMsg());
$state_numRows=0;
$state__totalRows=$state->RecordCount();

print "<select name=\"lstate\"  onChange=\"MM_jumpMenu('parent',this,0)\" class=\"ddmenu\">\n";
print        "<option value=\"\" SELECTED>Your Region</option>\n";
        
$link = isset($regionlink) ? $regionlink : "";
while (!$state->EOF ) {
    
    print '<option value="' . $link . '?area=' . $state->Fields("id") .'">' .
            $state->Fields("title") . "</option>\n";
    $state->MoveNext();
     
}
?>
