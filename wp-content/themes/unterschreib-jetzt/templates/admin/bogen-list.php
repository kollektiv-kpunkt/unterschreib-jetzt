<?php

global $wpdb;

$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}bogens");

?>
<p class="h1 mb-4">Pledges</p>
<table id="bogen-list" class="table table-striped table-bordered" style="width: 100%">
    <thead>
        <tr>
            <th scope="col">Bogen ID</th>
            <th scope="col">Timestamp</th>
            <th scope="col">First name</th>
            <th scope="col">Last name</th>
            <th scope="col">E-Mail</th>
            <th scope="col">Phone</th>
            <th scope="col">Address</th>
            <th scope="col">PLZ</th>
            <th scope="col">Ort</th>
            <th scope="col">Returned</th>
            <th scope="col">Missing</th>
            <th scope="col">Pledge</th>
        </tr>
    </thead>
    <tbody>
    <?php

    foreach ($results as $result) : ?>
        <tr>
            <th scope="row"><a href="?view=bogen&uuid=<?= $result->bogen_UUID?>"><?= $result->bogen_UUID ?></a></th>
            <td><?= $result->bogen_timestamp?></td>
            <td><?= $result->bogen_fname?></td>
            <td><?= $result->bogen_lname?></td>
            <td><?= $result->bogen_email?></td>
            <td><?= $result->bogen_phone?></td>
            <td><?= $result->bogen_address?></td>
            <td><?= $result->bogen_plz?></td>
            <td><?= $result->bogen_ort?></td>
            <td><?= $result->bogen_returned?></td>
            <td><?= $result->bogen_notreturned?></td>
            <td><?= $result->bogen_nosig ?></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>

<script>
jQuery(document).ready( function () {
    jQuery('#bogen-list').DataTable( {
        "pagingType": "full_numbers",
        searchPanes:{
            cascadePanes: true,
            viewTotal: true,
        },
        "columnDefs": [
            { "visible": false, "targets": [1, 4, 5, 6, 7, 8, 9, 10]}
        ],
        buttons: [
            'colvis',
            'csv',
            'excel'
        ],
        "order": [[ 0, 'asc' ]],
        dom: 
            "<'row'<'col-sm-12 col-md-6'Q><'col-sm-12 col-md-6'>>" +
            "<'row'<'col-sm-12 col-md-6 mb-4'B><'col-sm-12 col-md-6'>>" +
            "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });
} );
</script>