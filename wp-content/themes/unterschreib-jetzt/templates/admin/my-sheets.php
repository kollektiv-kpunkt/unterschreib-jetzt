<?php

global $wpdb;

$userID = wp_get_current_user()->ID;

$sheets = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}sheets WHERE sheet_User = '{$userID}'");

?>
<p class="h1 mb-4">My Sheets</p>
<table id="my-sheets" class="table table-striped table-bordered" style="width: 100%">
    <thead>
        <tr>
            <th scope="col">Sheet ID</th>
            <th scope="col">Bogen UUID</th>
            <th scope="col">#Signatures on sheet</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
    <?php

    foreach ($sheets as $sheet) : 
        ?>
        <tr id="row-<?= $sheet->sheet_UUID ?>">
            <th scope="row"><?= $sheet->sheet_ID ?></th>
            <td><a href="/administration/?view=bogen&uuid=<?=$sheet->sheet_BogenID?>"><?= $sheet->sheet_BogenID?></a></td>
            <td><?= $sheet->sheet_Nosig?></td>
            <td><a href="#" data-uuid="<?= $sheet->sheet_UUID ?>" class="delete-sheet">Delete</a></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>

<script>
jQuery(document).ready( function () {
    jQuery('#my-sheets').DataTable( {
        "pagingType": "full_numbers",
        searchPanes:{
            cascadePanes: true,
            viewTotal: true,
        },
        "order": [[ 0, 'asc' ]],
        dom: 
            "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });
} );

jQuery(".delete-sheet").click(function(e) {
    e.preventDefault();
    var uuid = jQuery(this).attr("data-uuid");
    var proceed = confirm(`Are you sure you want to delete sheet #${uuid}?`);
    if (proceed) {
        jQuery.ajax({
            url : "/wp-content/themes/unterschreib-jetzt/templates/admin/delete.php",
            type: "POST",
            data : { 
                type: "sheet", 
                uuid: uuid 
            },
            success: function(response, textStatus, jqXHR) {
                var notyf = new Notyf();
                if (response.type == "error") {
                    notyf.error(response.text);
                } else if (response.type == "success") {
                    notyf.success(response.text);
                    jQuery("#row-" + uuid).remove();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }
})

</script>