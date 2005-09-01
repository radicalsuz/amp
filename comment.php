<?php
$intro_id = 34;
$modid=23;
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  

if (isset($_POST["MM_insert"])){
	$MM_editTable  = "comments";
	$MM_editRedirectUrl = "article.php?id=".$_POST["articleid"];
	$publish = $defualtpublish;
	$MM_fieldsStr = "author|value|titlex|value|email|value|comment|value|articleid|value|publish|value|date|value";
	$MM_columnsStr = "author|',none,''|title|',none,''|email|',none,''|comment|',none,''|articleid|none,none,NULL|publish|none,1,0|date|',none,now()";
	require ("Connections/insetstuff.php");
    require ("Connections/dataactions.php");
}
?>

<script language="Javascript" type="text/javascript">

var fieldstocheck = new Array();
    fieldnames = new Array();

function checkform() {
  for (i=0;i<fieldstocheck.length;i++) {
    if (eval("document.form.elements['"+fieldstocheck[i]+"'].value") == "") {
      alert("Please enter "+fieldnames[i]);
      eval("document.form.elements['"+fieldstocheck[i]+"'].focus()");
      return false;
    }
  }
  return true;
}
function addFieldToCheck(value,name) {
  fieldstocheck[fieldstocheck.length] = value;
  fieldnames[fieldnames.length] = name;
}

</script>

      <form action="<?php echo $MM_editAction?>" method="POST" name="form"  >
	  
  <table width="100%" align="center" class="form">
    <tr> 
      <td class="form">Author</td>
      <td> <input name="author" type="text" id="author" size="40">  <script language="Javascript" type="text/javascript">addFieldToCheck("author","Author");</script>
      </td> </tr>
    <tr> 
      <td class="form">E-Mail</td>
      <td class="test"> 
        <input name="email" type="text" id="email" size="40"> <script language="Javascript" type="text/javascript">addFieldToCheck("email","E-Mail");</script> </td>
    </tr>
    <tr> 
      <td class="form">Title</td>
      <td><input name="titlex" type="text" id="titlex" size="40"> <script language="Javascript" type="text/javascript">addFieldToCheck("titlex","Title");</script> </td>
    </tr>
    <tr> 
      <td valign="top" class="form">Comment</td>
      <td><textarea name="comment" cols="40" rows="20" wrap="VIRTUAL" id="comment"></textarea><script language="Javascript" type="text/javascript">addFieldToCheck("comment","Comment");</script></td>
    </tr>
    <tr> 
      <td align="center" colspan="2"><br> <input type="hidden" name="MM_insert" value="true"> <input type="hidden" name="articleid" value="<?php echo $_GET["cid"];?>"> 
      <input name="submit" type="submit" value="Submit your Comment"  onClick="return checkform();"></td>
    </tr>
  </table>
        </form>
<?php 
include("AMP/BaseFooter.php"); 
?>
