<?php

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");

$obj = new SysMenu;


  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
    ob_start();
  
$Recordset1__MMColParam = "900000000000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}

   $Recordset1=$dbcon->Execute("SELECT * FROM gallery WHERE id = " . ($Recordset1__MMColParam) . " ") or DIE($dbcon->ErrorMsg());
   
   $Recordset1__totalRows=$Recordset1->RecordCount();
   if (isset($id)) {$typevar=$Recordset1->Fields("subtype");}
else {$typevar=1;}
    $typelab=$dbcon->Execute("SELECT id, type FROM articletype where id = ".$typevar."") or DIE($dbcon->ErrorMsg());
	$timber2=$dbcon->Execute("SELECT id, galleryname FROM gallerytype") or DIE($dbcon->ErrorMsg());
   $timber2_numRows=0;
   $timber2__totalRows=$timber2->RecordCount();
    $getimgset=$dbcon->Execute("SELECT thumb, optw, optl FROM sysvar where id =1") or DIE($dbcon->ErrorMsg());
  
include ("header.php");?>

 <h2><?php echo helpme(""); ?>Image Upload</h2><?php
 
 ###########################################################################################
#                                                                                           #
#     * KONFIGURATION *                                                                     #
 ####                  #####################################################################
#                                                                                           #
### Zielverzeichnisse, ohne abschliessenden Slash                                           #
 $picdir = "".$base_path_amp."img/original";             // Originalgrafiken                                       #
 $thumbdir = "".$base_path_amp."img/thumb";         // Vorschaugrafiken   
 $usedir = "".$base_path_amp."img/pic";                                            #
#                                                                                           #
### Value for the height of the Thumbnails                                                       # width if picture has larger width
 $wwidth=$getimgset->Fields("optw") ; 
 # width if picture has larger height
$lwidth=$getimgset->Fields("optl")  ;
 $thumbwidth=$getimgset->Fields("thumb")  ;                                                                        #
#                                                                                           #
### Extension for the name of the Thumbnails                                                #
#                                                                                           #
 $addition = "";                                                                        #
#                                                                                           #
### Specify Extension                                                         #
 $newext = "jpg";                                                                           #
#                                                                                           #
 #                                                                                         #
  #########################################################################################

        if(!isset($DEFAULTS))
                echo "<html><head><title>Bill's JPEG Uploader & Resizer</title>
                <link rel='STYLESHEET' type='text/css' href='script.css'></head><body link='#006600' alink='#006600' vlink='#FF0000' scroll='auto'>";

        $array = explode (".",$file_name);
        $filename = $array[0];
        $extension = strtolower($array[1]);

        if($file_name == "")
         {
                echo "<br><font class='scriptmainfont'><b>&nbsp;&nbsp;&nbsp;File to Upload:</b></font>";
         }
        else
         {
                if(!(($extension == jpe) or ($extension == jpg) or ($extension == jpeg)))
                 {
                        echo"<font class='scripterrorfont'>The attached file is not a jpeg!</font>";
           	 }
                else
                 {
                        if($newname == "")
                         {
                                $smallimage = "$thumbdir"."/"."$filename"."$addition"."."."$newext";
								$useimage = "$usedir"."/"."$filename"."$addition"."."."$newext";
                                $original = "$picdir"."/"."$filename"."."."$newext";
                         }
                        else
                         {
                                $filename = $newname;
                                $smallimage = "$thumbdir"."/"."$newname"."$addition"."."."$newext";
								$useimage = "$usedir"."/"."$newname"."$addition"."."."$newext";
                                $original = "$picdir"."/"."$newname"."."."$newext";
                         }
                        
                        if(file_exists($original))
                         {
                                echo"<font class='scripterrorfont'>&nbsp;A file with this name already exists  on the server !</font>";
                         }
                        else
                         {
                                @copy($file, "$picdir/$filename"."."."$newext");
								
                                echo "<font class='scriptsuccessfont'>&nbsp;The file was transferred to the server !</font>";
                                if(@copy($file,"$thumbdir/$filename$addition"."."."$newext"));
								
                                 if(@copy($file,"$usedir/$filename$addition"."."."$newext"));
								 chmod($smallimage,0755);
								 chmod($useimage,0755);
								 chmod($original,0755);
								           
                                if(file_exists($smallimage))
                                 {
                                        $image = imagecreatefromjpeg("$smallimage");
                                        $ywert=imagesy($image);
                                        $xwert=imagesx($image);

                                        if($xwert > $ywert)
                                         {
                                                $verh = $xwert / $ywert;
                                                $newwidth = $thumbwidth;
                                                $newheight = $newwidth / $verh;
                                         }
                                        else
                                         {
                                                $verh = $ywert / $xwert;
                                                $newwidth = $thumbwidth;
                                                $newheight= $newwidth * $verh;
                                         }

	if ($gd_version >= 2.0) {
            $destimage = ImageCreateTrueColor($newwidth,$newheight);
                         ImageCopyResampled($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$xwert,$ywert); }
				else {
            $destimage = ImageCreate($newwidth,$newheight);
                         ImageCopyResized($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$xwert,$ywert); }
                                        imagejpeg($destimage,$smallimage);
                                 }
								 if(file_exists($useimage))
                                 {
                                        $image = imagecreatefromjpeg("$useimage");
                                        $ywert=imagesy($image);
                                        $xwert=imagesx($image);

                                        if($xwert > $ywert)
                                         {
                                                $verh = $xwert / $ywert;
                                                $newwidth = $wwidth;
                                                $newheight = $newwidth / $verh;
                                         }
                                        else
                                         {
                                                $verh = $ywert / $xwert;
                                                $newwidth = $lwidth;
                                                $newheight= $newwidth * $verh;
                                         }
	if ($gd_version >= 2.0) {
            $destimage = ImageCreateTrueColor($newwidth,$newheight);
                         ImageCopyResampled($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$xwert,$ywert); }
				else {
           $destimage = ImageCreate($newwidth,$newheight);
                         ImageCopyResized($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$xwert,$ywert); }
										
										
										
                                        imagejpeg($destimage,$useimage);
										}
                         }
                 }
         }
              
