<?php

Class Petition {

	var $dbcon;
	var $petmod;
	var $petition_started;
	var $petition_ends;
	var $limit =25;
	var $pid;
	var $pq;
	
	function Petition(&$dbcon, $pid=NULL,$modin=NULL) {
		$this->dbcon = $dbcon;
		if (isset($pid)) {
			$this->pid=$pid;
			$this->setData();
		} 
		if (isset($modin)) {
			$this->petmod=$modin;
			$this->setData();
		}
	}
	
	function setData() {
		if (isset($this->petmod)) {
			$where = 'udmid = '.$this->petmod;
		} elseif (isset($this->pid)) {
			$where = "id =".$this->pid;
		}
		$sql = "SELECT * FROM petition where $where ";
		$this->pq = $this->dbcon->Execute($sql) or DIE("could not find petition: ".$sql.$this->dbcon->ErrorMsg());
		$this->petmod  =  $this->pq->Fields("udmid");
		$this->pid  =  $this->pq->Fields("id");
		
		if ($this->pq->Fields("datestarted") !="0000-00-00" or $this->pq->Fields("datestarted") != NULL ){
		$P->petition_started = DoDate($this->pq->Fields("datestarted"),"M, j Y");}
		if ($this->pq->Fields("dateended") !="0000-00-00" or $this->pq->Fields("dateended") != NULL){
		$P->petition_ends= DoDate($this->pq->Fields("dateended"),"M, j Y");}
	
	}
		
	function progressBox() {

		$sql="SELECT  COUNT(DISTINCT id) FROM userdata  where modin = ".$this->petmod 	;
		$ptct= $this->dbcon->CacheExecute($sql) or DIE("could not get count: ".$sql.$this->dbcon->ErrorMsg());
		$count = $ptct->fields[0];
		 
		$html .= "<table cellpadding=0 cellspacing=0 border=1 align=center bgcolor=\"#CCCCCC\" width=\"100%\"><tr><td>";
		$html .= "\n\t<table border=0 cellspacing=0 cellpadding=0 width=\"100%\"><tr>";
		if  ($this->petition_started){
			$html .= "\n\t\t<td align=center class=form><small><B>Posted:<br>".$this->petition_started."</B></small></td>";
		}
		if  ($this->petition_ends){
			$html .= "\n\t\t<td align=center class=form><B><small>Petition Ends:<br>".$this->petition_ends."</small></B></td>";
		}
		$html .= "\n\t\t<td align=center class=form><small><B>Petition Signatures:&nbsp; $count</b></small></td>";
		$html .= "\n\t</tr></table>";
		$html .= "</td></tr></table>";
		return $html;
	}
		
	function petition_signers(){
		if (!$_REQUEST["offset"]) {$offset= 0;}
		else {$offset=$_REQUEST["offset"];}
		$sql="SELECT First_Name, Last_Name, Company,Notes, City,  State  FROM userdata  where  modin = ".$this->petmod." and custom19 = 1 order by id desc  Limit $offset, ".$this->limit;
		$P=$this->dbcon->CacheExecute($sql) or DIE("could not find signers ".$sql.$this->dbcon->ErrorMsg());
		$sql="SELECT  COUNT(DISTINCT id) FROM userdata  where modin = ".$this->petmod." and custom19 =1";
		$ptct= $this->dbcon->CacheExecute($sql) or DIE("could not get count: ".$sql.$this->dbcon->ErrorMsg());
		$count = $ptct->fields[0];
		
		$html .='<a name="namelist"></a>
				<p class="title">Recent Petition Signers</p>
				<table width="100%" border="0" cellspacing="0" cellpadding="3">
				  <tr bgcolor="#CCCCCC"> 
					<td class="text">Name</td>
					<td class="text">Organization</td>
					<td class="text">Location</td>
					<td class="text">Comment</td>
				  </tr>';
		while (!$P->EOF) { 
			$html .= '
					  <tr> 
						<td class="text">'. trim($P->Fields("First_Name")).'&nbsp;'. trim($P->Fields("Last_Name")).'</td>
						<td class="text">'. $P->Fields("Company") .'</td>
						<td class="text">'. $P->Fields("City").'&nbsp;'.$P->Fields("State").'</td>
						<td class="text">'. $P->Fields("Notes").'</td>
					  </tr>';
			$P->MoveNext();
		}
		if ($count > $this->limit) {
			$html .= '<tr><div align=right><td colspan=4 class="text"><a href="petition.php?pid='.$pid.'&signers=1&offset='.($offset + $this->limit).'#namelist">Next Page</a></div></td></tr>';
		} 
		$html .= '</table><P><a href="petition.php?pid='. $this->pid.'">Sign the Petition</a></P><br><br>';
		return $html;
	}

	function intro_text() {
	
		$out .='<p class="title">'.$this->pq->Fields("title").'</p>';
		if ($this->pq->Fields("addressedto") != NULL) {
			$out .='<p><B><span class="bodystrong">To:</span> <span class="text">'.$this->pq->Fields("addressedto").'</span></B></p>';
		}
	
		$out .='<p class="text">'.converttext( $this->pq->Fields("text")).'</p>';
	
		if ($this->pq->Fields("intsigner") != NULL) {
			$out .='<p><B><span class="bodystrong">Initiated By:</span>'.$this->pq->Fields("intsigner").', '.$this->pq->Fields("org").'<a href="http://'.$this->pq->Fields("url").'">'.$this->pq->Fields("url").'</a><br>';
			$out .='a href="mailto:'.$this->pq->Fields("intsignerem").'">'. $this->pq->Fields("intsignerem").'</a></span></B></p>';
		}
		//$out .='<br>' ;
		return $out;	 
	}
	
	function signature_link() {
		$out =  "<P align=center><a href=\"petition.php?pid=".$this->pid."&signers=1\">View Signatures</a></p>";
		return $out;
	}

	function petitionlist() {
		$sql = "select * from petition  order by id desc";
		$R = $this->dbcon->Execute($sql) or DIE("could not find petition: ".$sql.$this->dbcon->ErrorMsg());
		while (!$R->EOF) {
			$out = '<p><a href="petition.php?pid='.$R->Fields("id").'" class="listtitle">'.$R->Fields("title") .'</a><br>'.$R->Fields("shortdesc").'</p><br>';
			$R->MoveNext();
		}
		return $out;
	}	

}

?>