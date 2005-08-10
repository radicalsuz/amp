<?php
/*********************
03-16-2005  v3.01
Module:  Index
Description:  display index page content
CSS: text, photocaption, hometitle,  subtitle, homebody
SYS VARS: $NAV_IMG_PATH
functions  getimagesize
To Do: 
*********************/ 
$maintext=$dbcon->CacheExecute("SELECT *  FROM articles  
                                WHERE class=2 and publish='1'  
                                ORDER BY pageorder asc") 
                                or DIE($dbcon->ErrorMsg());


print '<div class="home"><table width="100%" class="text">';

while  ( !$maintext->EOF ) { 
    print '<tr><td>';

    if ( $maintext->Fields("picuse") == (1) ) { //start of picture 

        $fpathtoimg = AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . $NAV_IMG_PATH .$maintext->Fields("pselection")."/".$maintext->Fields("picture");
        $pathtoimg = $NAV_IMG_PATH .$maintext->Fields("pselection")."/".$maintext->Fields("picture");
        if (file_exists($fpathtoimg)) {
            $imageInfo = getimagesize($fpathtoimg); 
            $pwidth = $imageInfo[0]; 
            $pheight = $imageInfo[1];
        } else {
            $pwidth = 0;
            $pheight = 0;
        }


    print   '<table width="'. 
            $pwidth .
            '" border="0" align="';
    if ( $maintext->Fields( "alignment" ) == ( "left" ) ) {
        echo "left";
    } else {
    echo "right";
    }

    print '" cellpadding="0" cellspacing="0"><tr><td>';
    print '<img src="' .
            $pathtoimg .  
            '" alt="' .
            $maintext->Fields("alttag") .
            '" vspace="4" hspace="4" border="1" width="' .
            $pwidth .
            '" height="' .
            $pheight . 
            '"></td></tr><tr align="center"><td width="' .
            $pwidth .
            '" class="photocaption">' .
            $maintext->Fields("piccap") .
            '</td></TR></table>';
    } //end of picture

    if ( $maintext->Fields( "title" ) != (NULL) ) {  // start of title
        print '<p class="hometitle">';
        
        if ( $maintext->Fields( "usemore" ) == ('1') ) { 
            print '<a href="' .
                $maintext->Fields( "morelink" ) .
                '" class="hometitle">';
        } 
        
        print $maintext->Fields( "title" );

        if ( $maintext->Fields( "usemore" ) == ('1') ) { 
            echo "</a>"; 
        }

        print '</p>'; 
     } // end of title
    
    if ( $maintext->Fields( "subtitile" ) != (NULL) ) {  // start of subtitle 
        print '<span class="subtitle">';
        print $maintext->Fields("subtitile");
        print '</span>'; 
    }  //end if for subtitle

    
    print '<span class="homebody"> <p class="homebody">';
      

    if ( $maintext->Fields( "html" ) == (0) ) {   // start non html text
        echo converttext( $maintext->Fields( "test" ) ); 
    }  //end of non html text
    
    if ( $maintext->Fields( "html" ) == (1) ) {  //start of html text 
        echo $maintext->Fields( "test" ); 
    } //end of html text 
	print '</p></span>';
    if ($maintext->Fields("usemore") == ('1')) {  // start of more link
        print ' <span class="morelink">'; 
        print '<a href="' .
                $maintext->Fields( "morelink" ) .
                '" class="morelink">Read More&nbsp;&#187;</a>&nbsp;&nbsp; </span><br>';
     }

   
	print '<br></td></tr>';

    $maintext->MoveNext();  

}

print '</table></div>';
