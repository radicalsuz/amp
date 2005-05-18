<?php
/** $Id: example2.php 923 2003-11-18 17:18:40Z jrust $ */
/**
* A more complex example. We convert a remote HTML file
* into a PDF file. Additionally, we set several options to
* customize the look.
*/
?>
<html>
<head>
  <title>PDF GENERATION</title>
</head>
<body>
  Creating the PDF from remote web page...<br />
<?php
// Require the class
require_once ("AMP/BaseDB.php");
require_once ("HTML_ToPDF/HTML_ToPDF.php");

// Full path to the file to be converted (this time a webpage)
// change this to your own domain
$htmlFile = $Web_url.$_GET['file'].'?modin='.$_GET['modin'].'&uid='.$_GET['uid'].'&id='.$_GET['id'].'&list='.$_GET['list'].'&type='.$_GET['type'];
$defaultDomain = $Web_url;

$pdfFile = AMP_LOCAL_PATH. '/downloads/pdfs/test'..'pdf';
// Remove old one, just to make sure we are making it afresh
@unlink($pdfFile);

$pdf =& new HTML_ToPDF($htmlFile, $defaultDomain, $pdfFile);

// Convert the file
$result = $pdf->convert();

// Check if the result was an error
if (PEAR::isError($result)) {
    die($result->getMessage());
}
else {
    echo "PDF file created successfully: $result";
    header("Location: ".basename($result));
}
?>
</body>
</html> 