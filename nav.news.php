<?php 

$newsfeed=$dbcon->CacheExecute("Select id, linktext, test, title, shortdesc from articles where class=3 and publish =1 and uselink=1 and fplink=1 Order by date desc Limit 4") or DIE($dbcon->ErrorMsg());
 $newsfeed_numRows=0;
   $newsfeed__totalRows=$newsfeed->RecordCount(); 
   $Repeat2__numRows = -1;
   $Repeat2__index= 0;
   $newsfeed_numRows = $newsfeed_numRows + $Repeat2__numRows;
  

  
   ?>


 
  <tr bgcolor="#990033"> 
    <td><img src="<?php echo $Web_url ?>images/s.gif" width="7" height="20" border="0"></td>
    <td class="boxheader">News</td>
    <td><img src="<?php echo $Web_url ?>images/s.gif" width="7" height="20" border="0"></td>
  </tr>
  <tr bgcolor="#006699"> 
    <td colspan="3"><img src="<?php echo $Web_url ?>images/s.gif" width="7" height="8" border="0"></td>
  </tr>
  <tr bgcolor="#006699"> 
    <td><img src="<?php echo $Web_url ?>images/s.gif" width="7" height="20" border="0"></td>
    
  <td> 
    <?php 	while (($Repeat2__numRows-- != 0) && (!$newsfeed->EOF)) 
   { 
    $maxTextLenght=95;
  $aspace=" ";
  $teaser =$newsfeed->Fields("shortdesc");
   if (($newsfeed->Fields("shortdesc")) == ($NULL)) {
  $teaser = $newsfeed->Fields("test");
  }
  if(strlen($teaser) > $maxTextLenght ) {
     $teaser = substr(trim($teaser),0,$maxTextLenght); 
     $teaser = substr($teaser,0,strlen($teaser)-strpos(strrev($teaser),$aspace));
    $teaser = $teaser.'...';
  }
    if ($newsfeed->Fields("linktext") != NULL) {
  $title = $newsfeed->Fields("linktext");}
  else { $title = $newsfeed->Fields("title");}?>
    <a href="article.php?id=<?php echo $newsfeed->Fields("id"); ?>" class="newstitlefront"><?php echo $title ;?></a><br> 
    <font class="newsbody"><?php echo $teaser;?><br>
  
    </font><br> 
    <?php  $Repeat2__index++;
  $newsfeed->MoveNext();
} ?>
  </td>
    <td><img src="<?php echo $Web_url ?>images/s.gif" width="7" height="8" border="0"></td>
  </tr>

  <tr bgcolor="#006699"> 
  <td><img src="<?php echo $Web_url ?>images/s.gif" width="7" height="8" border="0"></td>
    
  <td align="left" class="go"><a href="article.php?list=class&class=3"><img src="<?php echo $Web_url ?>images/more_news.gif" alt="more news" width="80" height="16" border="0"></a> 
  </td>
    <td><img src="<?php echo $Web_url ?>images/s.gif" width="7" height="8" border="0"></td>
  </tr>
  <tr bgcolor="#006699"> 
    <td colspan="3"><img src="<?php echo $Web_url ?>images/s.gif" width="7" height="8" border="0"></td>
  </tr>

<?php $newsfeed->Close(); ?>
