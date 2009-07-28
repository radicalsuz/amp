/*
 * Lazy Load - jQuery plugin for lazy loading images
 *
 * Copyright (c) 2007-2009 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   http://www.appelsiini.net/projects/lazyload
 *
 * Version:  1.3.2
 *
 */
(function($) {

    $.fn.lazyload = function(options) {
        var settings = {
            threshold    : 0,
            failurelimit : 100,
            event        : "scroll",
            effect       : "show",
            container    : window,
            top_fold 	 : false
        };
                
        if(options) {
            $.extend(settings, options);
        }

        var elements = this;
		var lazy_load_action = 
		    function(event) {
                elements.each(function() {
                    if (!$.belowthefold(this, settings) &&
						!$.abovethetopfold(this, settings) &&
						$(this).is(":visible") &&
                        !$.rightoffold(this, settings)) {
                            $(this).trigger("appear");
                    } 
                });
                /* Remove image from array so it is not looped next time. */
                var temp = $.grep(elements, function(element) {
                    return !element.loaded;
                });
                elements = $(temp);
            };

		var scroll_timeout = false;
	    var scroll_response = function() {
				if (!(scroll_timeout === undefined)) {
				  clearTimeout(scroll_timeout);
				}
				scroll_timeout = setTimeout( function() { lazy_load_action(); }, 500);
			}
        /* Fire one scroll event per scroll. Not one scroll event per image. */
        if ("scroll" == settings.event) {
            $(settings.container).bind("scroll", scroll_response );
            $(settings.container).bind("resize", scroll_response );
        }
        
        return this.each(function() {
            var self = this;
			if( $(self).attr('original') === undefined ) {
				$(self).attr("original", $(self).attr("src"));
			}
            if ("scroll" != settings.event 
                         || $.abovethetopfold(self, settings) 
                         || $.belowthefold(self, settings) 
                         || $.rightoffold(self, settings)) {
                if (settings.placeholder) {
                    $(self).attr("src", settings.placeholder);      
                } else {
                    //$(self).removeAttr("src");
                }
                self.loaded = false;
            } else {
				if( $(self).attr('src') !== $(self).attr('original')) {
				  $(self).attr("src", $(self).attr("original")).removeClass('lazyload-empty');
				}
                self.loaded = true;
            }
            
            $(self).one("appear", function() {
                if (!this.loaded) {
					$(this).addClass("lazyload-waiting");
                    $("<img />")
                        .bind("load", function() {
                            $(self)
                                .hide()
                                .attr("src", $(self).attr("original"))
                                [settings.effect](settings.effectspeed)
							    .removeClass("lazyload-empty lazyload-waiting");
                            self.loaded = true;
                        })
                        .attr("src", $(self).attr("original"));
                };
            });

            if ("scroll" != settings.event) {
                $(self).bind(settings.event, function(event) {
                    if (!self.loaded) {
                        $(self).trigger("appear");
                    }
                });
            }
        }); 

    };

    /* Convenience methods in jQuery namespace.           */
    /* Use as  $.belowthefold(element, {threshold : 100, container : window}) */

    $.belowthefold = function(element, settings) {
        if (settings.container === undefined || settings.container === window) {
            var fold = $(window).height() + $(window).scrollTop();
        }
        else {
            var fold = $(settings.container).offset().top + $(settings.container).height();
        }
		return fold <= $(element).offset().top - settings.threshold;
    };
    
    $.abovethetopfold = function(element, settings) {
        if (settings.container === undefined || settings.container === window) {
            var top_fold = $(window).scrollTop();
        }
        else {
            var top_fold = $(settings.container).offset().top + $(settings.container).scrollTop();
        }
		var bottom_edge = $(element).offset().top + $(element).height() + settings.threshold;
		return top_fold >= bottom_edge ;
    };

    $.rightoffold = function(element, settings) {
        if (settings.container === undefined || settings.container === window) {
            var fold = $(window).width() + $(window).scrollLeft();
        }
        else {
            var fold = $(settings.container).offset().left + $(settings.container).width();
        }
        return fold <= $(element).offset().left - settings.threshold;
    };
    
    /* Custom selectors for your convenience.   */
    /* Use as $("img:below-the-fold").something() */

    $.extend($.expr[':'], {
        "outside-viewport" : "$.abovethetopfold(a, {threshold : 0, container: window}) || $.belowthefold(a, {threshold : 0, container: window } )",
        "above-the-top-fold" : "$.abovethetopfold(a, {threshold : 0, container: window})",
        "below-the-fold" : "$.belowthefold(a, {threshold : 0, container: window})",
        "above-the-fold" : "!$.belowthefold(a, {threshold : 0, container: window})",
        "right-of-fold"  : "$.rightoffold(a, {threshold : 0, container: window})",
        "left-of-fold"   : "!$.rightoffold(a, {threshold : 0, container: window})"
    });
    
})(jQuery);
