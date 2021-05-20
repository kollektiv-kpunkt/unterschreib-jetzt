<?php
global $wpdb;
require_once(__DIR__ . "/../../../../../wp-load.php");

$wpdb->show_errors();
$query = 
    $wpdb->query( 
        $wpdb->prepare( 
            "INSERT INTO `{$wpdb->prefix}sheets` 
                (`sheet_ID`, `sheet_UUID`, `sheet_BogenID`, `sheet_PLZ`, `sheet_Nosig`, `sheet_User`) 
            VALUES 
                (%s, %s, %s, %s, %s, %s)
            ON DUPLICATE KEY UPDATE `sheet_UUID` = `sheet_UUID`;",
            $_POST["sheet_ID"],
            $_POST["sheet_UUID"],
            $_POST["sheet_BogenID"],
            $_POST["sheet_PLZ"],
            $_POST["sheet_Nosig"],
            $_POST["sheet_User"],
        )
    )
;

if ($query != 1) {
    $return = array(
        "status" => 501,
        "text" => "Something went wrong, please try again",
        "type" => "error"
    );
    header('Content-type: application/json');
    echo(json_encode($return));
    exit;
} else {
    $return = array(
        "status" => 200,
        "text" => "Sheet #" . $_POST["sheet_ID"] . " successfully registered!",
        "type" => "success"
    );
    header('Content-type: application/json');
    echo(json_encode($return));
}

?>