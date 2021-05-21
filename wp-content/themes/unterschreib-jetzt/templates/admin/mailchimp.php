<p class="h1">Sync with Mailchimp</p>
<form id="mailchimp-form" style="max-width: 15cm; margin-top: 2rem">
    <div class="form-group">
      <label for="mc_api">Mailchimp API Key</label>
      <input type="text" class="form-control" id="mc_api" name="mc_api" value="<?= get_option("mc_api") ?>" required>
      <small class="form-text text-muted">Please insert the Mailchimp API Key you can find in your profile.</small>
    </div>
    <div class="form-group">
      <label for="mc_prefix">Mailchimp Server Prefix</label>
      <input type="text" class="form-control" id="mc_prefix" name="mc_prefix" value="<?= get_option("mc_prefix") ?>" required>
      <small class="form-text text-muted">Please insert the Mailchimp Server preifx (visible in subdomain name).</small>
    </div>
    <div class="form-group">
      <label for="mc_listID">Mailchimp List ID</label>
      <input type="text" class="form-control" id="mc_listID" name="mc_listID" value="<?= get_option("mc_listID") ?>" required>
      <small class="form-text text-muted">Please insert the Mailchimp List ID you want to sync.</small>
    </div>
    <input type="hidden" name="sync_tag" value="<?= date("Ymd_Hi") ?>">
    <button type="submit" class="btn btn-primary mr-3">Sync</button>
    <div class="lds-ellipsis" style="display: block"><div></div><div></div><div></div><div></div></div>
</form>

<style>
    .lds-ellipsis div {
        background: var(--black);
    }
</style>

<script>

jQuery("#mailchimp-form").submit(function(e){
    e.preventDefault();
    var formData = jQuery(this).serialize();
    var form = jQuery(this);
    form.children(".lds-ellipsis").addClass("show")
    jQuery.ajax({
        url : "/wp-content/themes/unterschreib-jetzt/templates/admin/submit-mc.php",
        type: "POST",
        data : formData,
        success: function(response, textStatus, jqXHR) {
            var notyf = new Notyf({
                duration : 6000
            });
            if (response.type == "error") {
                notyf.error(response.text);
                form.children(".lds-ellipsis").removeClass("show");
            } else if (response.type == "success") {
                notyf.success(response.text);
                form.children(".lds-ellipsis").removeClass("show");
                console.log(response.errors)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
})

</script>