<?php

if (isset($_GET["uuid"])) {
    $uuid = $_GET["uuid"];
    $bogen = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}bogens` WHERE `bogen_UUID` = %s;", $uuid ));
    $sheetID = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}sheets ORDER BY `sheet_ID` DESC")->sheet_ID + 1;
}

?>


<p class="h1">Add sheet #<span id="sheet_ID_title"><?= $sheetID ?></span></p>
<form id="register-form" style="max-width: 15cm; margin-top: 2rem">
    <div class="form-group">
      <label for="sheet_BogenID">Bogen ID</label>
      <input type="text" class="form-control" id="sheet_BogenID" name="sheet_BogenID" value="<?php print (isset($uuid)) ? $uuid : "" ?>" readonly>
      <small class="form-text text-muted">This field is field out automatically.</small>
    </div>
    <div class="form-group">
      <label for="sheet_PLZ">PLZ</label>
      <input type="text" class="form-control" id="sheet_PLZ" name="sheet_PLZ" value="<?php print (isset($bogen->bogen_plz)) ? $bogen->bogen_plz : "" ?>" required>
    </div>
    <div class="form-group">
      <label for="sheet_Nosig">Number of signatures</label>
      <input type="number" class="form-control" id="sheet_Nosig" name="sheet_Nosig" required>
    </div>
    <div class="form-group">
      <label for="sheet_ID">Sheet ID</label>
      <input type="text" class="form-control" id="sheet_ID" name="sheet_ID" value="<?= $sheetID ?>" readonly>
      <small class="form-text text-muted">If you want to be able to identify this sheet later, please write this number on it.</small>
    </div>
    <input type="hidden" name="sheet_User" value="<?= wp_get_current_user()->ID ?>">
    <input type="hidden" name="sheet_UUID" value="<?= uniqid("sheet_") ?>">
    <button type="submit" class="btn btn-primary mr-3">Register</button>
</form>

<script>

jQuery("#register-form").submit(function(e){
    e.preventDefault();
    var formData = jQuery(this).serialize();
    var form = jQuery(this);
    jQuery.ajax({
        url : "/wp-content/themes/unterschreib-jetzt/templates/admin/submit-sheet.php",
        type: "POST",
        data : formData,
        success: function(response, textStatus, jqXHR) {
            var notyf = new Notyf();
            if (response.type == "error") {
                notyf.error(response.text);
            } else if (response.type == "success") {
                notyf.success(response.text);
                form.trigger("reset");
                var nextID = parseInt(jQuery("#sheet_ID").val()) + 1;
                jQuery("#sheet_ID").val(nextID);
                jQuery("#sheet_ID_title").text(nextID);
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