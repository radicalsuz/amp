<?php
$mod_name='content';
require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu;
   
$allclass=$dbcon->Execute("SELECT distinct class.id, class.class FROM class left  join articles on articles.class =class.id where articles.id  is not null ORDER BY class ASC") or DIE($freedomrising->ErrorMsg());
   $allclass_numRows=0;
   $allclass__totalRows=$allclass->RecordCount();
 
    ?>
<?php include ("header.php");?>

<table width="100%" border="0" align="center">
        <tr> 
          <td class="banner">Content</td>
        </tr>
        <tr> 
          <td><div align="left"> 
              <form action="article_list.php?<?php echo $keep ;?>" method="post" name="form2" class="name">
                <strong>Search By </strong><br/>
                <input name="sid" type="text" id="id" value="ID" size="5" class="name">
                <input name="stitle" type="text" id="title" value="Title" size="25" class="name">
                <input name="sauthor" type="text" id="author" value="Author" size="20" class="name">
                <input name="sdate" type="text" value="Date (ex 01-12-02)" size="25" class="name">
                <input name="Search" type="submit" id="Search" value="Search" class="name">
                <br>
                Note: Search is based on the section that you are currently in. 
                To search all content please click &quot;All Content&quot; below 
                before you start your search. You may only search one field at 
                a time. 
              </form>
              <p style="margin-bottom: 1ex;"><a href="article_list.php"><strong><big>View All Content</big></strong></a></p>
            </div></td>
        </tr>
        <tr class="intitle"> 
          <td class="intitle"><div align="left">View by Section</div>
            <div align="left"></div></td>
        </tr>
        <tr> 
          <td valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="0">
              <tr class="name"> 
                <td>
<script language="JavaScript" src="tree/tree.js"></script>
<script language="JavaScript" src="tree/tree_tpl.js"></script>
<script language="JavaScript">
		
var TREE_ITEMS =  [
<?php 
echo preg_replace( "/(.*)\]([^\]])*$/", "\$1\$2", $obj->print_menu_tree_java($MX_top) ) ; 
//echo  $obj->print_menu_tree_java($MX_top)  ; 
?> ] ;  new tree (TREE_ITEMS, tree_tpl); </script>
</td>
              </tr>
            </table></td>
        </tr>
        <td>&nbsp;</td>
        </tr>
        <tr class="intitle"> 
          <td class="intitle">View by Class</td>
        </tr>
        <tr> 
          <td><table width="100%" border="0" cellspacing="2" cellpadding="0">
              <?php while (!$allclass->EOF)
   { 
?>
              <tr class="name"> 
                <td><a href="article_list.php?&class=<?php echo $allclass->Fields("id")?>"><?php echo $allclass->Fields("class")?></a></td>
              </tr>
              <?php
 
  $allclass->MoveNext();
}
?>
            </table></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
      </table>
	  <?php include("footer.php"); ?>
