<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title><?= $_GLOBAL['SiteName'] ?> Administration</title>
</head>

<body>

<h1><?= $_GLOBAL['SiteName'] ?> Administration</h1>

<?php

if (isset($this->error_message)) {

    print '<p class="loginError">' . $this->error_message . '</p>';

}

?>

<form id="login" method="post" action="<?= $_SYSTEM['PHP_SELF'] ?>" />
    <label for="username">Login:</label> <input type="text" name="username" />
    <label for="password">Password:</label> <input type="text" name="password" />

    <input type="submit" value="Login" id="login" />
</form>

<p>If you are having trouble logging in, please contact the <a href="mailto:<?= $SiteAdmin ?>">site administrator</a>.</p>

</body>

</html>
