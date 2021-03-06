<?

/*
All source code copyright and proprietary Melonfire, 2001. All content, brand names and trademarks copyright and proprietary Melonfire, 2001. All rights reserved. Copyright infringement is a violation of law.

This source code is provided with NO WARRANTY WHATSOEVER. It is meant for illustrative purposes only, and is NOT recommended for use in production environments. 

Read more articles like this one at http://www.melonfire.com/community/columns/trog/ and http://www.melonfire.com/
*/

// menu.class.php v1.0
// methods to obtain node and tree relationships

class SysMenu {

// set up some variables to hold database parameters
// hostname, user and password
var $hostname;
var $user;
var $pass;
var $list;
// database and table containing menu data
var $db;
var $table;

	// constructor
	function SysMenu()
	{
	    $this->set_database_parameters( AMP_DB_HOST, AMP_DB_USER, AMP_DB_PASS, AMP_DB_NAME, "articletype");
	}

	// function: get next level of menu tree
	// returns: array
	function get_children($id, $wq = 1)
	{
		if ($wq == 1) {$query = "SELECT id, usenav, textorder, type FROM $this->table WHERE parent = '$id' ORDER BY textorder, type ASC"; }
		else {
		$query = "SELECT distinct articletype.id, articletype.type FROM articletype left join articles on articles.type =articletype.id where articles.id  is not null and articletype.parent = '$id' ORDER BY type ASC"; 
		}
		$result = $this->query($query);
		$count = 0;
        $children = Array();
		while ($row = mysql_fetch_array($result)) {
			$children[$count]["id"] = $row["id"];	
			$children[$count]["type"] = $row["type"];	
			$children[$count]["usenav"] = $row["usenav"];
			$children[$count]["textorder"] = $row["textorder"];
		//	$children[$count]["link"] = $row["link"];	
			$count++;
		}
		return $children;
	}
	

	// function: return a list of this node's parents
	// by travelling upwards all the way to the root of the tree
	// returns: array
	function get_ancestors($id, $count = 0) 
	{
		// get parent of this node
		$parent = $this->get_parent($id);
		// if not at the root, add to $ancestors[] array
		if($parent)
		{
		$this->ancestors[$count]["id"] = $parent;
		$this->ancestors[$count]["type"] = $this->get_label($parent);
		//$this->ancestors[$count]["link"] = $this->get_link($parent);
		// recurse to get the parent of this parent
		$this->get_ancestors($this->ancestors[$count]["id"], $count+1);
		// all done? at this stage the array contains a list in bottom-up order
		// reverse the array and return
		return array_reverse($this->ancestors);
		}
	}

