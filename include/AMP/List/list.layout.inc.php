<?php
/*********************
12-16-2003  v3.01
Module: Article
Description:  layout sectional index page list
CSS: listtitle,  bodygreystrong,  text
SYS VARS: $Web_url, $NAV_IMG_PATH
functions: converttext, DoDate, 
Called By: list.inc.php, list.new.php, list.pr.php, list.pr.t.php, list.news.t.php, list.sub.php, list.region.php
To Do:
*********************/ 
  $maxTextLenght=9000;  //trim text length

while  (!$list->EOF)
   { ?> 
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td ><?php if ($list->Fields("picuse") == (1)) { ?>
    <img src ="<?php echo $Web_url.$NAV_IMG_PATH."thumb/".$list->Fields("picture") ?>"  vspace="2" hspace="4" class=imgpad> <?php }?></td>
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
      <span class="text"> 
      <?php  

  $aspace=" ";
  $ttext =$list->Fields("shortdesc");
  if(strlen($ttext) > $maxTextLenght ) {
     $ttext = substr(trim($ttext),0,$maxTextLenght); 
     $ttext = substr($ttext,0,strlen($ttext)-strpos(strrev($ttext),$aspace));
    $ttext = $ttext.'...';
  }
  if ($list->Fields("shortdesc") != (NULL)){
  echo $ttext;
  echo " <br>";}
?>
      </span></td>
  </tr>
</table>

<br>
<?php
  $list->MoveNext();
}?>