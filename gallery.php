<?php
/*********************
01-04-2004  v3.01
Module:  Phot Gallery
Description:  displays images in  the photo gallery
CSS: text, title
VARS: $fullgal, $divz
GET VARS: gal
To Do:  gallery by type
	better sql
	make vars data vars

*********************/ 
//$fullgal =0;
$divz =2;  //numer of rows 

$modid =8;
$mod_id = 27 ;
#include("sysfiles.php");
#include("header.php"); 
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("includes/moduleintro.php");  

if ($_GET["gal"]) {
	$photo = $dbcon->CacheExecute("SELECT gallery.*, gallerytype.galleryname FROM gallery, gallerytype where gallery.galleryid=gallerytype.id and gallery.galleryid = ".$_GET["gal"]." and gallery.publish=1 ORDER BY gallery.date DESC") or DIE($dbcon->ErrorMsg());
} else {
 $photo=$dbcon->CacheExecute("SELECT * FROM gallery where publish=1") or DIE($dbcon->ErrorMsg());
}
 $gallerys = $dbcon->CacheExecute("SELECT * FROM gallerytype where galleryname != 'none'  order by galleryname asc") or DIE($dbcon->ErrorMsg());

$photo_numRows=0;
$photo__totalRows=$photo->RecordCount();
   
$Repeat2__numRows = 1;
$Repeat2__index= 0;
$photo_numRows = $photo_numRows + $Repeat2__numRows;

// *** Recordset Stats, Move To Record, and Go To Record: declare stats variables
  
// set the record count
$photo_total = $photo->RecordCount();
  
// set the number of rows displayed on this page
if ($photo_numRows < 0) {            // if repeat region set to all records

	$photo_numRows = $photo_total;

} elseif ($photo_numRows == 0) {    // if no repeat regions

	$photo_numRows = 1;

}
  
// set the first and last displayed record
$photo_first = 1;
$photo_last  = $photo_first + $photo_numRows - 1;
  
// if we have the correct record count, check the other stats
if ($photo_total != -1) {
	$photo_numRows = min($photo_numRows, $photo_total);
	$photo_first  = min($photo_first, $photo_total);
	$photo_last  = min($photo_last, $photo_total);
}

$MM_paramName = "";

// *** Move To Record and Go To Record: declare variables

$MM_rs		= &$photo;
$MM_rsCount	= $photo_total;
$MM_size	= $photo_numRows;
$MM_uniqueCol	= "";
$MM_paramName	= "";
$MM_offset	= 0;
$MM_atTotal	= false;
$MM_paramIsDefined = ($MM_paramName != "" && isset($$MM_paramName));

// *** Move To Record: handle 'index' or 'offset' parameter

if (!$MM_paramIsDefined && $MM_rsCount != 0) {

	// use index parameter if defined, otherwise use offset parameter
	if(isset($index)) {
		$r = $index;
	} else {
		if(isset($offset)) {
			$r = $offset;
		} else {
			$r = 0;
		}
	}
	$MM_offset = $r;

	// if we have a record count, check if we are past the end of the recordset
	if ($MM_rsCount != -1) {
		if ($MM_offset >= $MM_rsCount || $MM_offset == -1) {  // past end or move last
			if (($MM_rsCount % $MM_size) != 0) {  // last page not a full repeat region
				$MM_offset = $MM_rsCount - ($MM_rsCount % $MM_size);
			} else {
				$MM_offset = $MM_rsCount - $MM_size;
			}
		}
	}

	// move the cursor to the selected record
	for ($i=0;!$MM_rs->EOF && ($i < $MM_offset || $MM_offset == -1); $i++) {
		$MM_rs->MoveNext();
	}

	if ($MM_rs->EOF) $MM_offset = $i;  // set MM_offset to the last possible record
}

// *** Move To Record: if we dont know the record count, check the display range

