<?php

/*
    Activist Mobilization Platform (AMP)
    Copyright (C) 2000-2005  David Taylor

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    For further information, contact Radical Designs at info@radicaldesigns.org

*/

/*********************
07-09-2003  v3.01
Module:  Index
Description:  Index template page
SYS VARS: $NAV_IMG_PATH, $indexreplace
functions  evalhtml
To Do: 
*********************/ 
if (isset($_GET['filelink'])) header ("Location: " . $_GET['filelink']);

$mod_id = 2 ;
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
ob_start(); 

if (isset($indexreplace) && $indexreplace) {
    require ("$indexreplace");
} else {
    include("AMP/Article/index.inc.php");
}

include("AMP/BaseFooter.php");

?>
