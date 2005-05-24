<?php

Class Gallery {

	var $gals;
	var $photo;
	var $galleryname;
	var $dbcon;
	var $list_img_size = "thumb";
	var $dir = "thumb";
	var $off =0;
	var $limit ;
	var $amount;
	var $moveFirst;
	var $moveNext;
	var $movePrev;
	var $moveLast;
	
	function Gallery($dbcon) {
		$this->dbcon = $dbcon;
	}

	function build_gallery() {
		$this->$gals = $this->dbcon->CacheExecute("SELECT * FROM gallerytype where galleryname != 'none'  order by galleryname asc") or DIE($this->dbcon->ErrorMsg());
				
		if ($_GET["gal"]) {
		// set the current gallery name
			$galn = $gals = $this->dbcon->CacheExecute("SELECT * FROM gallerytype where id = ".$_GET["gal"]) or DIE($this->dbcon->ErrorMsg());
			$this->galleryname = $galn->Fields("galleryname");

		// get the count 	
			$sqlct  = "SELECT  COUNT(DISTINCT id)  from gallery where publish =1 and galleryid = ".$_GET["gal"];
			$listct=$this->dbcon->CacheExecute($sqlct)or DIE("could not get gallerycount".$this->dbcon->ErrorMsg());
			$this->amount = $listct->fields[0];
			if (!$this->limit) {$this->limit = $this->amount;}

		// set up offset varaibales
			if ($_GET["offset"]) {
				$this->off = $_GET['offset'];
			}
			$slimit = " LIMIT ".$this->off.",".$this->limit;

		// do the query		
			$this->photo = $this->dbcon->CacheExecute("SELECT * FROM gallery where  gallery.publish=1 and galleryid = ".$_GET["gal"]." ORDER BY date, id DESC ".$slimit) or DIE($this->dbcon->ErrorMsg());
		
		// get the big list
		} else {
 			$this->photo=$this->dbcon->CacheExecute("SELECT * FROM gallery where publish=1") or DIE($this->dbcon->ErrorMsg());
		}

		$this->set_moves();
	}
	

	function set_moves() {
		//if ( $this->amount > 1) {;
			$MM_removeList = "&offset=";
			reset ($_GET);
			while (list ($key, $val) = each ($_GET)) {
				$nextItem = "&".strtolower($key)."=";
				if (!stristr($MM_removeList, $nextItem)) {
					$MM_keepURL .= "&".$key."=".urlencode($val);
				}
			}
			
			$this->moveFirst=   $_SERVER['PHP_SELF']."?".$MM_keepURL."&offset=0";
//			if ($this->amount <= $this->off) {
				$this->moveNext =  $_SERVER['PHP_SELF']."?".$MM_keepURL."&offset=".($this->off+$this->limit);
		//	}
			$this->movePrev =  $_SERVER['PHP_SELF']."?".$MM_keepURL."&offset=".($this->off-$this->limit);
			$loffset = (floor($this->amount / $this->limit) * $this->limit);
			$this->moveLast =  $_SERVER['PHP_SELF']."?".$MM_keepURL."&offset=".($loffset);	
		//}
	}

//display move links
	function display_moves($first_icon,$last_icon,$css) {
		
		return $html;
	
	}

	function gal_index() {
		while(!$this->gals->EOF) {
			// get the image that will be display in the list
			$galimage = $this->gals->Fields("img");
			if (!$galimage) {
				$gphoto=$this->dbcon->CacheExecute("SELECT img FROM gallery where publish=1 and galleryid = ".$this->gals->Fields("id")." order by RAND()") or DIE($this->dbcon->ErrorMsg());
				$galimage = $gphoto->Fields("img");
			}
		
   			$daimg = AMP_LOCAL_PATH.DIRECTORY_SEPARATOR."img/pic/".$galimage;
			echo '<div class="gallerylist">';
			if (file_exists($daimg) && ($galimage)) { 
				echo '<a href="gallery.php?gal=' 
					. $this->gals->Fields("id") 
					. '"><img src="img/'.$this->list_img_size.'/' 
					. $galimage 
					. '"></a>';         
			} 
        
        	echo '<a href="gallery.php?gal='
        	 . $this->gals->Fields("id")
        	 . '">' 
        	 . $this->gals->Fields("galleryname")
        	 . '</a><p>'
        	 . $this->gals->Fields("description")
        	 . '</p> <br />';
        	 echo '</div>';
   
			$this->gals->MoveNext();
		}
		return $html;
	}	

		
	function gal_ddmenu() {
		$html = '<br>
		<select onChange="MM_jumpMenu(\'parent\',this,0)" class="name">
					  <option SELECTED value="gallery.php">Select Photo Gallery</option>
					  <option value="gallery.php">-----</option>';
		
		while (!$this->gals->EOF) { 
			$html .=  '<option value="gallery.php?gal='. $this->gals->Fields("id") .'" >'. $this->gals->Fields("galleryname");
			$html .= '</option>';
			$this->gals->MoveNext();
		}
		$html .='</select>';
		if ($this->gals->RecordCount() > 1) {
			return $html;
		}
	}

}


?>
