<?php

global $wpdb;
require_once(__DIR__ . "/../../../../../wp-load.php");

if (!get_option("mc_prefix")) {
    add_option("mc_api", $_POST["mc_api"]);
    add_option("mc_prefix", $_POST["mc_prefix"]);
    add_option("mc_listID", $_POST["mc_listID"]);
} else {
    if (get_option("mc_api") != $_POST["mc_api"]) {
        update_option("mc_api", $_POST["mc_api"]);
    }
    if (get_option("mc_prefix") != $_POST["mc_prefix"]) {
        update_option("mc_prefix", $_POST["mc_prefix"]);
    }
    if (get_option("mc_listID") != $_POST["mc_listID"]) {
        update_option("mc_listID", $_POST["mc_listID"]);
    }
}

$mc_api = $_POST["mc_api"];
$mc_prefix = $_POST["mc_prefix"];
$mc_listID = $_POST["mc_listID"];

$bogens = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}bogens`"));

require_once(__DIR__ . '/../../vendor/autoload.php');

$client = new MailchimpMarketing\ApiClient();

$client->setConfig([
  'apiKey' => $mc_api,
  'server' => $mc_prefix
]);

$added = 0;
$updated = 0;
$errors = 0;
$errorText = array();

foreach ($bogens as $bogen):

    try {
        $client->lists->setListMember($mc_listID, md5(strtolower($bogen->bogen_email)), [
            "email_address" => $bogen->bogen_email,
            'merge_fields' => [
                "FNAME" => $bogen->bogen_fname,
                "LNAME" => $bogen->bogen_lname,
                "ADDRESS" => array(
                    "addr1" => $bogen->bogen_address,
                    "city" => $bogen->bogen_ort,
                    "zip" => $bogen->bogen_plz,
                    "country" => 'CH'
                ),
                "PHONE" => $bogen->bogen_phone
            ],
            "status_if_new" => "subscribed",
        ]);
        $added++;
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $error_response = $e;
        array_push($errorText, $error_response);
        $errors++;
    }

endforeach;

$return = array(
    "status" => 200,
    "text" => "{$added} records added/updated, {$errors} errors thrown",
    "type" => "success",
    "errors" => $errorText
);
header('Content-type: application/json');
echo(json_encode($return));

?>