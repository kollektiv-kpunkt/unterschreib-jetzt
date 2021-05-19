jQuery(".signform").submit(function(e) {
    e.preventDefault();

    var nextStep = parseInt(jQuery(this).attr("data-step")) + 1;
    jQuery("aside").load(`wp-content/themes/unterschreib-jetzt/form-steps/step-${nextStep}.php`);
})

document.body.addEventListener("change", function(){
    jQuery(".signform").submit(function(e) {
        e.preventDefault();
        var nextStep = parseInt(jQuery(this).attr("data-step")) + 1;
        jQuery("aside").load(`wp-content/themes/unterschreib-jetzt/form-steps/step-${nextStep}.php`);
    })
})