<?php

class Image_Upload {

    var $extension;
    var $name;
    var $pic;
    var $thumb;
    var $original;
    var $imgpaths=array('thumb'=>'img/thumb/', 'pic'=>'img/pic/', 'original'=>'img/original/');
    var $long_image_width=150;
    var $wide_image_width=200;
    var $thumb_width=50;
    var $error;

    function Image_Upload ($filename) {
        
        $dotpoint = strrpos ($filename, ".");
        $name = basename ($filename);
        $this->name = substr( $name, 0, $dotpoint);
        
        if ($this->original=imagecreatefromjpeg($filename)) {
            $this->extension='jpg';
        } elseif ( $this->original=imagecreatefromgif($filename)) {
            $this->extension='gif';
        } elseif ($this->original=imagecreatefrompng($filename)) {
            $this->extension='png';
        } else {
            return false;
        }
        $this->getImgSettings();

    }
    function saveImagesAMP() {
        if ($this->saveImage ($this->original, 'original')) {
            if ($this->saveImage ($this->pic, 'pic')) {
                if ($this->saveImage ($this->thumb, 'thumb')) {
                    return true;
                }
            }
        }
        return false;
    }

    function saveImage($img, $version='original') {
        $type =$this->extension;
        if ($type =="jpg") $type="jpeg";
        $imgpath=$this->imgpaths[$version].$this->name.".".$this->extension;
        if (file_exists($imgpath)) {
            $this->error.="File already exists: ".$imgpath."<BR>";
            return false;
        }
        $write_function="image".$type;
        if ($write_function($img, $imgpath)) {
            chmod ($imgpath, 0755);
            return true;
        } else {
            $this->error.= "Failed to write $imgpath<BR>";
            return false;
        }
   }
   
            

    function getImgSettings() {
        if ($imgset=$dbcon->GetRow("SELECT thumb, optw, optl FROM sysvar where id =1")) {
            $this->thumb_width=$imgset['thumb'];
            $this->long_image_width=$imgset['optl'];
            $this->wide_image_width=$imgset['optw'];
        } else {
            $this->error="Failed to load Image Settings: ".$dbcon->ErrorMsg();
        }
    }

    /* Create a resized version of the image
    */

    function resize($newwidth=null, $newheight=null, $image=null) {
        if (!isset($image)) $image=&$this->original;
        $y_orig=imagesy($image);
        $x_orig=imagesx($image);
        if (is_numeric($newheight)) {
            if (!is_numeric($newwidth)) {
                $aspect_ratio = $y_orig / $newheight;
                $newwidth = $x_orig / $aspect_ratio;
            }
        } else {
            if (is_numeric($newwidth)) {
                $aspect_ratio = $x_orig / $newwidth;
                $newheight= $y_orig / $aspect_ratio;
            }
        }
        if (is_numeric($newwidth) && is_numeric($newheight)) {
            $dest = $this->_image_transfer($image, $newwidth, $newheight);
            return $dest;
        } else {
            return false;
        }
    }

    /* Internal Resize Function
    ** Checks for the existence of ImageCreateTrueColor
    ** which is only present with GD 2.0 or higher
    */
   
    function _image_transfer($image, $newwidth, $newheight ) {
        $y_orig=imagesy($image);
        $x_orig=imagesx($image);
        if (function_exists('ImageCreateTrueColor') && $this->extension!="gif") {
            $destimage = ImageCreateTrueColor($newwidth,$newheight);
            ImageCopyResampled($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$x_orig,$y_orig); 
        } else {
            $destimage = ImageCreate($newwidth,$newheight);
            ImageCopyResized($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$x_orig,$y_orig); 
        }
        return $destimage;
    }

    function makethumb($image=null) {
        if (isset($image)) $this->original=$image;
        $this->thumb=$this->resize($this->thumb_width);
    }
    function makepic ($image=null) {
        if (isset($image)) $this->original=$image;
        $y_orig=imagesy( $image );
        $x_orig=imagesx( $image );

        if ( $x_orig > $y_orig ) {
            $newwidth = $this->tall_image_width;
        } else {
            $newwidth = $this->wide_image_width;
        }
        
        $this->pic=$this->resize($newwidth);
    }
            
}

?>
