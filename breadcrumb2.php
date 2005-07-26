<?php
$avoid_printing_breadcrumb = true;
include( 'breadcrumb.php' );
$breadcrumb->setSeparator( "<b>&nbsp;&#187;&nbsp;</b>");
$breadcrumb->addActions();
$breadcrumb->addTemplate();

print $breadcrumb->execute();

?>
