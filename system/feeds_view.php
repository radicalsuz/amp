<?php
#generic update page
$modid = "45";

require("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$buildform = new BuildForm;
$obj = new SysMenu; 
$class=$dbcon->Execute("SELECT id, class FROM class ORDER BY id ASC") or DIE($dbcon->ErrorMsg());

function feed_read($id) {
	global $dbcon;
	$q = "delete from px_items where id=$id";
	//die($q);
	$dbcon->execute($q) or die($q.$dbcon->errorMsg());
}

function feed_publish($id,$type,$class) {
	global $dbcon, $ID;
	$d=$dbcon->execute("select p.*, f.title as ftitle from px_items p, px_feeds f where f.id = p.feed_id and  p.id = $id ") or die($dbcon->errorMsg());
	//pasre out date
	
	if (strlen($d->Fields("content")) > 750 ) {
		$text = addslashes($d->Fields("content"));
		$aspace=" ";
		$ttext = addslashes($d->Fields("content"));
		$ttext = substr(trim($ttext),0,750); 
		$ttext = substr($ttext,0,strlen($ttext)-strpos(strrev($ttext),$aspace));
		$ttext = $ttext.'...';
		$shortdesc = $ttext;
	} else {
		$text = addslashes($d->Fields("content"));
		$shortdesc = addslashes($d->Fields("content"));
		$linkover = 1 ;
	}
	$q = "insert into articles (title,class,type,shortdesc,test,date,linkover,link,source,sourceurl,publish,uselink,enteredby,updatedby,datecreated) values('".addslashes($d->Fields("title"))."','".$class."','".$type."','".$shortdesc."','".$text."','".DoTimeStamp($d->Fields("timestamp"),("Y-n-j"))."','".$linkover."','".addslashes($d->Fields("link"))."','".addslashes($d->Fields("ftitle"))."','".addslashes($d->Fields("link"))."','1','1','".$ID."','".$ID."',now())";
	//die($q);
	$dbcon->execute($q) or die($dbcon->errorMsg());
	feed_read($id);
}

if ($_POST[act]) {
	foreach ($_POST[read] as $k => $v) {
		if ($v == 1) {feed_read($k) ;}
	}

	foreach ($_POST[publish] as $k => $v) {
		if ($v == 1) {
			feed_publish($k,$_POST[type][$k],$_POST[fclass][$k]) ;
		}
	}

}
if ($_GET[feed]) { $feedsql = " and p.feed_id = ".$_GET[feed]." "; }

if ($_GET[offset]) {$offset=$_GET[offset];}
else { $offset=0;}
if ($_GET[limit]) {$limit=$_GET[limit];}
else { $limit=30;}



$rs  = $dbcon->Execute("select p.*, f.title as ftitle from px_items p, px_feeds f where f.id = p.feed_id  $feedsql order by p.id desc Limit  $offset, $limit") or die($dbcon->ErrorMsg() );
$sqlct = "select p.id from px_items p, px_feeds f where f.id = p.feed_id $feedsql order by p.id desc ";
$f  = $dbcon->Execute( "select id, title from px_feeds order by title asc" ) or die(  $dbcon->ErrorMsg() );


###########################KEEP PARAMATERS###########################
// create the list of parameters which should not be maintained
$MM_removeList = "&order= &offset= &limit=";
if ($MM_paramName != "") $MM_removeList .= "&".strtolower($MM_paramName)."=";

// add the URL parameters to the MM_keepURL string
reset ($_GET);
while (list ($key, $val) = each ($_GET)) {
	$nextItem = "&".strtolower($key)."=";
	if (!stristr($MM_removeList, $nextItem)) {
		$MM_keepURL .= "&".$key."=".urlencode($val);
	}
}
#######################################################################


include ("header.php");

?>
<script language="javascript" type="text/javascript"> 
function selectall(){ 
t=document.forms[0].length; 
for(i=0; i<t; (i=(i+2))) {
document.forms[0][i].checked=document.forms[0][1].checked;

} 
} 
</script>  
<h2>Feed Aggregator</h2>

<p class="name">&nbsp;&nbsp;Display:&nbsp;&nbsp;<select name="repeat" onChange="MM_jumpMenu('parent',this,0)" class="name">
                <option selected>Select Feed</option>
				<?php while (!$f->EOF) { ?>
                <option value="feeds_view.php?feed=<?php echo $f->Fields("id"); ?>"><?php echo $f->Fields("title"); ?></option>
				<?php $f->MoveNext(); }?>
				<option value="feeds_view.php">All Feeds</option>      
              </select>&nbsp;&nbsp;&nbsp;<select name="repeat" onChange="MM_jumpMenu('parent',this,0)" class="name">
                <option selected># to Display</option>
                <option value="feeds_view.php?limit=10&<?php echo  $MM_keepURL; ?>">10</option>
                <option value="feeds_view.php?limit=50&<?php echo  $MM_keepURL; ?>">50</option>
                <option value="feeds_view.php?limit=100&<?php echo  $MM_keepURL; ?>">100</option>
                <option value="feeds_view.php?limit=250&<?php echo  $MM_keepURL; ?>">250</option>
                <option value="feeds_view.php?limit=-1&<?php echo  $MM_keepURL; ?>">All</option>
			
              </select></p>
<p class="name">
<?php
//echo $sqlct;
$listct=$dbcon->Execute($sqlct)or die($dbcon->ErrorMsg() );
//$count = $listct->fields[0];
$count = $listct->RecordCount();
$total = ($offset +$limit);
if ($total > $count) {$total = $count ;}
echo "Displaying ".($offset +1)."-".$total." of ".$count."  <b>".$q."</b> <br>";
 $pages = ceil(($count/$limit));
if ($pages > 1) {
$i = 0;
$io =0;
echo "<b>Pages:</b>&nbsp;";
while ($i != $pages) {
echo "<a  href=\"feeds_view.php?offset=";
echo $io. "&";
echo $MM_keepURL ;
echo "\">";
echo ($i +1);

echo "</a> ";
$io = ($io+$limit);
$i++;
}
echo "<br>"; }
$i = ($offset+1);
?>
</p>
 <form  action="<?php echo $PHP_SELF ?>" method="POST">
&nbsp;&nbsp;<input type="submit" name="act" value="Submit" class="name">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Select All</strong> 
        <input type="checkbox"  value="select_all" onClick="selectall();" />
<table align="center" cellpadding="1" cellspacing="1" width="95%">
  <tr class=intitle>
    <th  align="left">Delete</th>
    <th align="left">ID</th>
	<th align="left">Title</th>
    <th  align="left">Date</th>
	<th  align="left">View</th>
  </tr>

  <?php
    while (!$rs->EOF) {
$i++;
  $bgcolor =($i % 2) ? "#D5D5D5" : "#E5E5E5";
        ?>

	<tr bgcolor="<?php echo $bgcolor ?>" class=name>
		<td><input type="checkbox" name="read[<?php echo $rs->Fields("id") ?>]" value="1" <?php if ($rs->Fields("read")) {echo "checked";} ?> ></td>
		<td><?php echo $rs->Fields("id") ?></td>
		<td><b><?php echo $rs->Fields("ftitle") ?>:</b> <?php echo utf8_decode($rs->Fields("title")) ?></td>
		
		<td><?php echo DoTimeStamp($rs->Fields("timestamp"),("n/j/y"))?></td>
		<td><a href="<?php echo $rs->Fields("link") ?>" target="_blank">view</a></td>
	</tr>
	
	<tr bgcolor="<?php echo $bgcolor ?>" class=name>
		<td></td>
		<td colspan="4"><?php echo utf8_decode( preg_replace( "/\\n/", "<br/>", $rs->Fields("content") ) ) ?></td>
	</tr>
	<tr bgcolor="<?php echo $bgcolor ?>" class=name>
	  <td colspan = '5'>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0" class=name>
            <tr class=name>
              <td rowspan="2" valign="middle"><div align="right"></div>                
                <div align="left"><br>
Publish: 
  <input name="publish[<?php echo $rs->Fields("id") ?>]" type="checkbox" value="1">             
    </div></td>
              <td>Section</td>
              <td><select name="type[<?php echo $rs->Fields("id") ?>]" class=name >
<?php echo $obj->select_type_tree(0); ?>              </select></td>
            </tr>
            <tr>
              <td>Class</td>
              <td><select name="fclass[<?php echo $rs->Fields("id") ?>]" class=name>
                <?php
     $class->MoveFirst();
    WHILE (!$class->EOF){
?>
                <OPTION VALUE="<?php echo  $class->Fields("id")?>" ><?php echo  $class->Fields("class");?> </OPTION><?php     $class->MoveNext(); } ?>
              </select></td>
            </tr>
          </table></td>
		
	</tr>
        <?php
	$rs->MoveNext();
}

  ?>

</table>

<?php

include ("footer.php");

?>
