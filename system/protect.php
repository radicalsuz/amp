<?php
// Instant Protect by Eugene Zossimov: rossos@avacom.net
// Thank you for using Instant Protect
//
// USAGE:
// CHMOD desired directory to 777
// Upload this file to the directory you just "CHMODED"...
// Call the script: http://www.yourdomain.com/your_yet_unprotected_directory/protect.php
// After protecting your directory, CHMOD it to 755 if you want...
 require("Connections/freedomrising.php"); 


 $MM_editAction = $PHP_SELF;
if ($submit2 == 1)
{

$username = $user;
$password = $pass;
$authname = $authname;
$dir = $base_path.$dir1;
$file1 = $dir."/.htpasswd";
$file2 = $dir."/.htaccess";
$file = $dir."/index.php";
$md1945 = crypt($password);
$htpasswd = "$username:$md1945";
$htaccess = "AuthType Basic\r\n";
$htaccess .= "AuthUserFile $dir/.htpasswd\r\n";
$htaccess .= "AuthGroupFile /dev/null\r\n";
$htaccess .= "AuthName \"".$authname."\"\r\n";
$htaccess .= "Require valid-user\r\n";
$redirect ="<?php header (\"Location: $Web_url$location\"); ?>";
echo $dir."<br>";
echo $file1."<br>";
echo $file2."<br>";
mkdir ($dir,0775) or die("Couldn't create dir");
$fp3 = fopen("$file", "w") or die("Couldn't open redirect for writing!");
$numBytes = fwrite($fp3, $redirect) or die("Couldn't create file!");
fclose($fp3);
if ($protect == 1){
$fp = fopen("$file1", "w") or die("Couldn't open HTPASSWD for writing!");
$numBytes = fwrite($fp, $htpasswd) or die("Couldn't create file!");
fclose($fp);
$fp2 = fopen($file2, "w") or die("Couldn't open HTACCESS for writing!");
$numBytes2 = fwrite($fp2, $htaccess) or die("Couldn't create file!");
fclose($fp2); 
}
//chmod ($dir,0755); 
?>
<div align="center">
  <p>&nbsp;</p>
  <p><font color="#FF0000" size="3" face="Arial, Helvetica, sans-serif"><strong>Directory Created</strong></font></p>
</div>

<?php
}

?>

