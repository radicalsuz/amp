<?php

function articleversion($id) {
	global $dbcon;
	$get=$dbcon->Execute("select * from articles where id = $id") or DIE($dbcon->ErrorMsg());
	$i = 0;
	foreach ($get->FetchRow() as $k => $v) {
		if ($i % 2) {
			$fields .=  $k. ",";
			$values .= "'". addslashes($v) . "',";
		}
		$i++;
	} 
	$fields = substr( $fields, 0, -1 );
	$values = substr( $values, 0, -1 );

	$sql ="Insert into articles_version ($fields) values ($values)"; 
	
	//echo $sql;
	$insert=$dbcon->Execute($sql) or DIE($dbcon->ErrorMsg());

}


function articleversionrestore($vid) {
	global $dbcon;
	
	$get=$dbcon->Execute("select * from articles_version where vid = $vid") or DIE($dbcon->ErrorMsg());
	$id = $get->Fields("id");
	articleversion($id);
	$i = 0;
	foreach ($get->FetchRow() as $k => $v) {
		if ($i % 2) {
			$matchedpairs .= $k." = '" . addslashes($v) ."',";
		}
		$i++;
	}
	
	$matchedpairs = substr( $matchedpairs, 0, -1 );
	$matchedpairs = preg_replace( "/vid = '(\d+)',/", " ", $matchedpairs );
	
	$sql = "UPDATE articles SET $matchedpairs where id  = $id";
	echo $sql;
	$update=$dbcon->Execute($sql) or DIE($dbcon->ErrorMsg());
}

function articleversionlist($id) {
	global $dbcon;
	$get=$dbcon->Execute("select a.vid, DATE_FORMAT(a.updated, '%c/%e/%Y %H:%i ') as updated, u.name from articles_version a left join users u on u.id = a.updatedby where a.id = $id") or DIE($dbcon->ErrorMsg());
	if ($get->Fields("vid")) {
	?><h2>Version History</h2>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr class="intitle">
    <td>Version Id </td>
    <td>Last Updated </td>
    <td>Updated By </td>
    <td></td>
	<td></td>
	<td></td>
  </tr>
  <?php while (!$get->EOF) {?>
  <tr>
    <td><?php echo $get->Fields("vid");?></td>
    <td><?php echo $get->Fields("updated");?></td>
    <td><?php echo $get->Fields("name");?></td>
    <td><a href="../article.php?vid=<?php echo $get->Fields("vid");?>&preview=1" target="_blank">View</a></td>
	<td><a href="article_edit.php?vid=<?php echo $get->Fields("vid");?>">Edit</a></td>
	<td><a href="article_edit.php?restore=<?php echo $get->Fields("vid");?>">Restore</a></td>
  </tr>
  <?php $get->MoveNext() ; } ?>
</table>
<?php
	}
}



?>
