<?php
require_once(dirname(__FILE__) . '/security.inc');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>Gallery Support</title>
    <link rel="stylesheet" type="text/css" href="support.css"/>
    <style>
      p { padding-left: 10px; margin-top: 2px; }
      h3 { margin-bottom: 2px; }
    </style>
  </head>

  <body>
      <H1> Gallery Support </H1>
      <a href="../../"> Back to Gallery </a>

      <h2>
        Here are some diagnostic scripts that can help you troubleshoot
        problems with your Gallery installation
      </h2>

      <h3> <a href="phpinfo.php">PHP Info</a> </h3>
      <p> PHP configuration information </p>
      <h3> <a href="cache.php">Cache Maintenance</a> </h3>
      <p> Delete files from the Gallery data cache </p>
      <h3> <a href="gd.php">GD</a> </h3>
      <p> Information about your GD configuration </p>
  </body>
</html>
