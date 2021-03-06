<?php 

require("Connections/freedomrising.php");
#$modid="30";
$mod_name = "nav";

if ($_POST['moduleid']) {
	$field= 'moduleid';
	$field_value= $_POST['moduleid'];
	$redirect = "introtext.php?id=".$_POST['moduleid'];
	//Default content modules should just return to nav_order page
	if ($field_value==1||$field_value==2) $redirect=$_SERVER['PHP_SELF'].'?mod_id='.$field_value;
}	
if ($_POST['typelist']){
	$field= 'typelist';
	$field_value= $_POST['typelist'];
	$redirect = "edittypes.php";
}	
if ($_POST['typeid']){
	$field = 'typeid';
	$field_value = $_POST['typeid'];
	$redirect = "edittypes.php";
}	
if ($_POST['classlist']) {
	$field = 'classlist';
	$field_value = $_POST['classlist'];
	$redirect = "edittypes.php";
}	


// insert, update, delete
if ($_POST['MM_update']) {
	$sql = "delete from nav where $field = $field_value";
	$dbcon->Execute($sql) or DIE($sql.$dbcon->ErrorMsg());
	//echo $sql.'<br>';
	foreach ($_POST['valid_nav'] as $k=>$v){	
		$sql ="insert into nav (navid,position,$field) values('".$_POST['navid'][$k]."','".$_POST['position'][$k]."','".$field_value."');"; 
		$dbcon->Execute($sql) or DIE($sql.$dbcon->ErrorMsg());
		//echo $sql.'<br>';
	}
	ampredirect($redirect);
}


if ($_GET['mod_id']) {
	$where = 'where moduleid= '.$_GET['mod_id'];
}	
if ($_GET['type']){
	$where ='where typelist= '.$_GET['type'];
}	
if ($_GET['typeid']){
	$where ='where typeid= '.$_GET['typeid'];
}	
if ($_GET['class']) {
	$where ='where classlist= '.$_GET['class'];
}	

$sql="SELECT * FROM nav $where  order by position asc";
$R=$dbcon->Execute($sql) or DIE('could not load navigation items'.$sql.$dbcon->ErrorMsg());
$N=$dbcon->Execute("SELECT modules.name as modname, navtbl.name, navtbl.id FROM navtbl, modules where modules.id= navtbl.modid order by modules.name asc, navtbl.name asc") or DIE($dbcon->ErrorMsg());	

include ("header.php") ; ?>
	<h2>Navigation Files</h2>
	<script type="text/javascript">

	var SearchLines=new Array(); //Holds pointers to search criteria form elements
	var searchitems=<?php if ($R->RecordCount() != 0) {echo $R->RecordCount();} else {echo "1";} ?>;  //the number of search lines being displayed on the form
	var sform=document.forms['Nav_Form'];
	var searchtable=document.getElementById('nav_table');
		
	
//This is A Javascript Function
//to create a new row of search criteria
	function AddItem() { 
		sform=document.forms['Nav_Form'];
		searchtable=document.getElementById('nav_table');

		searchitems++; 
		
		var newnavidbox=SetupSelect('navid');
		var newpositionbox=SetupSelect('position');
		var newaddbtn=document.createElement('input');
		var newhandle=document.createElement('img');
		newhandle.setAttribute('src', 'images/hand.gif');
		newhandle.setAttribute('align', 'left');
		newaddbtn.type='button';
		newaddbtn.value='+';
		event(newaddbtn, 'onclick', 'AddItem();');
		var newrmvbtn=document.createElement('input');
		newrmvbtn.type='button';
		newrmvbtn.value='-';
		event(newrmvbtn, 'onclick', ('RemoveItem('+searchitems+');'));
		var newrow=searchtable.tBodies[0].appendChild(document.createElement('tr'));
		var newcell=document.createElement('td');
		newcell.appendChild(newhandle);
		newcell.appendChild(newnavidbox);
		newrow.appendChild(newcell);
		newcell=document.createElement('td');
		newcell.appendChild(newpositionbox);
		newrow.appendChild(newcell);
		newcell=document.createElement('td');
		newcell.appendChild(newaddbtn);
		newrow.appendChild(newcell);
		newcell=document.createElement('td');
		newcell.appendChild(newrmvbtn);
		newrow.appendChild(newcell);
		
		SearchLines[searchitems]=new Array();
		SearchLines[searchitems][0]=newnavidbox;
		SearchLines[searchitems][1]=newpositionbox;
		

		
		
	} 

	function SaveRow (rowindex) { //This is A Javascript Function
		//won't work in IE for rows generated by script
		//used to commit the first row, which serves as a template
		sform=document.forms['Nav_Form'];
		SearchLines[rowindex]=new Array();
		SearchLines[rowindex][0]=sform.elements['navid['+rowindex+']'];
		SearchLines[rowindex][1]=sform.elements['position['+rowindex+']'];
		
	}
    function ValidateItems () { //Javascript Function
        //sets an indicator flag for which values should be kept
		searchtable=document.getElementById('nav_table');
		sform=document.forms['Nav_Form'];
        var newrow=searchtable.tBodies[0].appendChild(document.createElement('tr'));
        for (n=1; n<=searchitems; n++) {
            var newflag=document.createElement('input');
            newflag.type='hidden';
            newflag.name='valid_nav['+n+']';
            newflag.value=1;
            newrow.appendChild(newflag);
        }
    }

	function RemoveItem(which) { //This is A Javascript Function
		searchtable=document.getElementById('nav_table');
		if (searchitems>=1) {
			for (n=which; n<searchitems; n++) {
				MoveRow(n+1, n);
			}
			
			searchitems=searchitems-1;
			searchtable.deleteRow(searchitems);
			
		} else {
			sform.elements['navid[1]'].selectedIndex=0;
			sform.elements['position[1]'].selectedIndex=0;
			sform.elements['navid[1]'].focus;
		}
	} 

