<?php
$intro_id = 40;
$modid = 19;
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  

//$obj = new Menu;
				
  $day = (60 * 60 * 24);
$yesterday =date('Y-m-d',( time() - $day));

$week = $day * 7;
$weekAgo  =date('Y-m-d',(time() - $week));

$month = $day * 30;
$monthAgo =date('Y-m-d',(time() - $month));

$twoMonths = $day * 60;
$twoMonthsAgo =date('Y-m-d',(time() - $twoMonths));
$Year = $day * 365;
$YearAgo =date('Y-m-d',(time() - $Year));
  

function processWindow(&$content, $width, $height, $q) {
	//global $config;

	$lowQ = strtolower($q);
	$qSize = strlen($q);
	$window = $width / 2;
	$descEnd = "...";
	$boldQuery = true;
	$stripTags = true;
	$desc = array();

	$content = str_replace('<br', ' <br', $content);
	$content = str_replace('<Br', ' <Br', $content);
	$content = str_replace('<BR', ' <BR', $content);
	$content = str_replace('<p', ' <p', $content);
	$content = str_replace('<P', ' <P', $content);
	$content = str_replace('&nbsp;', ' ', $content); //'&nbsp' parts show up if they are broken, so convert to spaces.

	if ($stripTags)
		$content = strip_tags($content);

	$content = ereg_replace('[[:space:]]{2,}', ' ', $content); //Consume extra whitespace from content.
	$content = trim($content);

	$lowContent = strtolower($content);
	$contentSize = strlen($content);

	$queryIdx = 0;
	for ($i = 0; $i < $height; $i++) {
		$queryIdx = strpos($lowContent, $lowQ, $queryIdx);
		if (!is_int($queryIdx) || $queryIdx >= $contentSize) //If we don't find $q, then there are no more matches.
			break;

		/* Grab the section of text around the matching keyword. */
		$lBound = ($queryIdx - $window < 0) ? 0 : $queryIdx - $window;
		$uBound = ($lBound + $width > $contentSize) ? $contentSize : $lBound + $width;
		$lBound = ($uBound < $contentSize) ? $lBound : (($uBound - $width < 1) ? 0 : $uBound - $width);

		/* Slide our window to avoid cutting words. */
		$descAdj = $uBound - ($queryIdx + $qSize);
		for ($j = 0; $j < $descAdj; $j++) {
			if ($lBound - $j <= 0 || $content[$lBound - $j - 1] == ' ') {
				$lBound -= $j;
				$uBound -= $j;
				break;
			}
		}

		/* Shrink the uBound to avoid cutting words. */
		$descAdj = $uBound - ($queryIdx + $qSize);
		if ($uBound < $contentSize && $content[$uBound] != ' ') {
			for ($j = 1; $j < $descAdj; $j++) {
				if ($content[$uBound - $j] == ' ' && $uBound - $j > $lBound) {
					$uBound -= $j;
					break;
				}
			}
		}

		/* Cut the desc out of content and add descEnd. */
		$descBuf = '';
		if ($lBound > 0)
			$descBuf = $descEnd;
		$descBuf .= trim(substr($content, $lBound, $uBound - $lBound));
		if ($uBound < $contentSize)
			$descBuf .= $descEnd;

		if (!$boldQuery)
			$desc[$i] = $descBuf;
		else
			$desc[$i] = eregi_replace("($q)","<B>\\1</B>", $descBuf);

		$queryIdx = $uBound; //Jump the queryIdx to the end of the desc.
	}
	
	return $desc;
}



//if ($_GET[q]) {

//}
?>
            <form action="search.php" method="get" name="form2" class="name">
  <p><strong>Search For&nbsp;&nbsp;&nbsp;</strong><br>
    <input name="q" type="text" id="title" size="35" class="name">
  <br><strong>Posted in Last:<br>
    </strong> 
    <select name="date">
      <option value="">--</option>
      <option value="<?php echo $yesterday ; ?>">Day</option>
      <option value="<?php echo $weekAgo ; ?>">Week</option>
      <option value="<?php echo  $monthAgo ; ?>">Month</option>
      <option value="<?php echo $twoMonthsAgo ; ?>">Two 
      Months</option>
      <option value="<?php echo $YearAgo ; ?>">Year</option>
    </select>
 <br>
    
  <strong>In Section:<br>
  </strong> 
  <select name="section">
      <option value="">--</option>
				  <?php  echo  $obj->select_type_tree(1); ?>
 </select>
</p>
    <input name="Search" type="submit"  class="name"><hr>
</form> 
<?php
if ($_GET[q]) {
if ($_GET[offset]) {$offset=$_GET[offset];}
else { $offset=0;}
if ($_GET[limit]) {$limit=$_GET[limit];}
else { $limit=25;}

if ($_GET['date']){ 
$date = $_GET['date'] ;
$dsql = " and  date > '$date' "; }

 if ($_GET['section']){ 
$section = $_GET['section'] ;
$ssql = " and $MX_type = $section "; } 


$sql= "select test, title, id from articles where match(test, author, shortdesc, title) against('$_GET[q]') and publish =1 $dsql $ssql   ";
$sqlct= "SELECT  COUNT(DISTINCT id) from articles where match(test, author, shortdesc, title) against('$_GET[q]') and publish =1  $dsql  $ssql  ";
$searchx=$dbcon->CacheExecute($sql." Limit  $offset, $limit;");

if  (!$searchx->Fields('id')) {
$sql = "select distinct test, title, id from articles where  (title like '%$_GET[q]%'  or test like '%$_GET[q]%'  or author like '%$_GET[q]%'  or shortdesc like '%$_GET[q]%'   )and publish =1 $dsql  $ssql  order by id desc ";
$sqlct = "SELECT  COUNT(DISTINCT id) from articles where  (title like '%$_GET[q]%'  or test like '%$_GET[q]%'  or author like '%$_GET[q]%'  or shortdesc like '%$_GET[q]%'   )and publish =1 $dsql $ssql   order by id desc ";
$searchx=$dbcon->CacheExecute($sql."Limit  $offset, $limit;") ;
}
//echo $sql;
$listct=$dbcon->CacheExecute("$sqlct");
$count = $listct->fields[0];

echo "Displaying results ".($offset +1)."-".($offset +$limit)." of ".$count." matches for query  <b>".$q."</b> <br><br>";

 $pages = ceil(($count/$limit));

if ($pages > 1) {
$i = 0;
$io =0;
echo "<b>Pages:</b>&nbsp;";
while ($i != $pages) {
echo "<a  href=\"search.php?q=$_GET[q]&offset=";
echo $io;
echo "\">";
echo ($i +1);
echo "</a> ";
$io = ($io+$limit);
$i++;
}
echo "<br><br>"; }
$i = ($offset+1);
 while (!$searchx->EOF)   {  ?>
<span class =listtitle><?php echo $i;?>.&nbsp;
<a href="article.php?id=<?php echo $searchx->Fields('id')."\"  class=listtitle>".strip_tags($searchx->Fields('title')); ?></a> <br></span>
<?php $desc = processWindow($searchx->Fields('test'), 80, 1, $_GET[q]); 
if ($desc[0]) {echo $desc[0];}
else {echo $searchx->Fields('shortdesc');}
$i++;
?>
<br><br>

 <?php $searchx->MoveNext(); }  }
 
include ("AMP/BaseFooter.php");?>
