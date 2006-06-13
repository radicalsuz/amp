<?php
require_once("read_settings.php");
$title = $_POST["title"];
$body = $_POST["body"];
$sid = $_POST["file_1"];

if(!empty($sid)) {
	require_once("receive_helper.php");
	$file = receive($sid);
}
?>
<html>
<head>
	<title>File Upload</title>
	<link rel="stylesheet" href="upload.css" type="text/css" media="screen" title="Upload" charset="utf-8" />
</head>
<body>
	<h1>Asynchronous File Upload Demo</h1>
	<div class="inputhead">Title</div>
	<div class="data"><?php echo $title; ?></div>
	<div class="inputhead">Body</div>
	<div class="data"><?php echo $body; ?></div>
	<div class="inputhead">File 1</div>
	<div class="data"><?php echo $file; ?></div>
</body>
</html>