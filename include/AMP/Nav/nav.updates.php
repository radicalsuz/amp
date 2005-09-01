<?php 
$newsfeed=$dbcon->CacheExecute(900,"SELECT id, linktext, test, title, date from articles where type=29 and publish =1 and uselink=1 and fplink=1 Order by date desc Limit 4") or DIE($dbcon->ErrorMsg());
if ($newsfeed->RecordCount() > 0) {
   ?>
  <tr bgcolor="#990033"> 
    <td><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="20" border="0"></td>
    <td class="boxheader">Updates</td>
    <td><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="20" border="0"></td>
  </tr>
  <tr bgcolor="#006699"> 
    <td colspan="3"><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="8" border="0"></td>
  </tr>
  <tr bgcolor="#006699"> 
    <td><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="20" border="0"></td>
    
  <td class=newsbody> 
    <?php 	while (!$newsfeed->EOF)   { 
    if ($newsfeed->Fields("linktext")) {  $title = $newsfeed->Fields("linktext");}
  else { $title = $newsfeed->Fields("title");}?>
    <a href="article.php?id=<?php echo $newsfeed->Fields("id"); ?>" class="newsbody"><?php echo DoDate($newsfeed->Fields("date"),("n/j/y"))." - ".$title ;?></a>
    <br> <br>
    <?php 
  $newsfeed->MoveNext();
} ?>
  </td>
    <td><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="8" border="0"></td>
  </tr>

  <tr bgcolor="#006699"> 
  <td><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="8" border="0"></td>
    
  <td align="left"><a href="article.php?list=type&type=29" class="newsbody">More Updates</a> 
  </td>
    <td><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="8" border="0"></td>
  </tr>
  <tr bgcolor="#006699"> 
    <td colspan="3"><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="8" border="0"></td>
  </tr>
  <?php 
  }
$newsfeed=$dbcon->CacheExecute(900,"SELECT id, linktext, test, title, date from articles where type=30 and publish =1 and uselink=1 and fplink=1 Order by date desc Limit 4") or DIE($dbcon->ErrorMsg());
if ($newsfeed->RecordCount() > 0) {
   ?>
  <tr bgcolor="#990033"> 
    <td><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="20" border="0"></td>
    
  <td class="boxheader">Talking Points</td>
    <td><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="20" border="0"></td>
  </tr>
  <tr bgcolor="#006699"> 
    <td colspan="3"><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="8" border="0"></td>
  </tr>
  <tr bgcolor="#006699"> 
    <td><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="20" border="0"></td>
    
  <td class=newsbody> 
    <?php 	while (!$newsfeed->EOF)   { 
    if ($newsfeed->Fields("linktext")) {  $title = $newsfeed->Fields("linktext");}
  else { $title = $newsfeed->Fields("title");}?>
    <a href="article.php?id=<?php echo $newsfeed->Fields("id"); ?>" class="newsbody"><?php echo DoDate($newsfeed->Fields("date"),("n/j/y"))." - ".$title ;?></a>
    <br> <br>
    <?php 
  $newsfeed->MoveNext();
} ?>
  </td>
    <td><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="8" border="0"></td>
  </tr>

  <tr bgcolor="#006699"> 
  <td><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="8" border="0"></td>
    
  <td align="left" ><a href="article.php?list=type&type=30" class="newsbody">More Talking Points</a> 
  </td>
    <td><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="8" border="0"></td>
  </tr>
  <tr bgcolor="#006699"> 
    <td colspan="3"><img src="<?php echo $MM_website_name ?>images/s.gif" width="7" height="8" border="0"></td>
  </tr>
  <?php }?>
