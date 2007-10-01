<?php
/*
    Dynamic Heading Generator
    By Stewart Rosenberger
    http://www.stewartspeak.com/headings/    

    This script generates PNG images of text, written in
    the font/size that you specify. These PNG images are passed
    back to the browser. Optionally, they can be cached for later use. 
    If a cached image is found, a new image will not be generated,
    and the existing copy will be sent to the browser.

    Additional documentation on PHP's image handling capabilities can
    be found at http://www.php.net/image/    
*/
/*
require_once( 'AMP/Base/DB.php');
require_once( 'AMP/HostConfig.inc.php');
require_once( 'Config/Dynamic.php');
AMP_config_load( 'system');
AMP_config_load( 'cache');
*/
require_once( 'AMP/Base/Config.php');

$font_face = ( isset( $_REQUEST['face']) && $_REQUEST['face'] ) ? $_REQUEST['face'] : 'FreeSans';
$font_file_1  = AMP_BASE_INCLUDE_PATH . 'TrueType/' . $font_face . '.otf';
$font_file_2  = AMP_BASE_INCLUDE_PATH . 'TrueType/' . $font_face . '.ttf';
$font_file = file_exists( $font_file_1 ) ? $font_file_1 : ( file_exists( $font_file_2 ) ? $font_file_2 : false ); 
if ( !$font_file ) {
    $font_file_1  = AMP_LOCAL_PATH . '/custom/fonts/' . $font_face . '.otf';
    $font_file_2  = AMP_LOCAL_PATH . '/custom/fonts/' . $font_face . '.ttf';
    $font_file = file_exists( $font_file_1 ) ? $font_file_1 : ( file_exists( $font_file_2 ) ? $font_file_2 : AMP_BASE_INCLUDE_PATH . "TrueType/FreeSans.ttf");
}

$font_size  =  ( isset( $_REQUEST['size']) && $_REQUEST['size'] ) ? $_REQUEST['size'] : 50 ;
$font_color  =  ( isset( $_REQUEST['color']) && $_REQUEST['color'] ) ? '#'.$_REQUEST['color'] : '#000000' ;
$background_color  =  ( isset( $_REQUEST['background_color']) && $_REQUEST['background_color'] ) ? '#'. $_REQUEST['background_color'] : '#ffffff' ;
$transparent_background  = ( isset( $_REQUEST['transparent']) && $_REQUEST['transparent'] ) ? $_REQUEST['transparent'] : true;
$cache_images = ( isset( $_REQUEST['cache'])  ) ? $_REQUEST['cache'] : true;
$cache_folder = '/cache' ;

$shadow = ( isset( $_REQUEST['shadow'])  ) ? $_REQUEST['shadow'] : false;
$kerning = ( isset( $_REQUEST['kerning'])  ) ? $_REQUEST['kerning'] : false;








/*
  ---------------------------------------------------------------------------
   For basic usage, you should not need to edit anything below this comment.
   If you need to further customize this script's abilities, make sure you
   are familiar with PHP and its image handling capabilities.
  ---------------------------------------------------------------------------
*/

$mime_type = 'image/png' ;
$extension = '.png' ;
$send_buffer_size = 4096 ;

// check for GD support
if(!function_exists('ImageCreate'))
    fatal_error('Error: Server does not support PHP image generation') ;

// clean up text
if(empty($_GET['text']))
    fatal_error('Error: No text specified.') ;
    
$text = $_GET['text'] ;
if(get_magic_quotes_gpc())
    $text = stripslashes($text) ;
$text = javascript_to_html($text) ;

// look for cached copy, send if it exists
$hash = md5(basename($font_file) . $font_size . $font_color .
            $background_color . $transparent_background . $text . $shadow . $kerning) ;
$cache_filename = AMP_LOCAL_PATH . $cache_folder . '/' . AMP_CACHE_TOKEN_IMAGE . $hash . $extension ;
if ( $cache_images && file_exists( $cache_filename ))
{
    header('Content-type: ' . $mime_type) ;
    $cache_ptr = fopen( $cache_filename, 'r' );
    fpassthru( $cache_ptr );
    fclose( $cache_ptr );
    exit ;
}

// check font availability
$font_found = is_readable($font_file) ;
if(!$font_found)
{
    fatal_error('Error: The server is missing the specified font.') ;
}

// create image
$background_rgb = hex_to_rgb($background_color) ;
$font_rgb = hex_to_rgb($font_color) ;
$dip = get_dip($font_file,$font_size) ;
$box = @ImageTTFBBox($font_size,0,$font_file,$text) ;

if ( $kerning ) {
    $box[4] = $box[4] + ( $kerning * strlen( $text )); 
    $box[2] = $box[2] + ( $kerning * strlen( $text ));
}

$image = @ImageCreate(abs($box[2]-$box[0]),abs($box[5]-$dip)) ;
if(!$image || !$box)
{
    fatal_error('Error: The server could not create this heading image.') ;
}

// allocate colors and draw text
$background_color = @ImageColorAllocate($image,$background_rgb['red'],
    $background_rgb['green'],$background_rgb['blue']) ;
$font_color = ImageColorAllocate($image,$font_rgb['red'],
    $font_rgb['green'],$font_rgb['blue']) ;   
