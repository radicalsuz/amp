<?php
 /*********************
06-03-2003  v3.01
Module: Article
Description:   sectional index page  that displays the nav link as title for general content with press releases and news articles broken out below (written for jpi)
CSS:  go, listtitle, text
calls: list.news.t.php, list.pr.t.php
Called By: list.inc.php (from database var)
GET VARS: 
   					$all = 1 - overrise the pagation var and show all articles
To Do: smaller sql select 
*********************/ 
  
//populate list 
   $list=$dbcon->CacheExecute("SELECT id, linktext,  shortdesc, doc FROM articles  WHERE type=$type and publish=1 and class =1  Order by date desc, linktext asc ")or DIE($dbcon->ErrorMsg());  
 while(!$list->EOF)
   { ?>
 <a href="article.php?id=<? echo $list->Fields("id")?>" class="listtitle" > 
  <?php echo $list->Fields("linktext")  ; ?></a>&nbsp;<?php if ($list->Fields("doc") != NULL) {  ?><a href="downloads/<?php echo $list->Fields("doc")?>"><img src="img/pdf.gif" border="0"></a><?php } ?><span class="text"><?php  
 if ($list->Fields("shortdesc") != NULL ){
 echo "-&nbsp;";
  $maxTextLenght=9000;
  $aspace=" ";
  $ttext =$list->Fields("shortdesc");
  if(strlen($ttext) > $maxTextLenght ) {
     $ttext = substr(trim($ttext),0,$maxTextLenght); 
     $ttext = substr($ttext,0,strlen($ttext)-strpos(strrev($ttext),$aspace));
    $ttext = $ttext.'...';
  }
  echo (converttext($ttext)); }
?>
 <br><br>
<?php
  $list->MoveNext();
}
echo "<br>";
//press releases
  $list=$dbcon->CacheExecute("SELECT id, linktext,  title, shortdesc, doc FROM articles  WHERE type=$type and publish=1 and class =10 Order by date desc, linktext asc ")or DIE($dbcon->ErrorMsg());  
 while(!$list->EOF)
   { ?>
 <a href="article.php?id=<? echo $list->Fields("id")?>" class="listtitle" > 
  <?php if  ( $list->Fields("linktext") ) {echo $list->Fields("linktext")  ; }  else {echo "Press Release";} ?></a>&nbsp;<?php if ($list->Fields("doc") != NULL) {  ?><a href="downloads/<?php echo $list->Fields("doc")?>"><img src="img/pdf.gif" border="0"></a><?php } ?><span class="text"><?php  
 if ($list->Fields("shortdesc") != NULL ){
 echo "-&nbsp;";
  $maxTextLenght=9000;
  $aspace=" ";
  $ttext =$list->Fields("shortdesc");
  if(strlen($ttext) > $maxTextLenght ) {
     $ttext = substr(trim($ttext),0,$maxTextLenght); 
     $ttext = substr($ttext,0,strlen($ttext)-strpos(strrev($ttext),$aspace));
    $ttext = $ttext.'...';
  }
  echo (converttext($ttext)); }
?>
 <br><br>
<?php
  $list->MoveNext();
}

$list=$dbcon->CacheExecute("SELECT id, link, linkover, shortdesc, date, usedate, author, source, source, sourceurl, picuse, picture, title FROM articles  WHERE type=$MM_type and publish=1 and class =3  Order by date desc, linktext asc ")or DIE($dbcon->ErrorMsg());  

 ?>
 
<p class="title">Press Coverage</p>
<?php 


  $maxTextLenght=9000;  //trim text length

while  (!$list->EOF)
   { ?> 
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php if ($list->Fields("picuse") == (1)) { ?>
    <img src ="<?php echo $Web_url.$NAV_IMG_PATH."thumb/".$list->Fields("picture") ?>" align="left" vspace="2" hspace="4" border="1"> <?php }?></td>
    <td width="100%" valign="top">
     <a href="<?php if ($list->Fields("linkover") != (1)) { ?>article.php?id=<? echo $list->Fields("id")?><?php }?>
<?php if ($list->Fields("linkover") == (1)) { ?><?php echo $list->Fields("link")?><?php }?>" class="listtitle"  > 
<?php echo utf8_decode($list->Fields("title"))  //end title ?> </a> <br> <?php if (trim($list->Fields("author")) != (NULL)) { ?><span class="bodygreystrong">by&nbsp;<?php echo converttext($list->Fields("author"))?></span><?php } //end if for author ?><?php if ((trim($list->Fields("author")) != (NULL)) &  ($list->Fields("source") != (NULL))) { echo ?>,&nbsp;<?php } ?><?php if ($list->Fields("source") != (NULL)) { ?><span class="bodygreystrong"><?php if ($list->Fields("sourceurl") != NULL){echo "<a href=\"".$list->Fields("sourceurl")."\">";}
echo $list->Fields("source");
if ($list->Fields("sourceurl") != NULL){echo "</a>";}
?></span> <?php } //end if for author ?>
      <?php if (($list->Fields("author") != (NULL)) or ($list->Fields("source") != (NULL))) { echo ?>
      <br> 
      <?php } ?>
      <?php 	if ($list->Fields("usedate") != (1)) {
 if ($list->Fields("date") != ("0000-00-00")){
  //start date ?>
      <span class="bodygreystrong"> <?php echo DoDate( $list->Fields("date"), 'F j, Y') ;?></span><br>
      <?php }} //end date ?>
     
    </td>
  </tr>
</table>

<br>
<?php
  $list->MoveNext();
}

?>
