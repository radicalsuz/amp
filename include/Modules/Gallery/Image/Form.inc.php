<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/Gallery/Image/ComponentMap.inc.php');

class GalleryImage_Form extends AMPSystem_Form_XML {

    function GalleryImage_Form( ){
        $name = "galleryImages";
        $this->init( $name, 'POST', 'gallery_image.php' );
    }

    function setDynamicValues( ){
        $this->addTranslation( 'date', '_makeDbDateTime', 'get');
    }

    function _initJavascriptActions( ){
        $header = &AMP_getHeader( );
        $this->_initPhotoLookup( $header );
    }

    function _initPhotoLookup( $header ) {
        $script = <<<PHOTOCODE
document.forms['galleryImages'].elements['img'].observe( 'change', function( ) {
    var picture_filename = document.forms['galleryImages'].elements['img'].value;
    var photo_data = photo_data_maker( );

    new Ajax.Request( 
        '/system/image_manager.php', 
        { onSuccess:photo_data.update,
          parameters: {
              action:"read",
              id: picture_filename
          },
          method: 'GET'
                
        });
});

function photo_data_maker( ) {
    return {

    update: function( response ) {
        var json_object = eval( response.getResponseHeader( 'X-JSON'));
        //alert( this.form.id );
        if( !( json_object.author || json_object.caption || json_object.date ) ) {
            $( 'picture_data').update( 'No Image Data Found');
            return;
        }
        

        go_button = document.createElement( 'input');
        go_button.type = 'button';
        go_button.value = "Use These";
        go_button.style.className = 'photo_data_activate';
        var display_value = '';
        if( json_object.caption != undefined ) {
            display_value += "Caption:"+ json_object.caption + "<br />";
        }
        if ( json_object.author != undefined ) {
            display_value += "Credit: " + json_object.author + "<br />";
        }
        if ( json_object.date != undefined && json_object.date != "0000-00-00") {
            display_value += "Date: " + json_object.date + "<br />" ;

        }
        display_value = "<div class=photocaption>" + display_value + "</div>"; 
        $( 'picture_data').update( display_value );
        $( 'picture_data').appendChild( go_button );
        AMP_show_panel( 'picture_data');
        
        go_button.observe( 'click', function( ) {
            AMP_show_panel( 'galleryImages');
            if( json_object.caption != undefined ) {
                document.forms['galleryImages'].elements['caption'].value = json_object.caption;
            }

            if( json_object.author != undefined ) {
                $( 'galleryImages').photoby.value = json_object.author;
            }

            if ( json_object.date != undefined && json_object.date != "0000-00-00") {
                var date_value = json_object.date.split( '-');
                $( 'galleryImages').elements['date[Y]'].value = date_value[0];
                $( 'galleryImages').elements['date[d]'].value = date_value[2].replace( /^0/, '');
                $( 'galleryImages').elements['date[M]'].value = date_value[1].replace( /^0/, '') ;

            }
            $( 'picture_data').update( '');

        });
        
    }
    };
}
PHOTOCODE;
        $header->addJavascriptOnload(  $script, 'photodata' );

    }

}

?>
