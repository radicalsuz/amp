<?php require_once("upload_helper.php"); ?>
<html>
<head>
	<title>File Upload</title>
	<script language="javascript" type="text/javascript" src="prototype.js"></script>
	<script language="javascript" type="text/javascript" src="upload.js">></script>
	<link rel="stylesheet" href="upload.css" type="text/css" media="screen" title="Upload" charset="utf-8" />
</head>
<body>
	<h1>Asynchronous File Upload Demo</h1>
	<form name="postform" action="receive.php" method="post">
		<div class="inputhead">Title</div>
		<input class="input" type="text" name="title" /><br/>
		<div class="inputhead">Body</div>
		<textarea class="input" name="body"></textarea><br/>
		<?php echo upload_value('file_1');?>
	</form>
	<?php echo upload_form('file_1','File 1');?>
	<input type="button" onclick="submitUpload(document.postform);" value="Submit">
</body>
</html>