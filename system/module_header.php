<?php
if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) $url_vars = '?' . $_SERVER['QUERY_STRING'];
header('Location: introtext.php' . $url_vars );
?>
