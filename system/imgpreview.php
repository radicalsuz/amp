<HTML>
  <HEAD>
    <SCRIPT TYPE="text/javascript">
    <!--
    function setImage( imgname ) {
            url_item = parent.document.getElementById("f_url");

            url_item.value = imgname;
            //window.document.forms["Insert_Image"].elements["url"].value = imgname;
            parent.onPreview();
    }
    -->
    </SCRIPT>
    <script type="text/javascript" src="/scripts/jquery/jquery-1.2.6.min.js"></script>
    <script type="text/javascript" src="/scripts/jquery/jquery.lazyload.js"></script>
    <style>
    .imgthumb {
     float:left;
     width: 100px;
     height: 80px;
     overflow: hidden;
     margin-bottom: 20px;
     margin-right: 10px;
    }
    .imgthumb img {
        display: block;
        border: 0;
    }
    .imgthumb img.lazyload-empty {
        width: 100px;
        height: 50px;
        background-color: #eeffcc;
    }
    .imgthumb img.lazyload-waiting {
        background: url( /img/ajax-loader.gif ) no-repeat 0 0;
    }
    .imgthumb a {
        font-size: 10px;
        text-decoration: none;
        font-family: "Helvetica, Geneva, Arial, sans-serif";
    }
  </style>
</HEAD><BODY>

<?php
require_once('AMP/BaseDB.php');

$filelist = AMPfile_list('img/thumb/', null, true); 
unset($filelist['']);
# handy for debugging this
#$filelist = array_slice(  $filelist, 0, 200 );

$index = 0;
foreach ($filelist as $picfile) {
    $src_attr = $index > 10 ? "original" : "src";
    $index++;
    print "<div class='imgthumb'><a href='javascript:setImage(\"/img/pic/$picfile\");' title='$picfile'>
        <img $src_attr=\"/image.php?image_class=thumb&filename=$picfile&action=resize&height=50&width=100&keep_proportions=1\">
        $picfile
        </a></div>\n";
}
?>
<script type="text/javascript">
jq = jQuery.noConflict( );
$ = jq;
$('document').ready( 
    function( ) {
        jq( 'img' ).lazyload(  { placeholder: '/img/spacer.gif'});
    }
);
</script>
  </BODY>
</HTML>