?>  <?php
    if ( (isset($MM_insert)) ) {
    $MM_editTable  = "gallery";
    $MM_editColumn = "id";
	$imnae2 = "$filename$addition"."."."$newext";
    $MM_fieldsStr = "section|value|imnae2|value|caption|value|photoby|value|date|value|byemail|value|checkbox|value|select2|value";
    $MM_columnsStr = "section|none,none,NULL|img|',none,''|caption|',none,''|photoby|',none,''|date|',none,''|byemail|',none,''|publish|none,1,0|galleryid|none,none,NULL|";
 require ("../Connections/insetstuff.php");
 require ("../Connections/dataactions.php");
   }
?>

      <p><strong>Upload .JPG Image Files Only (<a href="imgother_upload.php">click 
        here</a> for other formats) </strong></p>
      <form method="POST" action="<?php echo $MM_editAction ?>" enctype="multipart/form-data">
 <input type=file name=file size=25><br>
 <input type=text name=newname size=20>

  <font class="scriptmainfont"> New Image Name</font>
  (NO Extension !!!) <br>  <input name="upload" type="submit" value="submit">
  <br>
  <br>        

        <table width="100%" border="0" class="name">
          <tr><td>Add to Gallery</td><td><input name="MM_insert" type="checkbox" ></td></tr><tr> 
            <td width="78" align="right"><div align="left">Gallery</div></td>
            <td width="211"> <select name="select2">
                <option value="1">none</option>
                <?php
  if ($timber2__totalRows > 0){
    $timber2__index=0;
    $timber2->MoveFirst();
    WHILE ($timber2__index < $timber2__totalRows){
?>
                <option value="<?php echo  $timber2->Fields("id")?>"<?php if ($timber2->Fields("id")==$Recordset1->Fields("galleryid")) echo "SELECTED";?>> 
                <?php echo  $timber2->Fields("galleryname");?> </option>
                <?php
      $timber2->MoveNext();
      $timber2__index++;
    }
    $timber2__index=0;  
    $timber2->MoveFirst();
  }
?>
              </select></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Section</div></td>
            <td> <select name="section">
                <OPTION VALUE="<?php echo  $typelab->Fields("id")?>" SELECTED><?php echo  $typelab->Fields("type")?></option>
				 <?php echo $obj->select_type_tree(0); ?>
              </select> </td>
          </tr>
          <tr> 
            <td align="right" width="78"><div align="left">Caption</div></td>
            <td width="211"> <textarea name="caption" cols="40" wrap="VIRTUAL" rows="3"><?php echo $Recordset1->Fields("caption")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td align="right" width="78"><div align="left">Photo By</div></td>
            <td width="211"> <input type="text" name="photoby" size="45" value="<?php echo $Recordset1->Fields("photoby")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" width="78"><div align="left">Date</div></td>
            <td> <input type="text" name="date" value="<?php echo $Recordset1->Fields("date")?>">
              2001-10-22</td>
          </tr>
          <tr> 
            <td align="right" width="78"><div align="left">By Email</div></td>
            <td width="211"> <input type="text" name="byemail" size="45" value="<?php echo $Recordset1->Fields("byemail")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" width="78"><div align="left">Publish</div></td>
            <td width="211"> <input <?php If (($Recordset1->Fields("publish")) == "1") { echo "CHECKED";} ?> type="checkbox" name="checkbox" value="1"> 
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="right"><div align="left"> 
                </div></td>
          </tr>
        </table>
    </form>
<div align="center"> </div>
      <hr class="script">
      <br>

<?php

        if(isset($original))
         {
                echo "<font class='scriptsuccessfont'>Thumbnail</font><br><br>
                <font class='scriptmainfont'>Thumbnail &raquo; <i>".$smallimage."</i><br><img src=\"../img/thumb/".$filename.$addition.".".$newext."\"><br><br>
				Other &raquo; <i>".$useimage."</i><br><img src=\"../img/pic/".$filename.$addition.".".$newext."\"><br><br>
                Original &raquo; <i>".$original."</i></font><br><img src=\"../img/original/".$filename.$addition.".".$newext."\">";
         }

        if(!isset($DEFAULTS))
                echo "";

include ("footer.php");

?>