if ($MM_rsCount == -1) {

	// walk to the end of the display range for this page
	for ($i=$MM_offset; !$MM_rs->EOF && ($MM_size < 0 || $i < $MM_offset + $MM_size); $i++) {
		$MM_rs->MoveNext();
	}

	// if we walked off the end of the recordset, set MM_rsCount and MM_size
	if ($MM_rs->EOF) {
		$MM_rsCount = $i;
		if ($MM_size < 0 || $MM_size > $MM_rsCount) $MM_size = $MM_rsCount;
	}

	// if we walked off the end, set the offset based on page size
	if ($MM_rs->EOF && !$MM_paramIsDefined) {
		if (($MM_rsCount % $MM_size) != 0) {  // last page not a full repeat region
			$MM_offset = $MM_rsCount - ($MM_rsCount % $MM_size);
		} else {
			$MM_offset = $MM_rsCount - $MM_size;
		}
	}

	// reset the cursor to the beginning
	$MM_rs->MoveFirst();

	// move the cursor to the selected record
	for ($i=0; !$MM_rs->EOF && $i < $MM_offset; $i++) {
		$MM_rs->MoveNext();
	}
}

// *** Move To Record: update recordset stats

// set the first and last displayed record
$photo_first = $MM_offset + 1;
$photo_last  = $MM_offset + $MM_size;

if ($MM_rsCount != -1) {
	$photo_first = $photo_first<$MM_rsCount?$photo_first:$MM_rsCount;
	$photo_last  = $photo_last<$MM_rsCount?$photo_last:$MM_rsCount;
}

// set the boolean used by hide region to check if we are on the last record

$MM_atTotal = ($MM_rsCount != -1 && $MM_offset + $MM_size >= $MM_rsCount);

// *** Go To Record and Move To Record: create strings for maintaining URL and Form parameters

// create the list of parameters which should not be maintained

$MM_removeList = "&index=";
if ($MM_paramName != "") $MM_removeList .= "&".strtolower($MM_paramName)."=";
$MM_keepURL="";
$MM_keepForm="";
$MM_keepBoth="";
$MM_keepNone="";

// add the URL parameters to the MM_keepURL string

reset ($HTTP_GET_VARS);
while (list ($key, $val) = each ($HTTP_GET_VARS)) {
	$nextItem = "&".strtolower($key)."=";
	if (!stristr($MM_removeList, $nextItem)) {
		$MM_keepURL .= "&".$key."=".urlencode($val);
	}
}

// add the URL parameters to the MM_keepURL string
if(isset($HTTP_POST_VARS)) {
	reset ($HTTP_POST_VARS);
	while (list ($key, $val) = each ($HTTP_POST_VARS)) {
		$nextItem = "&".strtolower($key)."=";
		if (!stristr($MM_removeList, $nextItem)) {
			$MM_keepForm .= "&".$key."=".urlencode($val);
		}
	}
}

// create the Form + URL string and remove the intial '&' from each of the strings
$MM_keepBoth = $MM_keepURL."&".$MM_keepForm;

if (strlen($MM_keepBoth) > 0) $MM_keepBoth = substr($MM_keepBoth, 1);
if (strlen($MM_keepURL) > 0)  $MM_keepURL = substr($MM_keepURL, 1);
if (strlen($MM_keepForm) > 0) $MM_keepForm = substr($MM_keepForm, 1);

// *** Move To Record: set the strings for the first, last, next, and previous links

$MM_moveFirst="";
$MM_moveLast="";
$MM_moveNext="";
$MM_movePrev="";
$MM_keepMove = $MM_keepBoth;  // keep both Form and URL parameters for moves
$MM_moveParam = "index";

// if the page has a repeated region, remove 'offset' from the maintained parameters
if ($MM_size > 1) {

	$MM_moveParam = "offset";

	if (strlen($MM_keepMove)> 0) {

		$params = explode("&", $MM_keepMove);
		$MM_keepMove = "";

		for ($i=0; $i < sizeof($params); $i++) {

			list($nextItem) = explode("=", $params[$i]);
			if (strtolower($nextItem) != $MM_moveParam) {
				$MM_keepMove.="&".$params[$i];
      			}
		}

		if (strlen($MM_keepMove) > 0) $MM_keepMove = substr($MM_keepMove, 1);
	}

}

