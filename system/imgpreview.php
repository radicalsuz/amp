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
        background: url( /img/ajax-loader.gif ) no-repeat 45px 15px #eeffcc;
    }
    .imgthumb a {
        font-size: 10px;
        text-decoration: none;
        font-family: "Helvetica, Geneva, Arial, sans-serif";
    }
    #image_search_form {
        font-size: 10px;
        text-decoration: none;
        font-family: "Helvetica, Geneva, Arial, sans-serif";
    }
    #image_search_form {
        margin-bottom: 20px
    }
    #image_search_form label {
        margin-right: 10px;
    }
    #image_search_form input, #image_search_form select {
        margin-right: 30px;
    }
  </style>
</HEAD><BODY>


<?php
require_once('AMP/BaseDB.php');

$filelist = AMPfile_list('img/thumb/', null, true); 
unset($filelist['']);
# handy for debugging this
#$filelist = array_slice(  $filelist, 0, 200 );
require_once( 'AMP/Renderer/HTML.php');
$renderer = new AMP_Renderer_HTML( );
$folders = AMP_lookup( 'image_folders');
print "<form method='GET' action='/system/imgpreview.php' id='image_search_form'>\n";
print "<p>";
print $renderer->label( 'image_filename', "Search in file names" );
print $renderer->input( 'image_filename', null, array( 'id' => 'search_image_filename') );
print "</p>";
if( $folders ) {
    print "<p>";
    print $renderer->label( 'image_folder', "Show images in folder" );
    print $renderer->select( 'image_folder', null, array( '' => 'None') + $folders, array( 'id' => 'search_image_folder') );
    print "</p>";
}
print "\n</form>";


$index = 0;
foreach ($filelist as $picfile) {
    $src_attr = $index > 20 ? "original" : "src";
    $index++;
    print "<div class='imgthumb'><a href='/img/pic/$picfile' title='$picfile'>
        <img $src_attr=\"/image.php?image_class=thumb&filename=$picfile&action=resize&height=50&width=100&keep_proportions=1\">
        $picfile
        </a></div>\n";
}
?>
    <script type="text/javascript" src="/scripts/jquery/jquery-1.2.6.min.js"></script>
    <script type="text/javascript" src="/scripts/jquery/jquery.lazyload.js"></script>
    <script type="text/javascript">
        jq = jQuery.noConflict( );
        $ = jq;
        function search_images( name_value, folder_value ) {
            var use_name = !( name_value === '');
            var use_folder = !( folder_value === '');
            jq( '.imgthumb').hide( );
            if( use_name && use_folder ){
                jq( '.imgthumb a[title^="' + folder_value + '/"]:contains("' + name_value + '")').parent( '.imgthumb').show( );
            } else if ( use_name ) {
                jq( '.imgthumb a:contains("' + name_value + '")').parent( '.imgthumb').show( );
            } else if ( use_folder ) {
                jq( '.imgthumb a[title^="' + folder_value + '/"]').parent( '.imgthumb').show( );
            } else {
                jq( '.imgthumb').show( );
            }
            jq( window ).trigger( 'scroll');
        }

        $('document').ready( 
            function( ) {
                jq( 'img[src=""]').attr( 'src', '/img/spacer.gif').addClass( 'lazyload-empty');
                jq( 'img' ).lazyload( { threshold: 200 });
                jq( '.imgthumb a' ).click( function( ) {
                    setImage( $( this ).attr( 'href'));
                    return false;
                });
                var search_keypress_timeout;
                jq( '#image_search_form').submit( function( ){ return false; })
                jq( '#search_image_filename').val('').keypress( function( ){
                    if ( !( search_keypress_timeout === undefined )) {
                        clearTimeout( search_keypress_timeout );
                    }
                    var self = this;
                    setTimeout( function( ) { search_images( jq( self ).val( ), jq( '#search_image_folder').val( )) }, 300 );

                });
                jq( '#search_image_folder').val('').change( function( ){
                    search_images( jq( '#search_image_filename' ).val( ), jq( '#search_image_folder').val( ));
                });
            }
        );
    </script>
  </BODY>
</HTML>
