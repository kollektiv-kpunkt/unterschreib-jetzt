<?php
global $wpdb;
require_once(__DIR__ . "/../../../../../wp-load.php");

$type = $_POST["type"];



if ($type == "sheet") {
    $sheet = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}sheets` WHERE `sheet_UUID` = %s;", $_POST["uuid"] ));
    $bogen = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}bogens` WHERE `bogen_UUID` = %s;", $sheet->sheet_BogenID ));

    $bogen_returned = $bogen->bogen_returned - $sheet->sheet_Nosig;

    if ($bogen_returned <= $bogen->bogen_nosig) {
        $bogen_notreturned = $bogen->bogen_returned - $sheet->sheet_Nosig;
    } else {
        $bogen_notreturned = 0;
    }

    $query = 
        $wpdb->query( 
            $wpdb->prepare( 
                "UPDATE `{$wpdb->prefix}bogens` SET
                    `bogen_returned` = %d,
                    `bogen_notreturned` = %d
                WHERE `bogen_UUID` = %s;",
                $bogen_returned,
                $bogen_notreturned,
                $sheet->sheet_BogenID
            )
        )
    ;
    
    $query = 
        $wpdb->query( 
            $wpdb->prepare( 
                "DELETE from `{$wpdb->prefix}sheets` WHERE `sheet_UUID` = %s;",
                $_POST["uuid"]
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
            "text" => "Sheet #" . $_POST["uuid"] . " successfully deleted!",
            "type" => "success",
            "returned" => $bogen_returned,
            "notreturned" => $bogen_notreturned
        );
        header('Content-type: application/json');
        echo(json_encode($return));
    }
}


?>