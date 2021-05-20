<?php
$uuid = $_GET["uuid"];
$bogen = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}bogens` WHERE `bogen_UUID` = %s;", $uuid ));

$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}sheets WHERE sheet_BogenID = {$bogen->bogen_UUID}");
?>

<p class="lead" style="margin-bottom: 0"><em>UUID: <?= $bogen->bogen_UUID ?></em></p>
<p class="h1"><?= "{$bogen->bogen_fname} {$bogen->bogen_lname}" ?></p>

<table style="width: 100%; max-width: 600px; margin-top: 2rem; margin-bottom: 2rem">
    <tr>
        <td><b>Firstname:</b></td>
        <td><?= $bogen->bogen_fname ?></td>
    </tr>
    <tr>
        <td><b>Lastname:</b></td>
        <td><?= $bogen->bogen_lname ?></td>
    </tr>
    <tr>
        <td><b>E-Mail:</b></td>
        <td><a href="mailto: <?= $bogen->bogen_email ?>"><?= $bogen->bogen_email ?></a></td>
    </tr>
    <tr>
        <td><b>Phone:</b></td>
        <td><?= $bogen->bogen_phone ?></td>
    </tr>
    <tr>
        <td><b>Pledge:</b></td>
        <td><?= $bogen->bogen_nosig ?></td>
    </tr>
    <tr>
        <td><b>Returned:</b></td>
        <td><?= $bogen->bogen_returned ?></td>
    </tr>
</table>

<a href="?view=register&uuid=<?= $bogen->bogen_UUID ?>" id="add-bogen" class="btn btn-success">Add Sheet</a>

<p class="h3 mt-5 mb-4">Sheets</p>

<table id="sheet-list" class="table table-striped table-bordered" style="width: 100%;">
    <thead>
        <tr>
            <th scope="col">Sheet ID</th>
            <th scope="col">Signatures</th>
            <th scope="col">User</th>
        </tr>
    </thead>
    <tbody>
    <?php

    foreach ($results as $result) : ?>
        <tr>
            <th scope="row"><?= $result->sheet_ID ?></th>
            <td><?= $result->sheet_Nosig?></td>
            <td><?= get_userdata($result->sheet_User)->user_login?></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>

<script>
jQuery(document).ready( function () {
    jQuery('#sheet-list').DataTable( {
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
</script>