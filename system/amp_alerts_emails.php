<h3>AMP Email Alerts</h3>
<form name="form" method="post" action="http://www.radicaldesigns.org/custom/amp_alerts.php" class="name">
<input name="redirect" type="hidden" value="<?php echo $_SERVER["SCRIPT_URI"]."?".$_SERVER["QUERY_STRING"] ?>">
<input name="user_ID" type="hidden" value="<?php echo $ID ?>">
<input name="domain" type="hidden" value="<?php echo $_SERVER["HTTP_HOST"] ?>">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email Addreess:&nbsp;&nbsp;
<input type="text" name="email">
&nbsp;
<input type="submit" name="Submit" value="Submit">
</form>