	// function: is this node at the root of the tree?
	// returns: boolean
	function is_root_node($id)
	{
		if($this->get_parent($id) == 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	// function: get parent
	// returns: node id
	function get_parent($id)
	{
		$query = "SELECT parent FROM $this->table WHERE id = '$id'";	
		$result = $this->query($query);
		$row = mysql_fetch_row($result);
		return $row[0];
	}


	// function: get whether this id is a branch or leaf
	// returns: boolean
	function get_type($id)
	{
		if($this->get_children($id))
		{ 
			return 1; 
		}
		else
		{ 
			return 0; 
		}
	}

	// function: get label for $id
	// returns: string
	function get_label($id)
	{
		$query = "SELECT type FROM $this->table WHERE id = '$id'";	
		$result = $this->query($query);
		$row = mysql_fetch_row($result);
		return $row[0];
	}

	// function: get link for $id
	// returns: string
	function get_link($id)
	{
		$query = "SELECT link FROM $this->table WHERE id = '$id'";	
		$result = $this->query($query);
		$row = mysql_fetch_row($result);
		return $row[0];
	}

	function depth($depth) {
		for ($i=2; $i<= $depth; ++$i) {
			print "&nbsp;&nbsp;&nbsp;&nbsp;";
		}
	}
	

	// function: display complete menu tree (useful when debugging)
	// returns: HTML list
	function print_menu_tree($id = 0,$y=0) 
	{
		$result = $this->get_children($id,1);	
		for ($x=0; $x<sizeof($result); $x++)
		{
			$y++;
			$typeid = $result[$x]["id"];
            /*
			 if (AMP_Authorized( AMP_PERMISSION_SECTION_LIMIT)){
		if ($sectional_per[$typeid])  {
			$this->depth($y);
		echo  "<a href=\"article_list.php?type=". $result[$x]["id"] ."\">".$result[$x]["type"] . "</a><br>"; }}
		else {
            */
		$this->depth($y);
		echo  "<a href=\"article_list.php?type=". $result[$x]["id"] ."\">".$result[$x]["type"] . "</a><br>";
		#}
			$this->print_menu_tree($result[$x]["id"],$y);
			
			$y--;	
		}
	
	}
	

	function print_menu_tree_java($id = 0,$y=0) {
	
		$result = $this->get_children($id,1);	
        $o = "";
		for ($x=0; $x<sizeof($result); $x++) {
			$typeid = $result[$x]["id"];
			$check = $this->get_children($typeid,1);
			$o .=  "['</a><a href=\"type_edit.php?id=".$typeid."\"><img src=\"images/edit.png\" border=\"0\" valign=\"bottom\"></a>&nbsp;<a href=\"article_list.php?type=".$result[$x]["id"]."\"><img src=\"images/spacer.gif\" width=\"7\" border=\"0\"><img src=\"images/view.jpg\" border=\"0\"><img src=\"images/spacer.gif\" width=\"5\" border=\"0\">".addslashes($result[$x]["type"])."</a> ', 'article_list.php?type=".$result[$x]["id"]."'";
			
			if (sizeof($check)>=1) {
				$o .=  ", \n";
			}
			else {
				$o .=  "], \n";
			}
			$o .= $this->print_menu_tree_java($result[$x]["id"],$y);
			if ($x==(sizeof($result) -1)) {
			//echo ",";
			//}
			//else {
				$o .=  "], \n";
			}
		}
		return $o;
	}
	
		function print_menu_tree_per($id = 1,$y=0) 
	{
	global  $secper;
	
		$result = $this->get_children($id,1);	
		for ($x=0; $x<sizeof($result); $x++)
		{
			$y++;
			$typeid= $result[$x]["id"];
			//echo  "<a href=\"article_list.php?type=". $result[$x]["id"] ."\">".$result[$x]["type"] . "</a><br>"; 
			echo  " <option value=\"". $typeid ."\" ";
			if ($secper[$typeid])  { echo " selected ";}
			echo ">";
			$this->depth($y);
			echo $result[$x]["type"] . "</option>";
			$this->print_menu_tree_per($result[$x]["id"],$y);
			$y--;	
		}	
	}
	
		
function select_type_tree($id = 0,$y=0,$selcode) 
	{
		$result = $this->get_children($id);	
		for ($x=0; $x<sizeof($result); $x++)
		{
			$y++;
            $typeid = $result[$x]["id"];
                /*
                    CONSIDER SECTIONAL PERMISSIONS?
            */
            echo "<option value =\"".$selcode.$result[$x]["id"]."\">";
            $this->depth($y);
            echo  $result[$x]["type"] . "</option>";

            $this->select_type_tree($result[$x]["id"],$y,$selcode);
            $y--;	
        }
        echo "</option>";
    }

// used in sectional list page
function section_type_tree_edit($id = 0,$y=0) {	
    $result = $this->get_children($id);	
    for ($x=0; $x<sizeof($result); $x++){
        $y++;
        $typeid = $result[$x]["id"];
        $i++;
        $bgcolor =($i % 2) ? "#D5D5D5" : "#E5E5E5";
        echo "\n		<tr bordercolor=\"#333333\" bgcolor=\"". $bgcolor."\" onMouseover=\"this.bgColor='#CCFFCC'\" onMouseout=\"this.bgColor='". $bgcolor ."'\"> "; 
        echo "\n			<td> <div align='center'><A HREF='type_edit.php?id=".$result[$x]["id"]."'><img src=\"images/edit.png\" alt=\"Edit\" width=\"16\" height=\"16\" border=\"0\"></A></div></td>";
        echo "\n			<td>".$this->depth2($y).$result[$x]["type"]."</td>";
        echo "\n			<td>".$result[$x]["id"]."</td>";
        if ($result[$x]["usenav"]) {$status ='live';} else {$status ='draft';}
        echo "\n			<td>".$status."</td>";
        echo "\n			<td><input name=\"order[".$result[$x]["id"]."]\" type=\"text\" value=\"".$result[$x]["textorder"]."\" size=2></td>";

        echo '<td><div align="right"><A HREF="nav_position.php?type='.$result[$x]["id"].'">Edit</A></div></td>';
        echo '<td><div align="right"><A HREF="nav_position.php?typeid='.$result[$x]["id"].'">Edit</A></div></td>';
        echo "\n		</tr>";
        $this->section_type_tree_edit($result[$x]["id"],$y);
        $y--;	
    }
}

function depth2($depth) {
    $d = "";
    for ($i=2; $i<= $depth; ++$i) {
        $d .= "&nbsp;&nbsp;&nbsp;&nbsp;";
    }
    return $d;
}

function select_type_treedo($id = 0,$y=0,$list) {
    $result = $this->get_children($id);	
    for ($x=0; $x<sizeof($result); $x++) {
        $y++;
        $typeid = $result[$x]["id"];
        $list[$typeid] = $this->depth2($y).$result[$x]["type"];
        $list= $this->select_type_treedo($typeid,$y,$list);
        $y--;	

    }
    return $list;
}




function select_type_tree2($id = 0,$y=0) {	
    $list = array('1'=>'Select Section');
    $result = $this->get_children($id);	

    for ($x=0; $x<sizeof($result); $x++) {
        //$y++;
        $typeid = $result[$x]["id"];
        $list[$typeid] = $result[$x]["type"];
        $list =$this->select_type_treedo($typeid,$y,$list);
        //$y--;
    }
    return $list;
}



// function: add a record to the menu table
function create_node($label, $link, $parent) 
{
    $this->query("INSERT INTO $this->table(type, link, parent) VALUES ('$label', '$link', '$parent')");
}

// function: remove a record from the menu table
function remove_node($id) 
{
    $this->query("DELETE FROM $this->table WHERE id = '$id'");
}

// function: execute query $query
// returns: result identifier
function query($query)
{
    // connect
    $connection = mysql_connect($this->hostname, $this->user, $this->password) or die ("Cannot connect to database");
    // run query
    $ret = mysql_db_query($this->db, $query, $connection) or die ("Error in query: $query");
    // return result identifier
    return $ret;
}

// function: set database parameters
// returns: none 
function set_database_parameters($hostname, $user, $password, $db, $table)
{
    $this->hostname = $hostname;
    $this->user = $user;
    $this->password = $password;
    $this->db = $db;
    $this->table = $table;
}

// end
}

?>
