<?php
  require("Connections/freedomrising.php");
?><?php

$sql="(SELECT css, Concat('articletype, type, id=,', templateid) as db_id FROM articletype WHERE css<>'' ) UNION (SELECT css, Concat('template, name, id=,', id) as db_id FROM template )  ORDER BY css ASC"; 
   $Recordset1=$dbcon->Execute("$sql") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $Recordset1_numRows = $Recordset1_numRows + $Repeat1__numRows;
?>
<?php
	#array($datafiles);
	#$datafiles = $mysql_fetch_array($Recordset1->Fields("css, db_id"));
	$i=0;
	while (!$Recordset1->EOF) {
#		printf("%s //  %s <BR>\r", $Recordset1->Fields("css"), $Recordset1->Fields("db_id"));
		$datafiles[$i]['css']=$Recordset1->Fields("css");
		#$datafiles[$i]['id']=$Recordset1->Fields("db_id");
		$filename_css=explode(",", $datafiles[$i]['css']);
		#$dblocation_css[$i]=explode(",", $datafiles[$i]['dbid']);
		for ($j=0; $j<count($filename_css); $j++) {
			$datafiles[$i]['css']=$filename_css[$j];
			if (strpos($allcss, trim($datafiles[$i]['css']))===FALSE) {
				$allcss.= trim($datafiles[$i]['css']).",";}
			$i++;
			
			/*if ($j>0) {
			
					$datafiles[$i]['id']=$datafiles[$i-1]['id'];
					$dblocation_css[$i]  = $dblocation_css[$i-1];
			}*/
			
		}
	
	$Recordset1->MoveNext();
	#$i++;
	}
	$Recordset1->MoveFirst();
	$allcss=substr($allcss, 0, strlen($allcss)-1);
	$allcss_set=explode(",", $allcss);	

	?>
<?php include("header.php"); ?>
      <table width="98%" border="0" align="center">
        <tr class="banner"> 
          <td colspan="2"><b>CSS Files</b></td>
        </tr>
        <tr> 
          <td><b>Filename</b></td>
          <td><b>Used For</b></td>
          </tr>
        <?php #while (($Repeat1__numRows-- != 0) && (!$Recordset1->EOF)) 
   for ($i=0; $i<count($allcss_set); $i++)
   { 
	$sql = "(SELECT Concat('Section: ', type) as location, css FROM articletype where css like '%".$allcss_set[$i]."%' and css <> '' and !(isnull(css))) UNION (SELECT Concat('Template: ', name) as location, css from template where css like'%".$allcss_set[$i]."%' and css <> '' and !(isnull(css))) ORDER BY location ASC;";
	$locations=$dbcon->Execute($sql) or DIE($dbcon->Errormsg());
	

?>

        <tr bgcolor="#CCCCCC"> 
          <td> <?php 
			#if ($i==1)	 {
				echo "edit: <A HREF=\"css_edit.php?filename=".$allcss_set[$i]."\"> $allcss_set[$i]</A><BR>";?>
		
		</td>  <td> <?php 
		while (!$locations->EOF) {
		echo $locations->Fields("location")."<BR>";
		$locations->MoveNext();
		}?> </td>
         </tr>
        <?php
  
}
?>
      </table>
            <p>
              <?php
  $Recordset1->Close();
?>
            </p>
<?php include("footer.php"); ?>