//This is A Javascript Function
//Which Moves values *from* one set of select boxes *to* another

	function MoveRow (from,to) {
		//if (to==1) { //First field not stored in array
		//	sform.elements['navid1'].selectedIndex=SearchLines[from][0].selectedIndex;
		//	sform.elements['position1'].selectedIndex=SearchLines[from][1].selectedIndex;

		//} else { //retrieve values using array data
			SearchLines[to][0].selectedIndex=SearchLines[from][0].selectedIndex;
			SearchLines[to][1].selectedIndex=SearchLines[from][1].selectedIndex;
		//}

	}	

	//This is A Javascript Function
	//To Ensure IE Compliance for assigning onClick action
	function event(elem,handler,funct) {//This is A Javascript Function

		if(document.all) {
			elem[handler] = new Function(funct);
		} else {
			elem.setAttribute(handler,funct);
		}
	}

//This is A Javascript Function
//To Create a Selectbox by copying the options from an Existing Select

	function SetupSelect(selecttype) {
		var newselect=document.createElement('select');
		var selbox = SearchLines[1][convertSelectType(selecttype)];
		newselect.name=selecttype+'['+searchitems+']';
		for (n=0; n<selbox.options.length; n++) {
			newselect.options[n] = new Option(selbox.options[n].text, selbox.options[n].value);
		}
		
		return(newselect);
	}

	//this is a javascript function
	//for putting select boxes into an array
	function convertSelectType(selecttype) {
		var numindex;
		switch (selecttype) {
			case('navid'):numindex=0; break;
			case('position'): numindex=1;break;
		}
		return numindex;
	}
	</script>	


	<form name="Nav_Form" onSubmit="ValidateItems();" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<table id="nav_table"><div>

<?php  