if ( $shadow ) {
    $box_width = abs( $box[2] - $box[0]);
    $box_height = abs( $box[5] - $dip );
    $shadow_image = @ImageCreate(abs($box[2]-$box[0]),abs($box[5]-$dip)) ;
    $shadow_background_color = @ImageColorAllocate($shadow_image,$background_rgb['red'],
        $background_rgb['green'],$background_rgb['blue']) ;
    $shadow_color = ImageColorAllocate( $shadow_image, 0, 0, 0 );
    $shadow_copy = ImageCopy( $shadow_image, $image, 0, 0, 0, 0, $box_width, $box_height );
    if ( $kerning ) {
        kern_text( $shadow_image, $font_size, 0, ceil( $font_size*.05)-$box[0], abs( $box[5] - $box[3])-$box[1]+ceil( $font_size*.05), $shadow_color, $font_file, $text, $kerning ) ;
    } else {
        ImageTTFText($shadow_image,$font_size,0,
            ceil( $font_size*.05)-$box[0],
            abs($box[5]-$box[3])-$box[1]+ceil( $font_size*.05),
            $shadow_color, $font_file,$text) ;
    }
    if($transparent_background) 
        ImageColorTransparent($shadow_image,$shadow_background_color) ;
    $shadow_merge = ImageCopyMerge( $image, $shadow_image, 0, 0, 0, 0, $box_width, $box_height, 15);
    ImageDestroy( $shadow_image );
}
kern_text($image,$font_size,0,-$box[0],abs($box[5]-$box[3])-$box[1],
    $font_color,$font_file,$text, $kerning ) ;
/*
ImageTTFText($image,$font_size,0,-$box[0],abs($box[5]-$box[3])-$box[1],
    $font_color,$font_file,$text) ;
*/

// set transparency
if($transparent_background) {
    ImageColorTransparent($image,$background_color) ;
}

header('Content-type: ' . $mime_type) ;
ImagePNG($image) ;

// save copy of image for cache
if($cache_images)
{
    ImagePNG($image,$cache_filename ) ;
}

ImageDestroy($image) ;
exit ;


/*
	try to determine the "dip" (pixels dropped below baseline) of this
	font for this size.
*/
function get_dip($font,$size)
{
	$test_chars = 'abcdefghijklmnopqrstuvwxyz' .
			      'ABCDEFGHIJKLMNOPQRSTUVWXYZ' .
				  '1234567890' .
				  '!@#$%^&*()\'"\\/;.,`~<>[]{}-+_-=' ;
	$box = @ImageTTFBBox($size,0,$font,$test_chars) ;
	return $box[3] ;
}


/*
    attempt to create an image containing the error message given. 
    if this works, the image is sent to the browser. if not, an error
    is logged, and passed back to the browser as a 500 code instead.
*/
function fatal_error($message)
{
    // send an image
    if(function_exists('ImageCreate'))
    {
        $width = ImageFontWidth(5) * strlen($message) + 10 ;
        $height = ImageFontHeight(5) + 10 ;
        if($image = ImageCreate($width,$height))
        {
            $background = ImageColorAllocate($image,255,255,255) ;
            $text_color = ImageColorAllocate($image,0,0,0) ;
            ImageString($image,5,5,5,$message,$text_color) ;    
            header('Content-type: image/png') ;
            ImagePNG($image) ;
            ImageDestroy($image) ;
            exit ;
        }
    }

    // send 500 code
    header("HTTP/1.0 500 Internal Server Error") ;
    print($message) ;
    exit ;
}


/* 
    decode an HTML hex-code into an array of R,G, and B values.
    accepts these formats: (case insensitive) #ffffff, ffffff, #fff, fff 
*/    
function hex_to_rgb($hex)
{
    // remove '#'
    if(substr($hex,0,1) == '#')
        $hex = substr($hex,1) ;

    // expand short form ('fff') color
    if(strlen($hex) == 3)
    {
        $hex = substr($hex,0,1) . substr($hex,0,1) .
               substr($hex,1,1) . substr($hex,1,1) .
               substr($hex,2,1) . substr($hex,2,1) ;
    }

    if(strlen($hex) != 6)
        fatal_error('Error: Invalid color "'.$hex.'"') ;

    // convert
    $rgb['red'] = hexdec(substr($hex,0,2)) ;
    $rgb['green'] = hexdec(substr($hex,2,2)) ;
    $rgb['blue'] = hexdec(substr($hex,4,2)) ;

    return $rgb ;
}


/*
    convert embedded, javascript unicode characters into embedded HTML
    entities. (e.g. '%u2018' => '&#8216;'). returns the converted string.
*/
function javascript_to_html($text)
{
    $matches = null ;
    preg_match_all('/%u([0-9A-F]{4})/i',$text,$matches) ;
    if(!empty($matches)) for($i=0;$i<sizeof($matches[0]);$i++)
        $text = str_replace($matches[0][$i],
                            '&#'.hexdec($matches[1][$i]).';',$text) ;

    return $text ;
}

function kern_text( &$image, $font_size, $angle, $x, $y, $color, $font_file, $text, $kerning ) {
    if ( $kerning == 0 ) {
        return ImageTTFText($image,$font_size,$angle,
            $x, $y, $color, $font_file, $text );
    }
    $len = strlen( $text );
    $spot = 0;
    for( $spot = 0; $spot<$len; $spot++ ) {
        $bbox = ImageTTFText( $image, $font_size, $angle, $x, $y, $color, $font_file, substr( $text, $spot, 1));
        $x = $bbox[2] + $kerning;
    }
}

?>
