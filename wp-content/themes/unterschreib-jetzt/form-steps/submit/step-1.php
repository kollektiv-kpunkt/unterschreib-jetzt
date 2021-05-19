<?php
global $wpdb;
require_once(__DIR__ . "/../../../../../wp-load.php");
global $i18n;
include __DIR__ . "/../../i18n/de.php";

$prefix = $wpdb->prefix;

$uuid = $_POST["uuid"];
$postID = $_POST["postID"];
$fname = $_POST["fname"];
$lname = $_POST["lname"];
$email = $_POST["email"];
if (isset($_POST["phone"]) && $_POST["phone"] != "") {
    $phone = $_POST["phone"];
} else {
    $phone = "No phone number";
}

if (isset($_POST["optin"])) {
    $optin = 1;
} else {
    $optin = 0;
}

if (get_field("noprint", $postID) == []) {
    $print = 0;
} else {
    $print = 1;
}


$query = 
    $wpdb->query( 
        $wpdb->prepare( 
            "INSERT INTO `{$prefix}bogens` 
                (`bogen_UUID`, `bogen_postID`, `bogen_fname`, `bogen_lname`, `bogen_email`, `bogen_phone`, `bogen_optin`) 
            VALUES 
                (%s, %s, %s, %s, %s, %s, %s)
            ON DUPLICATE KEY UPDATE `bogen_UUID` = `bogen_UUID`;",
            $uuid,
            $postID,
            $fname,
            $lname,
            $email,
            $phone,
            $optin
        )
    )
;

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
        "noprint" => $print,
        "uuid" => $uuid
    );
    header('Content-type: application/json');
    echo(json_encode($return));
}

?>