$Recordset1=$dbcon->Execute("$sql")or DIE($dbcon->ErrorMsg());
//echo $sql;
   $page_numRows=0;
   $page__totalRows= $Recordset1->RecordCount();
   

   $Repeat2__numRows = $repeat;
   $Repeat2__index= 0;
   $page_numRows = $page_numRows + $Repeat2__numRows;
   $page_total = $Recordset1->RecordCount();
   include ("pagation.php");