// set the strings for the move to links

if (strlen($MM_keepMove) > 0) $MM_keepMove.="&";
$urlStr = $PHP_SELF."?".$MM_keepMove.$MM_moveParam."=";
$MM_moveFirst = $urlStr."0";
$MM_moveLast  = $urlStr."-1";
$MM_moveNext  = $urlStr.($MM_offset + $MM_size);
$MM_movePrev  = $urlStr.(max($MM_offset - $MM_size,0));



if ($gallerys->RecordCount() >= 1) {

?>
<br>
<select onChange="MM_jumpMenu('parent',this,0)" class="name">
              <option SELECTED value="gallery.php">Select Photo Gallery</option>
              <option value="gallery.php">-----</option>

	<?php while (!$gallerys->EOF) { ?>

		<option value="gallery.php?gal=<?= $gallerys->Fields("id")?>" >
			<?= $gallerys->Fields("galleryname"); ?> 
		</option>
	<?php $gallerys->MoveNext();
	}
	$gallerys->MoveFirst();
}
?> </select>
 <table width="100%" border="0" cellspacing="0" cellpadding="10" bordercolor="#000000" align="center">
                    <tr> 
                      <td> 
    <?php 
	if (!$_GET[gal]) {?>
 
	<table width="100%" border="0" cellspacing="0" cellpadding="10">
       		<?php while(!$gallerys->EOF) {
$galimage = $gallerys->Fields("img");
if (!$galimage) {
$gphoto=$dbcon->CacheExecute("SELECT img FROM gallery where publish=1 and galleryid = ".$gallerys->Fields("id")." order by RAND()") or DIE($dbcon->ErrorMsg());
$galimage = $gphoto->Fields("img");
}
?> <tr>

   <td width="25%" align="center" valign="top" class="text">
   <?php $daimg = $base_path_amp."img/pic/".$galimage;
if (file_exists($daimg) && ($galimage)) { ?>
        <a href="gallery.php?gal=<?php echo $gallerys->Fields("id")?>"><img src="img/pic/<?php echo $galimage; ?>" align="top" border="0" class="gallerypic"></a>          <?php } ?> 
            <br> 
             </td>
        
        <td width="75%" align="center" valign="top" class="text"><div align="left"><a href="gallery.php?gal=<?php echo $gallerys->Fields("id")?>"><p class="eventtitle"><?= $gallerys->Fields("galleryname") ?></p></a><?= $gallerys->Fields("description") ?></div></td>
        <?php
		
  $gallerys->MoveNext();
}?>

</tr>
      </table>
	<?php }
	elseif ($photo->Fields("img") == NULL) {	?>
                        <p>&nbsp;</p>
                        <p class="text">There are no photos currently in this gallery.</p>

 <?php } 
 elseif ($fullgal == 1) {
	if (!$dir) { $dir = "thumb"; }	?>

			<p class="title"> 
 <?php if ($_GET["gal"]) {	echo $photo->Fields("galleryname");}
				 else {	echo "Photo Gallery";}	?>
            </p>		
						   
      <table width="100%" border="0" cellspacing="0" cellpadding="10">
        <tr>
<?php
$rowx_count=0;
$crcd2 ="</tr><tr>";
$crcd1 = "";
while (!$photo->EOF) { 
$daimg = $base_path_amp."img/original/".$photo->Fields("img");
if (file_exists($daimg)) {
	$rowx_count++;
?>
   <td valign="top" align="center" class="text">
          <p class="text">  <a href="img/original/<?= $photo->Fields("img")?>"><img src="img/<?= $dir; ?>/<?= $photo->Fields("img") ?>" align="top" border="0"></a> 
          
            <br> <?= $photo->Fields("caption") ?>
             <?php if ($photo->Fields("date") != ("0000-00-00 00:00:00")) { echo "&nbsp;";
              	DoDate( $photo->Fields("date"), 'F jS Y'); 
              }
              if ($photo->Fields("photoby")) { ?>
            <br>by: <?php if ($photo->Fields("byemail")) {?><a href="mailto:<?php echo $photo->Fields("byemail")?>"> <?php } ?>
              <?php echo $photo->Fields("photoby")?> </a><br>
              <?php } ?>  </td>
        
        <?php
		 $roundcr= ($rowx_count % $divz) ? $crcd1 : $crcd2;
		 
		  echo $roundcr;
 }
  $photo->MoveNext();
}?>

</tr>
      </table>
						
<?php } 
####LIST OF PHOTOS #####
 else {
 	if (!$dir) {$dir="pic";}?>
						
 <table width="100%" border="0" cellspacing="0" cellpadding="25">
       <?php while (($Repeat2__numRows-- != 0) && (!$photo->EOF))   { ?>
                          <tr> 
                            <td valign="top"> 
							
							<p class="title">
	<?php if ($_GET["gal"]) {echo $photo->Fields("galleryname");}
						 else{?>Photo Gallery<?php } ?>
						</p>
                              <table border="0" width="120" align="left">
                                <tr> 
                                  <td width="23%" align="center"> 
                                    <?php if ($MM_offset != 0) { ?>
                                    <a href="<?php echo $MM_moveFirst?>"><img src="img/First.gif" width="18" height="13" border=0></a> 
                                    <?php } // end $MM_offset != 0 ?>
                                  </td>
                                  <td width="31%" align="center"> 
                                    <?php if ($MM_offset != 0) { ?>
                                    <a href="<?php echo $MM_movePrev?>"><img src="img/Previous.gif" width="14" height="13" border=0></a> 
                                    <?php } // end $MM_offset != 0 ?>
                                  </td>
                                  <td width="23%" align="center"> 
                                    <?php if (!$MM_atTotal) { ?>
                                    <a href="<?php echo $MM_moveNext?>"><img src="img/Next.gif" width="14" height="13" border=0></a> 
                                    <?php } // end !$MM_atTotal ?>
                                  </td>
                                  <td width="23%" align="center"> 
                                    <?php if (!$MM_atTotal) { ?>
                                    <a href="<?php echo $MM_moveLast?>"><img src="img/Last.gif" width="18" height="13" border=0></a> 
                                    <?php } // end !$MM_atTotal ?>
                                  </td>
                                </tr>
                              </table>
                              <p class="text">&nbsp; </p>
                              <p class="text"> 
                                <?php echo $photo->Fields("caption")?>
                              </p>
                              <p class="text"> 
                                <?php if ($photo->Fields("photoby") != ($null)) { ?>
                                by:  <?php if ($photo->Fields("byemail") != NULL) {?><a href="mailto:<?php echo $photo->Fields("byemail")?>"> <?php } ?>
                                
                             
                                <?php echo $photo->Fields("photoby")?>
                                </a><br><?php }?>
                                <?php if ($photo->Fields("date") != ("0000-00-00 00:00:00")) { ?><?php echo DoDate( $photo->Fields("date"), 'F jS Y') ?><?php }
/* if ($photo->Fields("date") != ("0000-00-00 00:00:00")) */
?><br>Click <a href="img/original/<?php echo $photo->Fields("img")?>">Here</a> For Full Size Image
                              </p>
                              <p> 
                            </td>
                            <td> 
                              <div align="center"><img src="img/<?php echo $dir; ?>/<?php echo $photo->Fields("img")?>" align="top"></div>
                            </td>
                          </tr>
                          <?php
  $Repeat2__index++;
  $photo->MoveNext();
}
?>    </table><?php } ?>
                      </td>
                    </tr>
                  </table>
               

<?php include("footer.php"); ?>
