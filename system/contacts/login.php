<?php
     
  
  require_once("../Connections/freedomrising.php");  

?><?php
// *** Start the session
session_start();
// *** Validate request to log in to this site.
$KT_LoginAction = $PHP_SELF;
if ($QUERY_STRING!="") $KT_LoginAction .= "?".$QUERY_STRING;
if (isset($user)) {
  $KT_valUsername=$user;
  $KT_fldUserAuthorization="permission";
  $KT_redirectLoginSuccess="welcome.php";
  $KT_redirectLoginFailed="login.php";
  $KT_rsUser_Source="SELECT user, password ";
  if ($KT_fldUserAuthorization != "") $KT_rsUser_Source .= "," . $KT_fldUserAuthorization;
  $KT_rsUser_Source .= " FROM users WHERE user='" . $KT_valUsername . "' AND password='" . $password . "'";
  $KT_rsUser=$dbcon->Execute($KT_rsUser_Source) or DIE($dbcon->ErrorMsg());
  if (!$KT_rsUser->EOF) {
    // username and password match - this is a valid user
    $KT_Username=$KT_valUsername;
    session_register("KT_Username");
    if ($KT_fldUserAuthorization != "") {
      $KT_UserAuthorization=$KT_rsUser->Fields($KT_fldUserAuthorization);
    } else {
      $KT_UserAuthorization="";
    }
    session_register("KT_UserAuthorization");
    if (isset($accessdenied) && false) {
      $KT_redirectLoginSuccess = $accessdenied;
    }
    $KT_rsUser->Close();
    session_register("KT_login_failed");
	$KT_login_failed = false;
    header ("Location: $KT_redirectLoginSuccess");
    exit;
  }
  $KT_rsUser->Close();
  session_register("KT_login_failed");
  $KT_login_failed = true;
  header ("Location: $KT_redirectLoginFailed");
  exit;
}
?>
<html>
<head>
<title>login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#FFFFFF" text="#000000">
<form name="form1" method="post" action="<?php echo $KT_LoginAction?>">
  <p> 
    <input type="text" name="user">
  </p>
  <p> 
    <input type="password" name="password">
  </p>
  <p> 
    <input type="submit" name="Submit" value="Submit">
  </p>
</form>
</body>
</html>

