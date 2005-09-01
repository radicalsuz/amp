<?php 
 /*********************
06-03-2003  v3.01
Module: Article
Description:   sectional index page  for news room
calls: list.news.php, list.pr.php
Called By: list.inc.php (from database var)
To Do: 
*********************/ 
include ("AMP/List/list.pr.php");
echo "<br>";
include ("AMP/List/list.news.php"); 
?>
