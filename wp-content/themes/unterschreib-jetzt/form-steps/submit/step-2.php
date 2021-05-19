<?php
global $wpdb;
require_once(__DIR__ . "/../../../../../wp-load.php");
global $i18n;
include __DIR__ . "/../../i18n/de.php";

$prefix = $wpdb->prefix;

$uuid = $_POST["uuid"];
$address = $_POST["address"];
$plz = $_POST["plz"];
$place = $_POST["place"];
$birthday = $_POST["birthday"];
if (isset($_POST["nosig"]) && $_POST["nosig"] != "") {
    $nosig = $_POST["nosig"];
} else {
    $nosig = 1;
}
if (isset($_POST["drucker"])) {
    $drucker = 1;
} else {
    $drucker = 0;
}

$query = 
    $wpdb->query( 
        $wpdb->prepare( 
            "UPDATE `{$prefix}bogens` SET 
                `bogen_address` = %s,
                `bogen_plz` = %s,
                `bogen_ort` = %s,
                `bogen_birthday` = %s,
                `bogen_drucker` = %s,
                `bogen_nosig` = %s,
                `bogen_notreturned` = %s
            WHERE `bogen_UUID` = %s;",
            $address,
            $plz,
            $place,
            $birthday,
            $drucker,
            $nosig,
            $nosig,
            $uuid
        )
    )
;

$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$prefix}bogens` WHERE `bogen_UUID` = %s;", $uuid ), ARRAY_A );

if ($query != 1) {
    $return = array(
        "status" => 501,
        "text" => $i18n["error"],
        "type" => "error"
    );
    header('Content-type: application/json');
    echo(json_encode($return));
    exit;
} else {
    $return = array(
        "status" => 200,
        "text" => "",
        "type" => "success",
        "uuid" => $row["bogen_UUID"],
        "fname" => $row["bogen_fname"],
        "bogenDetails" => $row
    );
    header('Content-type: application/json');
    echo(json_encode($return));
}