<?php include ("header.php");?>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_findObj(n, d) {
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function instantprotectval() {
  var args = instantprotectval.arguments; var myDot=true; var myV=''; var myErr='';var addErr=false;var myReq;
  for (var i=1; i<args.length;i=i+4){
    if (args[i+1].charAt(0)=='#'){myReq=true; args[i+1]=args[i+1].substring(1);}else{myReq=false}
    var myObj = MM_findObj(args[i].replace(/\[\d+\]/ig,""));
    myV=myObj.value;
    if (myObj.type=='text'||myObj.type=='password'||myObj.type=='hidden'){
      if (myReq&&myObj.value.length==0){addErr=true}
      if ((myV.length>0)&&(args[i+2]==1)){ //fromto
        var myMa=args[i+1].split('_');if(isNaN(parseInt(myV))||myV<myMa[0]/1||myV > myMa[1]/1){addErr=true}
      } else if ((myV.length>0)&&(args[i+2]==2)){
          var rx=new RegExp("^[\\w\.=-]+@[\\w\\.-]+\\.[a-z]{2,4}$");if(!rx.test(myV))addErr=true;
      } else if ((myV.length>0)&&(args[i+2]==3)){ // date
        var myMa=args[i+1].split("#"); var myAt=myV.match(myMa[0]);
        if(myAt){
          var myD=(myAt[myMa[1]])?myAt[myMa[1]]:1; var myM=myAt[myMa[2]]-1; var myY=myAt[myMa[3]];
          var myDate=new Date(myY,myM,myD);
          if(myDate.getFullYear()!=myY||myDate.getDate()!=myD||myDate.getMonth()!=myM){addErr=true};
        }else{addErr=true}
      } else if ((myV.length>0)&&(args[i+2]==4)){ // time
        var myMa=args[i+1].split("#"); var myAt=myV.match(myMa[0]);if(!myAt){addErr=true}
      } else if (myV.length>0&&args[i+2]==5){ // check this 2
            var myObj1 = MM_findObj(args[i+1].replace(/\[\d+\]/ig,""));
            if(myObj1.length)myObj1=myObj1[args[i+1].replace(/(.*\[)|(\].*)/ig,"")];
            if(!myObj1.checked){addErr=true}
      } else if (myV.length>0&&args[i+2]==6){ // the same
            var myObj1 = MM_findObj(args[i+1]);
            if(myV!=myObj1.value){addErr=true}
      }
    } else
    if (!myObj.type&&myObj.length>0&&myObj[0].type=='radio'){
          var myTest = args[i].match(/(.*)\[(\d+)\].*/i);
          var myObj1=(myObj.length>1)?myObj[myTest[2]]:myObj;
      if (args[i+2]==1&&myObj1&&myObj1.checked&&MM_findObj(args[i+1]).value.length/1==0){addErr=true}
      if (args[i+2]==2){
        var myDot=false;
        for(var j=0;j<myObj.length;j++){myDot=myDot||myObj[j].checked}
        if(!myDot){myErr+='* ' +args[i+3]+'\n'}
      }
    } else if (myObj.type=='checkbox'){
      if(args[i+2]==1&&myObj.checked==false){addErr=true}
      if(args[i+2]==2&&myObj.checked&&MM_findObj(args[i+1]).value.length/1==0){addErr=true}
    } else if (myObj.type=='select-one'||myObj.type=='select-multiple'){
      if(args[i+2]==1&&myObj.selectedIndex/1==0){addErr=true}
    }else if (myObj.type=='textarea'){
      if(myV.length<args[i+1]){addErr=true}
    }
    if (addErr){myErr+='* '+args[i+3]+'\n'; addErr=false}
  }
  if (myErr!=''){alert('The required information is incomplete or contains errors:\t\t\t\t\t\n\n'+myErr)}
  document.MM_returnValue = (myErr=='');
}
//-->
</script>

<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><form name="form1" ACTION="<?php echo $MM_editAction ?>" METHOD="POST">
                    <h2 align="left" class="banner">Directory Redirection</h2>
                    <table border="0" align="center" cellpadding="2" cellspacing="0">
                      <tr> 
                        <td width="101">Directory Name</td>
                        <td width="99"> <div align="left"> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                            <input name="dir1" type="text" id="dir14" size="25">
                            </font></div></td>
                      </tr>
                      <tr> 
                        <td>Redirection URL</td>
                        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input name="location" type="text" id="location" size="50">
                          </font></td>
                      </tr>
                      <tr> 
                        <td>Protect Directory</td>
                        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input type="checkbox" name="protect" value="1">
                          <input type="hidden" name="submit2" value="1">
                          </font></td>
                      </tr>
                      <tr> 
                        <td>Username:</td>
                        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input name="user" type="text" id="user3" size="25">
                          </font></td>
                      </tr>
                      <tr> 
                        <td>Password:</td>
                        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input name="pass" type="text" id="pass2" size="25">
                          </font></td>
                      </tr>
                      <tr> 
                        <td>Protect Notes:</td>
                        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input name="authname" type="text" id="authname3" size="35">
                          </font></td>
                      </tr>
                      <tr> 
                        <td>&nbsp;</td>
                        <td><input name="submit" type="submit" id="submit4"  value="Submit"></td>
                      </tr>
                    </table>
            </form></td>
        </tr>
        <tr>
          <td><div align="center"></div></td>
        </tr>
      </table></td>
  </tr>
</table>
<div align="center">
  <p>&nbsp;</p>

</div>
<?php include ("footer.php"); ?>