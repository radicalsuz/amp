<?php
require_once( 'AMP/System/Data/Item.inc.php');

Class Petition extends AMPSystem_Data_Item {

	var $petition_started = false;
	var $petition_ends = false;
	var $limit =25;
    var $datatable = "petition";
    var $name_field = 'title';
	
	function Petition(&$dbcon, $pid=NULL,$modin=NULL) {
        $this->__construct( $dbcon, $pid, $modin );
	}

    function __construct( &$dbcon, $pid=NULL, $modin=NULL ) {
        if ( isset( $modin ) && $modin_petition_id = $this->findByModin( $modin )){
            $pid = $modin_petition_id;
        }
        $this->init( $dbcon, $pid );

    }

    function findByModin( $modin ){
        require_once( 'Modules/Petition/Lookups.inc.php');
        $modin_lookup = AMPSystem_Lookup::instance( 'petitionsByModin');
        if ( !isset( $modin_lookup[ $modin ])) return false;
        return $modin_lookup[ $modin ];
    }

    function getFormId( ){
        return $this->getData( 'udmid');
    }

    function setFormId( $modin ) {
        return $this->mergeData( array( 'udmid' => $modin ));
    }

    function getStartDate( ){
        $start_date = $this->getData( 'datestarted');
        return AMP_verifyDateValue( $start_date );
    }

    function getEndDate( ){
        $end_date = $this->getData( 'dateended');
        return AMP_verifyDateValue( $end_date );

    }

    function getBlurb( ){
        return $this->getData( 'shortdesc');
    }

    function getURL( ){
        if ( !isset( $this->id )) return AMP_CONTENT_URL_PETITIONS;
        return AMP_Url_AddVars( AMP_CONTENT_URL_PETITIONS, array( 'pid='.$this->id ));
    }

    function _adjustSetData( $data ){
		if ($start_date = $this->getStartDate( )) $P->petition_started = DoDate($start_date,"M, j Y");
		if ($end_date  = $this->getEndDate( )) $P->petition_started = DoDate($end_date,"M, j Y");
    }

	function progressBox() {

		$sql="SELECT  COUNT(id) as qty FROM userdata  where modin = ".$this->getFormId( );
		$ptct= $this->dbcon->CacheExecute($sql) or DIE("could not get count: ".$sql.$this->dbcon->ErrorMsg());
		$count = $ptct->Fields('qty');
		 
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
        require_once( 'AMP/UserData/Set.inc.php');
        $udm = new UserDataSet( $this->dbcon, $this->getFormId( ));
        if ( $list_plugin = $udm->getPlugin( 'Output', 'List')) {
            trigger_error( get_class( $list_plugin ));
            return $udm->doPlugin( 'Output', 'List');
        }
        
        $offset = 0;  
        if ( isset( $_REQUEST['offset'] ) && $_REQUEST['offset'] ) {
            $offset = $_REQUEST['offset'];
        }
		$sql= "SELECT First_Name, Last_Name, Company, Notes, City, State FROM userdata where modin = ".$this->getFormId( )." and custom19 = 1 order by id desc  Limit $offset, ".$this->limit;
		$P  = $this->dbcon->CacheExecute($sql) or DIE("could not find signers ".$sql.$this->dbcon->ErrorMsg());

		$sql= "SELECT  COUNT(*) FROM userdata  where modin = ".$this->getFormId( )." and custom19 =1";
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
			$html .= '<tr><div align=right><td colspan=4 class="text"><a href="petition.php?pid='. $this->id .'&signers=1&offset='.($offset + $this->limit).'#namelist">Next Page</a></div></td></tr>';
		} 
		$html .= '</table><P><a href="petition.php?pid='. $this->id.'">Sign the Petition</a></P><br><br>';
		return $html;
	}

	function intro_text() {
	
		$out .='<p class="title">'.$this->getData("title").'</p>';
		if ($this->getData("addressedto") != NULL) {
			$out .='<p><B><span class="bodystrong">To:</span> <span class="text">'.$this->getData("addressedto").'</span></B></p>';
		}
	
		$out .='<p class="text">'.converttext( $this->getData("text")).'</p>';
	
		if ($this->getData("intsigner") != NULL) {
			$out .='<p><B><span class="bodystrong">Initiated By:</span>'.$this->getData("intsigner").', '.$this->getData("org").'<a href="http://'.$this->getData("url").'">'.$this->getData("url").'</a><br>';
			$out .='<a href="mailto:'.$this->getData("intsignerem").'">'. $this->getData("intsignerem").'</a></span></B></p>';
		}
		//$out .='<br>' ;
		return $out;	 
	}
	
	function signature_link() {
		$out =  "<P align=center><a href=\"petition.php?pid=".$this->id."&signers=1\">View Signatures</a></p>";
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

    function get_url_edit( ) {
        if (!( isset( $this->id ) && $this->id )) return AMP_SYSTEM_URL_PETITIONS;
        return AMP_url_update( AMP_SYSTEM_URL_PETITIONS, array( 'id' => $this->id ) );
    }

}

?>
