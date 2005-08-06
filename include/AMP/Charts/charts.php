<?php
// charts.php v2.1
// ------------------------------------------------------------------------
// Copyright (c) 2004, maani.us
// ------------------------------------------------------------------------
// This file is part of "PHP/SWF Charts"
//
// PHP/SWF Charts is a shareware. See http://www.maani.us/charts/ for
// more information.
// ------------------------------------------------------------------------
if (!defined( 'AMP_CONTENT_URL_FLASH' )) define ('AMP_CONTENT_URL_FLASH', AMP_SITE_URL . '/flash/' );

function DrawChart($chart){

	if (file_exists("charts.swf")){$path="";}
	else{$path= AMP_CONTENT_URL_FLASH . "charts.php";}
	#else{$path=str_replace ("charts.php","",str_replace($_SERVER['DOCUMENT_ROOT' ],"",__FILE__));}

	//defaults
	if(!isset($chart[ 'canvas_bg' ]['width' ])){$chart[ 'canvas_bg' ]['width' ] =400;}
	if(!isset($chart[ 'canvas_bg' ]['height' ])){$chart[ 'canvas_bg' ]['height' ] =250;}
	if(!isset($chart[ 'canvas_bg' ]['color' ])){$chart[ 'canvas_bg' ]['color' ] ="666666";}
								
	$params=GetParams($chart);
?>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" WIDTH=<?php print $chart[ 'canvas_bg' ]['width' ]; ?> HEIGHT=<?php print $chart[ 'canvas_bg' ]['height' ]; ?> ID="charts" ALIGN="">
<PARAM NAME=movie VALUE="<?php print $path."charts.swf"; ?>?<?php print $params; ?>"> 
<PARAM NAME=quality VALUE=high>
<PARAM NAME=bgcolor VALUE=<?php print $chart[ 'canvas_bg' ]['color' ]; ?> >
 
<EMBED src="<?php print $path."charts.swf"; ?>?<?php print $params; ?>" swLiveConnect=true quality=high ID="charts" NAME="charts" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer" bgcolor=<?php print $chart[ 'canvas_bg' ]['color' ]; ?> WIDTH=<?php print $chart[ 'canvas_bg' ]['width' ]; ?> HEIGHT=<?php print $chart[ 'canvas_bg' ]['height' ]; ?> ></EMBED>
</OBJECT>

<?php
}

//===============================
function UpdateChart($chart){
	echo GetParams($chart);
	exit;
}

//===============================
function GetParams($chart){
	$params="l_u=1&";
	$allKeys= array_keys($chart);
	for ($i=0;$i<count($allKeys);$i++)
	{
		switch(true){
			case ($allKeys[$i]=="chart_data"):
			$params=$params."rows=".count($chart[ 'chart_data' ])."&";
			$params=$params."cols=".count($chart[ 'chart_data' ][0])."&";
			for ($r=0;$r<count($chart[ 'chart_data' ]);$r++){
				$params=$params."r".$r."=";
				for ($c=0;$c<count($chart[ 'chart_data' ][$r]);$c++)
				{
					$params=$params.$chart[ 'chart_data' ][$r][$c];
					if($c==count($chart[ 'chart_data' ][$r])-1){$params=$params."&";}
					else{$params=$params.";";}
				}
			}
			break;
			
			case (substr($allKeys[$i],0,5)=="draw_" or $allKeys[$i]=="link"):
			for ($r=0;$r<count($chart[ $allKeys[$i] ]);$r++){
				$params=$params.$allKeys[$i]."_".$r."=";
				$allKeys2= array_keys($chart[ $allKeys[$i] ][$r]);
				for ($k2=0;$k2<count($allKeys2);$k2++){
					$params=$params.$allKeys2[$k2].":".$chart[ $allKeys[$i] ][$r][$allKeys2[$k2]];
					if($k2<count($allKeys2)-1){$params=$params.";";}
				}
				$params=$params."&";
			}
			break;
			
			default:
			if(gettype($chart[$allKeys[$i]])=="array" ){
				$params=$params.$allKeys[$i]."=";
				$allKeys2= array_keys($chart[$allKeys[$i]]);
				for ($k2=0;$k2<count($allKeys2);$k2++){
					$params=$params.$allKeys2[$k2].":".$chart[$allKeys[$i]][$allKeys2[$k2]];
					if($k2<count($allKeys2)-1){$params=$params.";";}
				}
				$params=$params."&";
			}else{
				$params=$params.$allKeys[$i]."=".$chart[$allKeys[$i]]."&";
			}
		}
	}
	return $params;
}
//===============================
?>
