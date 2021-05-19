var submitForm = function submitForm() {
    jQuery(".signform").submit(function(e) {
        e.preventDefault();
        var form = jQuery(this);
        var formData = jQuery(this).serialize()
        var thisStep = parseInt(jQuery(this).attr("data-step"));
        var nextStep = thisStep + 1;
        form.children(".lds-ellipsis").addClass("show")

        setTimeout(() => {
            jQuery.ajax({
                url : `wp-content/themes/unterschreib-jetzt/form-steps/submit/step-${thisStep}.php`,
                type: "POST",
                data : formData,
                  async : false,
                success: function(response) {
                    if (response.type == "error") {
                        setTimeout(() => {
                            form.children(".form-alert").text(response.text);
                            form.children(".form-alert").addClass(response.type);
                            form.children(".form-alert").addClass("show");
                            form.children(".lds-ellipsis").removeClass("show");
                        }, 2000);
                    } else if (response.type == "success") {
                        jQuery("#ajax").load(`wp-content/themes/unterschreib-jetzt/form-steps/step-${nextStep}.php`, function(){
                            if (thisStep == 1) {
                                jQuery("input[name='uuid']").val(response.uuid);
                                if (response.noprint == 0) {
                                    jQuery("#noprint-group").remove();
                                }
                            } else if (thisStep == 2) {
                                jQuery.ajax({
                                    type: "POST",
                                    url: `wp-content/themes/unterschreib-jetzt/form-steps/submit/interface.php`,
                                    data: { uuid: response.uuid },
                                    success: function(re) {
                                        console.log(re)
                                    }
                                });
                                jQuery("#fname").text(response.fname);
                            }
                        });
                    }
                    console.log(response)
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                      console.log(textStatus);
                      console.log(errorThrown);
                }
            });
        }, 500);

    })
}

window.addEventListener("load", submitForm, false);
document.body.addEventListener("change", submitForm, false);