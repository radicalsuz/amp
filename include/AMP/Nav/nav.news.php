<?php 
$newsfeed=$dbcon->CacheExecute("Select id, linktext, test, title, shortdesc from articles where class=3 and publish =1 and uselink=1 and fplink=1 Order by date desc Limit 4") or DIE($dbcon->ErrorMsg());
?>
<tr bgcolor="#990033"> 
    <td><img src="<?php echo $Web_url ?>img/spacer.gif" width="7" height="20" border="0"></td>
    <td class="boxheader">News</td>
    <td><img src="<?php echo $Web_url ?>img/spacer.gif" width="7" height="20" border="0"></td>
</tr>
<tr bgcolor="#006699"> 
    <td colspan="3"><img src="<?php echo $Web_url ?>img/spacer.gif" width="7" height="8" border="0"></td>
</tr>
<tr bgcolor="#006699"> 
    <td><img src="<?php echo $Web_url ?>img/spacer.gif" width="7" height="20" border="0"></td>
    
  <td> 
<?php
while (!$newsfeed->EOF)) { 

  	$teaser =$newsfeed->Fields("shortdesc");
   	if (($newsfeed->Fields("shortdesc")) == ($NULL)) {
  		$teaser = $newsfeed->Fields("test");
	}
	$teaser = makesmall($teaser,95);

    if ($newsfeed->Fields("linktext") != NULL) {
  		$title = $newsfeed->Fields("linktext");
	}
  	else { 
		$title = $newsfeed->Fields("title");
	}
		
	echo '<a href="article.php?id='  . $newsfeed->Fields("id") . '" class="newstitlefront">' . $title . '</a><br>';
	echo '<font class="newsbody">' .  $teaser . '<br></font><br>'; 
	$newsfeed->MoveNext();
} 
?>
  </td>
    <td><img src="<?php echo $Web_url ?>img/spacer.gif" width="7" height="8" border="0"></td>
  </tr>

  <tr bgcolor="#006699"> 
  <td><img src="<?php echo $Web_url ?>img/spacer.gif" width="7" height="8" border="0"></td>
    
  <td align="left" class="go"><a href="article.php?list=class&class=3"><img src="<?php echo $Web_url ?>img/more_news.gif" alt="more news" width="80" height="16" border="0"></a> 
  </td>
    <td><img src="<?php echo $Web_url ?>img/spacer.gif" width="7" height="8" border="0"></td>
  </tr>
  <tr bgcolor="#006699"> 
    <td colspan="3"><img src="<?php echo $Web_url ?>img/spacer.gif" width="7" height="8" border="0"></td>
  </tr>