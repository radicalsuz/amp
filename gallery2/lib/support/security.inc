<?php
/*
 * $RCSfile: security.inc,v $
 *
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2005 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

if (php_sapi_name() == 'cli') {
    /* We don't apply this security check to command-line PHP */
    return;
}

class GalleryStub {
    var $_hash;
    function setConfig($key, $value) {
	$this->_hash[$key] = $value;
    }

    function getConfig($key) {
	if (isset($this->_hash[$key])) {
	    return $this->_hash[$key];
	}
	return null;
    }

    function setDebug() { }
    function setDebugLogFile() { }
    function setProfile() { }
}

$gallery = new GalleryStub();

/* Load config.php */
if (file_exists(dirname(__FILE__) . '/../../config.php')) {
    include(dirname(__FILE__) . '/../../config.php');
} else {
    render('missingConfig');
    exit;
}

$setupPassword = $gallery->getConfig('setup.password');
if (empty($setupPassword)) {
    render('missingPassword');
    exit;
}

if (passwordProvided()) {
    if (passwordIsCorrect()) {
	savePasswordCookie();
	redirectBackToSelf();
	exit;
    } else {
	askForPassword();
	exit;
    }
} else if (!isset($_COOKIE['g2Setup'])) {
    askForPassword();
    exit;
} else if ($_COOKIE['g2Setup'] == md5($setupPassword)) {
    unset($gallery);
    return;
} else {
    askForPassword();
    exit;
}

function passwordProvided() {
    return isset($_POST['g2_password']);
}

function passwordIsCorrect() {
    global $gallery;
    return $_POST['g2_password'] == $gallery->getConfig('setup.password');
}

function savePasswordCookie() {
    global $gallery;
    require_once(dirname(__FILE__) . '/../../modules/core/classes/GalleryUrlGenerator.class');

    $urlGenerator = new GalleryUrlGenerator();
    $urlGenerator->init('lib/tools');
    header(sprintf('Set-Cookie: g2Setup=%s',
		   md5($gallery->getConfig('setup.password'))));
}

function askForPassword() {
    global $message, $gallery;

    $message = array();
    if (!empty($_POST['g2_password'])) {
	$message['password'] = $_POST['g2_password'];
    }

    render('passwordForm');
}

function redirectBackToSelf() {
    require_once(dirname(__FILE__) . '/../../modules/core/classes/GalleryUrlGenerator.class');
    $urlGenerator = new GalleryUrlGenerator();
    header('Location: ' . $urlGenerator->getCurrentUrl());
}

function render($renderType) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>Security Check</title>
    <style> <?php readfile(dirname(__FILE__) . '/support.css'); ?> </style>
  </head>
  <body>
     <h1> Security Check </h1>

     <h2>
	You are attempting to access a secure section of this Gallery installation.  You can't
        proceed until you pass the security check.
     </h2>

     <?php if ($renderType == 'missingConfig'): ?>
     <div class="error">
       You must create a config.php file in the Gallery directory before
       you can continue configuring the application.  Use the
       <a href="../../../install">installer</a> to create one.
     </div>
     <?php endif; ?>

     <?php if ($renderType == 'missingPassword'): ?>
     <div class="error">
       You must enter a setup password in your gallery/config.php file in order
       to be able to set up Gallery. If your config.php is empty, you should run
       <a href="../../install">the installer</a> to install your Gallery.
     </div>
     <?php endif; ?>

     <?php if ($renderType == 'passwordForm'): ?>
     <div class="password_form">
       <div class="box">
         <span class="message">
           In order to verify you, we require you to enter your Gallery setup password.  This is
           the password you entered when you installed Gallery.  It can be found in your
           gallery/config.php file like this:
         </span>
         <pre>
           $gallery->setConfig('setup.password', '<b>your password here</b>');
         </pre>
         <form method="post">
           Password:
           <input type="password" name="g2_password"/>
           <script type="text/javascript">document.forms[0]['g2_password'].focus();</script>
           <input type="submit" value="Verify Me"/>
         </form>
       </div>
     </div>
     <?php endif; ?>
  </body>
</html>
<?php
}
?>