$x=1;
while (!$R->EOF) {

 ?>

	<tr>
		<td>
			<img src="images/hand.gif" align="left"><select name ='navid[<?php echo $x ;?>]'>
				<?php while (!$N->EOF) { ?>
				<option value="<?php echo $N->Fields("id"); ?>" <?php if ($N->Fields("id")==$R->Fields("navid")) echo "SELECTED";?>><?php echo $N->Fields("modname").": ".$N->Fields("name"); ?></option>
				<?php
	$N->MoveNext();
}
$N->MoveFirst()
?>
			</select>
		</td>
		<td>
			<select name="position[<?php echo $x ;?>]">
                  <option value="L1" <?php if ($R->Fields("position")== "L1") echo "SELECTED";?>>Left Side, Position 1</option>
                  <option value="L2" <?php if ($R->Fields("position")== "L2") echo "SELECTED";?>>Left Side, Position 2</option>
                  <option value="L3" <?php if ($R->Fields("position")== "L3") echo "SELECTED";?>>Left Side, Position 3</option>
                  <option value="L4" <?php if ($R->Fields("position")== "L4") echo "SELECTED";?>>Left Side, Position 4</option>
                  <option value="L5" <?php if ($R->Fields("position")== "L5") echo "SELECTED";?>>Left Side, Position 5</option>
                  <option value="L6" <?php if ($R->Fields("position")== "L6") echo "SELECTED";?>>Left Side, Position 6</option>
                  <option value="L7" <?php if ($R->Fields("position")== "L7") echo "SELECTED";?>>Left Side, Position 7</option>
                  <option value="L8" <?php if ($R->Fields("position")== "L8") echo "SELECTED";?>>Left Side, Position 8</option>
                  <option value="L9" <?php if ($R->Fields("position")== "L9") echo "SELECTED";?>>Left Side, Position 9</option>
                  <option value="R1" <?php if ($R->Fields("position")== "R1") echo "SELECTED";?>>Right Side, Position 1</option>
                  <option value="R2" <?php if ($R->Fields("position")== "R2") echo "SELECTED";?>>Right Side, Position 2</option>
                  <option value="R3" <?php if ($R->Fields("position")== "R3") echo "SELECTED";?>>Right Side, Position 3</option>
                  <option value="R4" <?php if ($R->Fields("position")== "R4") echo "SELECTED";?>>Right Side, Position 4</option>
                  <option value="R5" <?php if ($R->Fields("position")== "R5") echo "SELECTED";?>>Right Side, Position 5</option>
                  <option value="R6" <?php if ($R->Fields("position")== "R6") echo "SELECTED";?>>Right Side, Position 6</option>
                  <option value="R7" <?php if ($R->Fields("position")== "R7") echo "SELECTED";?>>Right Side, Position 7</option>
                  <option value="R8" <?php if ($R->Fields("position")== "R8") echo "SELECTED";?>>Right Side, Position 8</option>
                  <option value="R9" <?php if ($R->Fields("position")== "R9") echo "SELECTED";?>>Right Side, Position 9</option>
			</select>
</td><td>	
		<input name='add_criteria<?php echo $x ;?>' type='button' value='+'  onclick='AddItem();'>
</td><td>
		<input name='remove_criteria<?php echo $x ;?>' type='button' value='-'  onclick='RemoveItem(<?php echo $x ;?>);'>
</td></tr>

<?php
	$x++;
	$R->MoveNext();
}
if ($R->RecordCount() == 0) {
?>

	<tr>
		<td>
			<img src="images/hand.gif" align="left"><select name ='navid[<?php echo $x ;?>]'>
				<?php while (!$N->EOF) { ?>
				<option value="<?php echo $N->Fields("id"); ?>"><?php echo $N->Fields("name"); ?></option>
				<?php
	$N->MoveNext();
}
$N->MoveFirst()
?>
			</select>
		</td>
		<td>
			<select name="position[<?php echo $x ;?>]">
                  <option value="L1" >Left Side, Position 1</option>
                  <option value="L2" >Left Side, Position 2</option>
                  <option value="L3" >Left Side, Position 3</option>
                  <option value="L4" >Left Side, Position 4</option>
                  <option value="L5" >Left Side, Position 5</option>
                  <option value="L6" >Left Side, Position 6</option>
                  <option value="L7" >Left Side, Position 7</option>
                  <option value="L8" >Left Side, Position 8</option>
                  <option value="L9" >Left Side, Position 9</option>
                  <option value="R1" >Right Side, Position 1</option>
                  <option value="R2" >Right Side, Position 2</option>
                  <option value="R3" >Right Side, Position 3</option>
                  <option value="R4" >Right Side, Position 4</option>
                  <option value="R5" >Right Side, Position 5</option>
                  <option value="R6" >Right Side, Position 6</option>
                  <option value="R7" >Right Side, Position 7</option>
                  <option value="R8" >Right Side, Position 8</option>
                  <option value="R9" >Right Side, Position 9</option>
			</select>
</td><td>	
		<input name='add_criteria<?php echo $x ;?>' type='button' value='+'  onclick='AddItem();'>
</td><td>
		<input name='remove_criteria<?php echo $x ;?>' type='button' value='-'  onclick='RemoveItem(<?php echo $x ;?>);'>
</td></tr>


<?php  } ?>
</div><BR>
<?php
for ($i = 1; $i <= $x; $i++) {
	echo '<script type="text/javascript">SaveRow('.$i.');</script>';
}
?>

</table>
<input name="moduleid" type="hidden" value="<?php echo $_GET['mod_id']; ?>">
<input name="classlist" type="hidden" value="<?php echo $_GET['class']; ?>">
<input name="typelist" type="hidden" value="<?php echo $_GET['type']; ?>">
<input name="typeid" type="hidden" value="<?php echo $_GET['typeid']; ?>">
<input type="submit" name="MM_update" value="Save Changes"> 
</form>

<?php include ("footer.php") ; ?>
