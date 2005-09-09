<?

/*
All source code copyright and proprietary Melonfire, 2001. All content, brand names and trademarks copyright and proprietary Melonfire, 2001. All rights reserved. Copyright infringement is a violation of law.

This source code is provided with NO WARRANTY WHATSOEVER. It is meant for illustrative purposes only, and is NOT recommended for use in production environments. 

Read more articles like this one at http://www.melonfire.com/community/columns/trog/ and http://www.melonfire.com/
*/

// menu.class.php v1.0
// methods to obtain node and tree relationships

class Menu {

// set up some variables to hold database parameters
// hostname, user and password
var $hostname;
var $user;
var $pass;
// database and table containing menu data
var $db;
var $table;

	// constructor
	function Menu()
	{
	    $this->set_database_parameters( AMP_DB_HOST, AMP_DB_USER, AMP_DB_PASS, AMP_DB_NAME, "articletype");
    }

	// function: get next level of menu tree
	// returns: array
	function get_children($id, $wq = 1)
	{
		if ($wq == 1) {$query = "SELECT id,  type FROM $this->table WHERE parent = '$id'  and articletype.usenav = 1 ORDER BY type ASC"; }
		else {
		$query = "SELECT distinct articletype.id, articletype.type FROM articletype articles where articles.".$MX_type." =articletype.id and articles.id  is not null and articletype.parent = '$id' and articletype.usenav = 1 ORDER BY type ASC"; 
		}
		$result = $this->query($query);
		$count = 0;
		while ($row = mysql_fetch_array($result))
		{
			$children[$count]["id"] = $row["id"];	
			$children[$count]["type"] = $row["type"];	
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
	global $MX_top;
		// get parent of this node
		$parent = $this->get_parent($id);
		// if not at the root, add to $ancestors[] array
		if($parent != $MX_top)
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
		$query = "SELECT type FROM $this->table WHERE id = '$id' and usenav=1";	
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
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
    }
}

	// function: display complete menu tree (useful when debugging)
	// returns: HTML list
	function print_menu_tree($id = 1,$y=0) 
	{
		$result = $this->get_children($id,1);	
		for ($x=0; $x<sizeof($result); $x++)
		{
			$y++;
			$this->depth($y);
			echo  "<a href=\"section.php?id=". $result[$x]["id"] ."\" >".$result[$x]["type"] . "</a><br>"; 
			$this->print_menu_tree($result[$x]["id"],$y);
			$y--;	
		}
	
	}
		function print_full_menu_tree($id = 1,$y=0) 
	{
	global $MX_type, $MM_rel;
	
		$repeat = 12; //number of times the list repeats
		$result = $this->get_children($id,1);	
		for ($x=0; $x<sizeof($result); $x++)
		{
			$y++;
			$this->depth($y);
			//$this->depth($y);
			echo  "<a href=\"section.php?id=". $result[$x]["id"] ."\" class=\"text\"><strong>".$result[$x]["type"] . "</strong></a><br>"; 
			 $query2 = "SELECT id, title from articles where ".$MX_type." = ".$result[$x]["id"]." and publish=1 and (class=1 or class=6 or class=7 or class=3 or class=10) Order by date desc Limit $repeat"; 
			$result2 = $this->query($query2);
			$count = 1;
			
		while ($row2 = mysql_fetch_array($result2))
		{
		$z = ($y + 1);
		$this->depth($z);
		
		 $maxTextLenght=50;
		 $aspace=" ";
  		$tttext =$row2["title"];
  if(strlen($tttext) > $maxTextLenght ) {
     $tttext = substr(trim($tttext),0,$maxTextLenght); 
     $tttext = substr($tttext,0,strlen($tttext)-strpos(strrev($tttext),$aspace));
    $tttext = $tttext.'...';
  }
		echo "<a href=\"article.php?id=".$row2["id"]."\" class=\"text\">".$tttext."</a><br>";	
			$count++;
			if ($count > $repeat) {
			$this->depth($z);
			if ($MM_rel) {$MM_rel ="r";}
			echo "&nbsp;&nbsp;<a href=\"article.php?list=type".$MM_rel."&nointro=1&type=". $result[$x]["id"] ."\" class=\"text\"><i>More Articles In This Section</i></a><br>";}
		}
		
		
			$this->print_full_menu_tree($result[$x]["id"],$y);
			$y--;	
		}
	
	}
	
		
function select_type_tree($id = 0,$y=0,$selcode) 
	{
		$result = $this->get_children($id);	
		for ($x=0; $x<sizeof($result); $x++)
		{
			$y++;
		echo "<option value =\"".$selcode.$result[$x]["id"]."\">";
		$this->depth($y);
		echo  $result[$x]["type"] . "</option>"; 
			$this->select_type_tree($result[$x]["id"],$y,$selcode);
			$y--;	
		}
		echo "</option>";
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
