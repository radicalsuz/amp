<?php
require("Connections/freedomrising.php");
if (!$_GET[file]) {$file="introduction";}
$called=$dbcon->Execute("SELECT * FROM help WHERE file1 = '".$file."' order by sorder asc") or DIE($dbcon->ErrorMsg());
$files=$dbcon->Execute("SELECT distinct file1, title FROM help where type = '' or type is NULL order by title asc") or DIE($dbcon->ErrorMsg());
$ovfiles=$dbcon->Execute("SELECT distinct file1, title FROM help where type = 'overview' order by sorder asc") or DIE($dbcon->ErrorMsg());
$tutfiles=$dbcon->Execute("SELECT distinct file1, title FROM help where type = 'tutorial' order by  sorder asc") or DIE($dbcon->ErrorMsg());
?>
<html><title>Help - Activist CMS</title>
<link href="managment.css" rel="stylesheet" type="text/css">
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr bgcolor="#666666"> 
    <td colspan="2"><div align="left">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><font color="#FFFFCC" size="5" face="Verdana, Arial, Helvetica, sans-serif"><strong><em>HELP 
              SYSTEM</em></strong></font> </td>
            <td class="toplinks"><div align="right"><a href="javascript:window.close()"><B><font color="#FFFFFF">CLOSE 
                WINDOW</font></B></a></div></td>
          </tr>
        </table>
      </div></td>
  </tr>
  <tr bgcolor="#000000"> 
    <td height="2" colspan=2></td>
  </tr>
  <tr> 
    <td width="120" valign="top">

      <table width="100%" border="0" cellspacing="0" cellpadding="4">
        <tr> 
          <td class="intitle"><div align="center">Overviews</div></td>
        </tr>
        <tr> 
          <td class="side"><p>
              <?php   while ((!$ovfiles->EOF)) { ?>
              <a href="help.php?file=<?php echo $ovfiles->Fields("file1"); ?>" class="side"><?php echo $ovfiles->Fields("title"); ?></a><br>
              <?php  $ovfiles->MoveNext(); }?>
              <br>
          </td>
        </tr>
		<tr> 
          <td class="intitle"><div align="center">Tutorials</div></td>
        </tr>
		<tr> 
          <td class="side"><p>              <?php   while ((!$tutfiles->EOF)) { ?>
              <a href="help.php?file=<?php echo $tutfiles->Fields("file1"); ?>" class="side"><?php echo $tutfiles->Fields("title"); ?></a><br>
              <?php  $tutfiles->MoveNext(); }?>
              <br>

          </td>
        </tr>
        <tr> 
          <td class="intitle"><div align="center"><strong>Files</strong></div></td>
        </tr>
        <tr>
          <td> 
            <?php   while ((!$files->EOF)) { ?>
            <a href="help.php?file=<?php echo $files->Fields("file1"); ?>" class="side"><?php echo $files->Fields("title"); ?></a><br> 
            <?php  $files->MoveNext(); }?>
          </td>
        </tr>
      </table><img src="images/spacer.gif" width="120" height="1">
    </td>
    <td width="100%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="name">
		  <h2><a href="http://www.radicaldesigns.org/help/help.php?file=<?php echo $file;?>" target="_blank"><img src="images/global.gif" border="0" align="right"></a><?php echo $called->Fields("title"); ?></h2>
		   <table width="100%" border="0" cellspacing="0" cellpadding="8">
  <tr>
    <td class="text2">
 <?php   while (!$called->EOF)
		      
		    { ?>
            <p class="intitle"><a name="<?php echo $called->Fields("section"); ?>"></a><?php echo $called->Fields("section"); ?></p>
		          <?php echo converttext($called->Fields("html")); ?><br>
                  <br>
<a href="javascript:void(0)"
ONCLICK="open('help_notes.php?id=<?php echo $called->Fields("id"); ?>&add=1','miniwin2','scrollbars=1,resizable=1,width=650,height=500')"> 
            <img src="images/notes.gif" width="20" height="20" border="0" align="absmiddle">Add 
            User Notes</a> 
            <?php
		  $notes=$dbcon->Execute("SELECT * FROM helpnotes where helpid = ".$called->Fields("id")." order by id asc") or DIE($dbcon->ErrorMsg()); 
			   while ((!$notes->EOF)) { 
		  if ($notes->Fields("notes") != NULL) { 
             $users=$dbcon->Execute("SELECT name FROM users where id = ".$notes->Fields("user")."") or DIE($dbcon->ErrorMsg()); ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr> 
                <td bgcolor="#FFFFCC" class="name"><strong>Notes</strong></td>
                <td bgcolor="#FFFFCC" class="name"><div align="right">posted by 
                    <?php echo $users->Fields("name") ?> on <?php echo DoTimeStamp($notes->Fields("date"), ("n/j/y") );?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0)"
ONCLICK="open('help_notes.php?id=<?php echo $called->Fields("id"); ?>&update=<?php echo $notes->Fields("id"); ?>','miniwin2','scrollbars=1,resizable=1,width=650,height=500')">Edit</a></div></td>
              </tr>
              <tr> 
                <td colspan="2" bgcolor="#CCCCCC" class="name"><?php echo converttext($notes->Fields("notes")); ?></td>
              </tr>
            </table>
            <?php	}	  ?>
			<?php  $notes->MoveNext(); }?><br>
            <?php  $called->MoveNext(); }?>
			</td>
  </tr>
</table></td>
        </tr>
      </table>
      </td>
  </tr>
</table>

</body></html>