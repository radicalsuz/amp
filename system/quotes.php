<?php # quotes page
$modid=41;

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");

$obj = new SysMenu; 

   ?>
 
<?php 
	  
 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {       
    
	$MM_editTable  = "quotes";
	$MM_editColumn = "id";
    $MM_recordId =$MM_recordId ;
	//$date =  DateConvertIn($date);
    $MM_editRedirectUrl = "quotes.php";
    $MM_fieldsStr = "quote|value|source|value|publish|value|date|value|type|value";
    $MM_columnsStr =   "quote|',none,''|source|',none,''|publish|',none,''|date|',none,''|type|',none,''";
	 require ("../Connections/insetstuff.php");
     require ("../Connections/dataactions.php");

   }
  
   ?>
    <?php include ("header.php");?>
   <?php if (!$_GET[action]) { 
      $quotelist=$dbcon->Execute("SELECT * FROM quotes order by publish asc, quote asc") or DIE($dbcon->ErrorMsg());
   ?>
      <h2>Quote List </h2>
	  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr class="intitle">
          <td>id</td>
          <td>quote</td>
          <td>published</td>
          <td>&nbsp;</td>
        </tr>
       <?php while (!$quotelist->EOF)  { ?>
	    <tr>
          <td><?php echo $quotelist->Fields("id") ; ?></td>
          <td><?php echo $quotelist->Fields("quote") ; ?></td>
          <td><?php  if ($quotelist->Fields("publish")) {echo "live"; } ?></td>
          <td><a href="quotes.php?action=edit&id=<?php echo $quotelist->Fields("id") ; ?>">edit</a></td>
        </tr>
		<?php   $quotelist->MoveNext();}  ?>
      </table> 
	  <?php  }
	  if ($_GET[action]) {
	  
	  $type__MMColParam = "90000000000";
if ($_GET[id])  {$type__MMColParam = $_GET[id];}
   $quote=$dbcon->Execute("SELECT * FROM quotes WHERE id = " . ($type__MMColParam) . "") or DIE($dbcon->ErrorMsg());
  if ($_GET[id]){ $typelab=$dbcon->Execute("SELECT id, type FROM articletype where id = ".$quote->FIelds("type")."") or DIE($dbcon->ErrorMsg()); }
	  ?>
      <h2>Add/Edit Quote</h2><form name="form1" method="post" action="<?php echo $PHP_SELF ?>">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td>Publish</td>
            <td><input   type="checkbox" name="publish" value="1" <?php if ($quote->Fields("publish")) {echo"checked";} ?>></td>
          </tr>
          <tr> 
            <td valign="top">Quote</td>
            <td> <textarea name="quote" cols="55" rows="10" wrap="VIRTUAL" id="quote"></textarea> 
            </td>
          </tr>
          <tr> 
            <td>Source</td>
            <td><input name="source" type="text" id="source" size="45"></td>
          </tr>
          <tr> 
            <td>Date</td>
            <td><input name="date" type="text" id="date" size="45"></td>
          </tr>
          <tr> 
            <td>Section</td>
            <td><select name="type">
               <?php  if ($_GET["id"]){?><option value="<?php echo  $typelab->Fields("type")?>" selected><?php echo  $typelab->Fields("typename")?></option>
			   
                <?php } echo $obj->select_type_tree(); ?> </select></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input type="submit" name="<?php if (!$_GET["id"]) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes"> 
              <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
            
              <input type="hidden" name="MM_recordId" value="<?php echo $_GET["id"]; ?>"></td>
          </tr>
        </table>
		      </form>
			  <?php }?>
      <?php include ("footer.php");?>
