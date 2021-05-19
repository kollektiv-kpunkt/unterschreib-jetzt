jQuery("#mobile-fab").click(function(){
    jQuery("aside").addClass("form");
    jQuery("html").addClass("noscroll");
})

jQuery(".close-icon").click(function(){
    jQuery("aside").removeClass("form");
    jQuery("html").removeClass("noscroll");
})