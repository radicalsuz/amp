<?php
$mod_name="content";

require("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu;
$buildform = new BuildForm;

function art_publish($ids) {
	global $dbcon;
	if (is_array($ids)) {
		$q1 ="update articletype set  usenav=0";
		$dbcon->execute($q1) or die($dbcon->errorMsg());
		//	for($v=0; $v<$count; $v++) {
		while(list($key,$value)= each($ids)){ 
			$q = "update articletype set  usenav=1 where id=$key";
			$dbcon->execute($q) or die($dbcon->errorMsg());
		}
		$qs = array('msg' => urlencode('Selected items posted as draft.'));
	}
	//send_to($_SERVER['PHP_SELF'], $qs);
}

function art_order($ids) {
	global $dbcon;
	if (is_array($ids)) {
		$q1 ="update articletype set  textorder=0";
		$dbcon->execute($q1) or die($dbcon->errorMsg());
		//	for($v=0; $v<$count; $v++) {
		while(list($key,$value)= each($ids)){ 
			//	echo $value."<br>";
			$q = "update articletype set  textorder= '$value' where id=$key";
			$dbcon->execute($q) or die($dbcon->errorMsg());
		}
		$qs = array('msg' => urlencode('Selected items posted as draft.'));
	}
	//send_to($_SERVER['PHP_SELF'], $qs);
}

# a switch to see what the page should be doing

switch($_POST['act']) {
	case 'Update':
		art_publish($_POST['publish']);
		art_order($_POST['order']);
		break;
}

include ("header.php");?>

<h2>Sections</h2>

<form  action="<?= $PHP_SELF ?>" method="POST">
<div class='list_table'> <table class='list_table'><tr class="intitle">
    
          <td></td>
          <td>Section</td>
          <td>ID</td>
          <td>Status</td>
		  <td >Order</td>
		  <td>Index Navs</td>
		  <td >Content Navs</td>

        </tr>
        <?php $obj->section_type_tree_edit(0);?>
      </table></div>
	  <input type="submit" name="act" value="Update" class="name">
	  </form>
      <br>

<?php
$table = "class";
$listtitle ="Class";
$listsql ="select id, class  from $table  ";
$orderby =" order by  class asc  ";
$fieldsarray=array( 'Class'=>'class','ID'=>'id'
					);
$filename="class.php";

listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort,$extra);

include ("footer.php");
?>
