<html>
<head><?php  $filename = $_GET[file]; ?>
<script>
<!--
function goState(id) {
	opener.location.href ='<?php echo $filename; ?>?area=' + id;
	window.close();
	
}
//-->
</script>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td valign="top" class="headerH1">Click on a state to get local listings or 
      <a href="<?php echo $filename; ?>">view 
      nation-wide listings</a></td>
    <td valign="top" class="headerH1"><div align="right">
        <select name="lstate"  onChange="MM_jumpMenu('opener',this,0)">
          <option SELECTED>Select State</option>
          <option value="<?php echo $filename; ?>?area=1" >Alabama 
          </option>
          <option value="<?php echo $filename; ?>?area=2" >Alaska 
          </option>
          <option value="<?php echo $filename; ?>?area=3" >Arizona 
          </option>
          <option value="<?php echo $filename; ?>?area=4" >Arkansas 
          </option>
          <option value="<?php echo $filename; ?>?area=5" >California 
          </option>
          <option value="<?php echo $filename; ?>?area=6" >Colorado 
          </option>
          <option value="<?php echo $filename; ?>?area=7" >Connecticut 
          </option>
          <option value="<?php echo $filename; ?>?area=9" >DC 
          </option>
          <option value="<?php echo $filename; ?>?area=8" >Delaware 
          </option>
          <option value="<?php echo $filename; ?>?area=10" >Florida 
          </option>
          <option value="<?php echo $filename; ?>?area=11" >Georgia 
          </option>
          <option value="<?php echo $filename; ?>?area=12" >Hawaii 
          </option>
          <option value="<?php echo $filename; ?>?area=13" >Idaho 
          </option>
          <option value="<?php echo $filename; ?>?area=14" >Illinois 
          </option>
          <option value="<?php echo $filename; ?>?area=15" >Indiana 
          </option>
          <option value="<?php echo $filename; ?>?area=53" >International 
          </option>
          <option value="<?php echo $filename; ?>?area=16" >Iowa 
          </option>
          <option value="<?php echo $filename; ?>?area=17" >Kansas 
          </option>
          <option value="<?php echo $filename; ?>?area=18" >Kentucky 
          </option>
          <option value="<?php echo $filename; ?>?area=19" >Louisiana 
          </option>
          <option value="<?php echo $filename; ?>?area=20" >Maine 
          </option>
          <option value="<?php echo $filename; ?>?area=21" >Maryland 
          </option>
          <option value="<?php echo $filename; ?>?area=22" >Massachusetts 
          </option>
          <option value="<?php echo $filename; ?>?area=23" >Michigan 
          </option>
          <option value="<?php echo $filename; ?>?area=24" >Minnesota 
          </option>
          <option value="<?php echo $filename; ?>?area=25" >Mississippi 
          </option>
          <option value="<?php echo $filename; ?>?area=26" >Missouri 
          </option>
          <option value="<?php echo $filename; ?>?area=27" >Montana 
          </option>
          <option value="<?php echo $filename; ?>?area=28" >Nebraska 
          </option>
          <option value="<?php echo $filename; ?>?area=29" >Nevada 
          </option>
          <option value="<?php echo $filename; ?>?area=30" >New 
          Hampshire </option>
          <option value="<?php echo $filename; ?>?area=31" >New 
          Jersey </option>
          <option value="<?php echo $filename; ?>?area=32" >New 
          Mexico </option>
          <option value="<?php echo $filename; ?>?area=33" >New 
          York </option>
          <option value="<?php echo $filename; ?>?area=34" >North 
          Carolina </option>
          <option value="<?php echo $filename; ?>?area=35" >North 
          Dakota </option>
          <option value="<?php echo $filename; ?>?area=36" >Ohio 
          </option>
          <option value="<?php echo $filename; ?>?area=37" >Oklahoma 
          </option>
          <option value="<?php echo $filename; ?>?area=38" >Oregon 
          </option>
          <option value="<?php echo $filename; ?>?area=39" >Pennsylvania 
          </option>
          <option value="<?php echo $filename; ?>?area=40" >Puerto 
          Rico </option>
          <option value="<?php echo $filename; ?>?area=41" >Rhode 
          Island </option>
          <option value="<?php echo $filename; ?>?area=42" >South 
          Carolina </option>
          <option value="<?php echo $filename; ?>?area=43" >South 
          Dakota </option>
          <option value="<?php echo $filename; ?>?area=44" >Tennessee 
          </option>
          <option value="<?php echo $filename; ?>?area=45" >Texas 
          </option>
          <option value="<?php echo $filename; ?>?area=46" >Utah 
          </option>
          <option value="<?php echo $filename; ?>?area=47" >Vermont 
          </option>
          <option value="<?php echo $filename; ?>?area=48" >Virginia 
          </option>
          <option value="<?php echo $filename; ?>?area=49" >Washington 
          </option>
          <option value="<?php echo $filename; ?>?area=50" >West 
          Virginia </option>
          <option value="<?php echo $filename; ?>?area=51" >Wisconsin 
          </option>
          <option value="<?php echo $filename; ?>?area=52" >Wyoming 
          </option>
        </select>
      </div></td>
  </tr>
  <tr> 
    <td colspan="2" align="center"> <br/>
      <br/> <map name=ImageMap10364>
        <area shape=rect alt="Hawaii"               coords=28,106,95,144                                                   href="#" onClick="goState(12)">
        <area shape=rect alt="Alaska"               coords=9,7,108,84                                                      href="#" onClick="goState(2)">
        <area shape=POLY alt="Alabama"              coords=362,154,367,185,352,189,349,193,345,190,347,152                 href="#" onClick="goState(1)">
        <area shape=POLY alt="Arkansas"             coords=331,144,326,157,319,169,303,166,300,159,303,138                 href="#" onClick="goState(4)">
        <area shape=POLY alt="Arizona"              coords=206,132,198,180,185,175,164,159,167,133,173,127,175,124         href="#" onClick="goState(3)">
        <area shape=poly alt="California"           coords=145,76,144,102,167,141,162,158,147,155,124,131,116,81,124,68 href="#" onClick="goState(5)">
        <area shape=POLY alt="Colorado"             coords=251,104,247,132,208,129,213,99 href="#" onClick="goState(6)">
        <area shape=POLY alt="Connecticut"          coords=436,82,429,88,428,80,436,81  href="#" onClick="goState(7)">
        <area shape=rect alt="Maryland"             coords=461,147,483,162               href="#" onClick="goState(21)">
        <area shape=rect alt="District Of Columbia" coords=461,132,483,147     href="#" onClick="goState(9)">
        <area shape=POLY alt="Delaware"             coords=421,112,417,109,418,105,422,112 href="#" onClick="goState(8)">
        <area shape=rect alt="Delaware"             coords=461,118,483,133                 href="#" onClick="goState(8)">
        <area shape=POLY alt="Florida"              coords=399,184,415,223,408,229,402,226,386,202,379,194,367,195,353,191,355,184 href="#" onClick="goState(10)">
        <area shape=poly alt="Georgia"              coords=383,154,398,175,396,183,391,186,370,182,364,152 href="#" onClick="goState(11)">
        <area shape=POLY alt="Iowa"                 coords=319,86,324,97,320,103,289,103,286,93,288,82 href="#" onClick="goState(16)">
        <area shape=POLY alt="Idaho"                coords=180,29,183,47,186,57,190,68,200,71,196,89,163,80,173,27 href="#" onClick="goState(13)">
        <area shape=POLY alt="Illinois"             coords=346,94,340,133,335,130,331,124,326,118,321,110,325,91 href="#" onClick="goState(14)">
        <area shape=POLY alt="Indiana"              coords=361,94,356,120,345,124,347,93 href="#" onClick="goState(15)">
        <area shape=POLY alt="Kansas"               coords=296,112,297,135,252,134,254,108 href="#" onClick="goState(17)">
        <area shape=POLY alt="Kentucky"             coords=373,125,377,130,366,137,331,137,344,128,354,121,364,119,372,121 href="#" onClick="goState(18)">
        <area shape=POLY alt="Louisiana"            coords=323,172,321,180,322,184,334,187,340,197,338,202,305,194,307,168 href="#" onClick="goState(19)">
        <area shape=poly alt="Massachusetts"        coords=447,67,449,76,426,72,443,72 href="#" onClick="goState(22">
        <area shape=rect alt="New Jersey"           coords=461,104,483,119 href="#" onClick="goState(31)">
        <area shape=POLY alt="Maryland"             coords=424,106,428,116,417,113,409,107,401,107 href="#" onClick="goState(21)">
        <area shape=rect alt="Connecticut"             coords=461,89,483,104 href="#" onClick="goState(7)">
        <area shape=POLY alt="Maine"                coords=453,28,462,46,448,59,443,62,439,31,444,25 href="#" onClick="goState(20)">
        <area shape=poly alt="Michigan"             coords=364,59,375,81,369,91,346,88,345,65,332,57,324,53,340,44 href="#" onClick="goState(23)">
        <area shape=POLY alt="Minnesota"            coords=297,32,320,46,307,56,309,74,315,82,283,78,283,35 href="#" onClick="goState(24)">
        <area shape=POLY alt="Missouri"             coords=320,105,333,131,332,143,328,141,300,135,294,109,294,105 href="#" onClick="goState(26)">
        <area shape=POLY alt="Mississippi"          coords=345,153,341,191,338,186,324,181,332,150 href="#" onClick="goState(25)">
        <area shape=poly alt="Montana"              coords=247,35,238,68,202,62,193,64,182,46,181,25 href="#" onClick="goState(27)">
        <area shape=POLY alt="North Carolina" coords=429,129,429,140,411,152,400,147,380,147,371,147,399,130 href="#" onClick="goState(34)">
        <area shape=POLY alt="North Dakota"   coords=282,36,282,60,244,57,248,32 href="#" onClick="goState(35)">
        <area shape=POLY alt="Nebraska"       coords=285,88,288,108,252,106,248,97,238,94,241,81 href="#" onClick="goState(28)">
        <area shape=POLY alt="New Hampshire"  coords=442,71,435,70,439,52,440,72 href="#" onClick="goState(30)">
        <area shape=rect alt="New Hampshire"  coords=418,33,433,44 href="#" onClick="goState(30)">
        <area shape=POLY alt="New Jersey"     coords=427,89,426,108,421,101,422,94,423,87 href="#" onClick="goState(31)">
        <area shape=rect alt="Rhode Island"     coords=461,75,483,90 href="#" onClick="goState(41)">
        <area shape=POLY alt="New Mexico"     coords=244,137,238,176,208,180,202,179,210,131 href="#" onClick="goState(32)">
        <area shape=POLY alt="Nevada"         coords=181,85,169,131,167,134,143,94,149,75 href="#" onClick="goState(29)">
        <area shape=poly alt="New York"       coords=435,86,438,88,428,89,413,82,391,83,394,72,410,68,413,55,424,53,433,86 href="#" onClick="goState(33)">
        <area shape=POLY alt="Ohio"           coords=383,102,381,107,380,112,372,120,359,116,363,95,373,93,383,92 href="#" onClick="goState(36)">
        <area shape=POLY alt="Oklahoma"       coords=299,139,297,162,271,159,263,152,260,139,245,136 href="#" onClick="goState(37)">
        <area shape=poly alt="Oregon"         coords=140,40,170,47,164,78,123,64,128,48,135,37 href="#" onClick="goState(38)">
        <area shape=POLY alt="Pennsylvania"   coords=418,90,416,102,385,103,387,88,418,88 href="#" onClick="goState(39)">
        <area shape=POLY alt="Rhode Island"   coords=441,84,437,83,439,79,439,85 href="#" onClick="goState(41)">
        <area shape=rect alt="Massachusetts"   coords=461,61,483,76 href="#" onClick="goState(22)">
        <area shape=POLY alt="South Dakota"   coords=284,62,282,85,241,83,244,56 href="#" onClick="goState(43)">
        <area shape=POLY alt="South Carolina" coords=411,154,410,159,396,172,379,148,405,148 href="#" onClick="goState(42)">
        <area shape=POLY alt="Tennessee"      coords=385,136,363,150,331,148,337,140 href="#" onClick="goState(44)">
        <area shape=poly alt="Texas"          coords=261,142,264,155,281,161,302,163,303,199,282,218,280,231,265,225,248,195,242,200,238,202,230,196,224,184,220,176,241,175,248,139 href="#" onClick="goState(45)">
        <area shape=POLY alt="Utah"           coords=197,90,200,97,208,101,202,130,172,123,181,85 href="#" onClick="goState(46)">
        <area shape=POLY alt="Virginia"       coords=413,114,424,118,419,127,378,130,383,128,393,121,397,115,407,108 href="#" onClick="goState(48)">
        <area shape=poly alt="Vermont"        coords=436,51,432,69,429,49 href="#" onClick="goState(47)">
        <area shape=rect alt="Vermont"        coords=397,43,413,55 href="#" onClick="goState(47)">
        <area shape=POLY alt="Washington"     coords=174,24,168,46,138,39,132,32,134,14,173,22 href="#" onClick="goState(49)">
        <area shape=POLY alt="Wisconsin"      coords=333,61,339,66,343,72,337,90,321,87,313,75,310,71,314,56,323,57 href="#" onClick="goState(51)">
        <area shape=POLY alt="West Virginia"  coords=385,105,389,108,398,108,401,110,390,124,381,130,373,122 href="#" onClick="goState(50)">
        <area shape=POLY alt="Wyoming"        coords=243,70,237,100,198,94,204,63 href="#" onClick="goState(52)">
      </map> <img src="img/state-map.gif" usemap="#ImageMap10364" alt="" border="0"/> </p> 
    </td>
  </tr>
</table>

</body>
</html>