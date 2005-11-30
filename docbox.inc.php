<?php
/*********************
11-21-2002  v3.01
Module:  Content
Description: displays the document download box in article pages
CSS: docbox
To Do: 

*********************/ 
   $docs=$dbcon->CacheExecute("SELECT doc, doctype FROM articles WHERE id = $MM_id") or DIE($dbcon->ErrorMsg());
   $docs_numRows=0;
   $docs__totalRows=$docs->RecordCount();
?><br> <br> 
<table align="center" width="50%" class="docbox">
  <tr> 
    <td  bordercolor="#000000"> 
      <div align="center">
        <?php if ($docs->Fields("doctype") == ("pdf")) { ?>
        <a href="downloads/<?php echo $docs->Fields("doc")?>"><img src="<?php echo $Web_url.$NAV_IMG_PATH ?>pdf.gif" border="0" align="absmiddle"></a> 
        <?php }
/* if ($docs->Fields("doctype") == ("pdf")) */
?><?php if ($docs->Fields("doctype") == ("word")) { ?>
        <a href="downloads/<?php echo $docs->Fields("doc")?>"><img src="<?php echo $Web_url.$NAV_IMG_PATH ?>worddoc.gif" width="20" height="16" border="0" align="absmiddle"></a> 
        <?php }
/* if ($docs->Fields("doctype") == ("word")) */
?><?php if ($docs->Fields("doctype") == ("img")) { ?>
        <a href="downloads/<?php echo $docs->Fields("doc")?>"><img src="<?php echo $Web_url.$NAV_IMG_PATH ?>img.gif" border="0" align="absmiddle"></a> 
        <?php }
/* if ($docs->Fields("doctype") == ("img")) */
?>
        Download as 
        <?php if ($docs->Fields("doctype") == ("word")) { ?>
        Microsoft Word Document
        <?php }
/* if ($docs->Fields("doctype") == ("word")) */
?><?php if ($docs->Fields("doctype") == ("pdf")) { ?>
        PDF 
        <?php }
/* if ($docs->Fields("doctype") == ("pdf")) */
?><?php if ($docs->Fields("doctype") == ("img")) { ?>
        image file
        <?php }
/* if ($docs->Fields("doctype") == ("img")) */

$docs->Close();
?></div>
    </td>
  </tr>
</table>
