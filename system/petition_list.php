<?php
$modid=7;
  require("Connections/freedomrising.php");
?><?php
   $petitons=$dbcon->Execute("SELECT title, id, udmid FROM petition ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $petitons_numRows=0;
   $petitons__totalRows=$petitons->RecordCount();
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $petitons_numRows = $petitons_numRows + $Repeat1__numRows;
?><?php include("header.php"); ?>
<h2 align="right"><b> Petitions</b></h2>
      <table width="100%" align="center">
        <tr class="intitle"> 
          <td><b>Title</b></td>
          <td><strong>Petition Text</strong></td>
          <td><strong>Petition Fields</strong></td>
          <td>Petition Signers</td>
        </tr>
        <?php while (($Repeat1__numRows-- != 0) && (!$petitons->EOF)) 
   { 
?>
        <tr bgcolor="#CCCCCC"> 
          <td> <?php echo $petitons->Fields("title")?> </td>
          <td> <div align="right"><A HREF="petition_edit.php?id=<?php echo $petitons->Fields("id") ?>">edit</A></div></td>
          <td> <div align="right"><a href="modfields2.php?id=<?php echo $petitons->Fields("udmid") ?>">edit</a></div></td>
          <td> <div align="right"><a href="moddata_list.php?modin=<?php echo $petitons->Fields("udmid") ?>">signers</a></div></td>
        </tr>
        <?php
  $Repeat1__index++;
  $petitons->MoveNext();
}
?>
      </table>
      <?php include("footer.php"); ?>
<?php
  $petitons->Close();

?>
 