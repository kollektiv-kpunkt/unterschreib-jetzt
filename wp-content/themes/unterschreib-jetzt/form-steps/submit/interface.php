<?php
global $wpdb;
require_once(__DIR__ . "/../../../../../wp-load.php");
global $i18n;
include __DIR__ . "/../../i18n/de.php";

$uuid = $_POST["uuid"];

$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}bogens` WHERE `bogen_UUID` = %s;", $uuid ), ARRAY_A );

//FPDF
require(__DIR__ . "/../../vendor/fpdf/fpdf.php");
//QR Code Generator
require(__DIR__ . "/../../vendor/qrcode/qrcode.class.php");

$uuid = $row["bogen_UUID"];
$bogen_img = get_field("bogen", $row["bogen_postID"]);

$pre_name = get_field("pre_name", $row["bogen_postID"]);
$pre_birthday = get_field("pre_birthday", $row["bogen_postID"]);
$pre_strasse = get_field("pre_strasse", $row["bogen_postID"]);
$pre_plz = get_field("pre_plz", $row["bogen_postID"]);
$pre_ort = get_field("pre_ort", $row["bogen_postID"]);

$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();
$pdf->Image($bogen_img,0,0,210);
$filename = "bogen-{$uuid}.pdf";
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"];
$qr_url = $actual_link . "administration?view=erfassen&uuid=" . $uuid;
$qrcode = new QRcode($qr_url, 'H');
$qrcode->displayFPDF($pdf, 10, 118, 20);

$pdf->SetFont('Arial','',12);

$pdf->Text($pre_name["x_wert"], $pre_name["y_wert"], ucfirst(strtolower($row["bogen_fname"])) . " " . ucfirst(strtolower($row["bogen_lname"])));
$pdf->Text($pre_birthday["x_wert"], $pre_birthday["y_wert"], date("Y", strtotime($row["bogen_birthday"])));
$pdf->Text($pre_strasse["x_wert"], $pre_strasse["y_wert"], ucfirst(strtolower($row["bogen_address"])));
$pdf->Text($pre_plz["x_wert"], $pre_plz["y_wert"], ucfirst(strtolower($row["bogen_plz"])));
$pdf->Text($pre_ort["x_wert"], $pre_ort["y_wert"], ucfirst(strtolower($row["bogen_ort"])));



$filepath = __DIR__ . "/../../bogen/" . $filename;
$pdf->Output($filepath,'F');
$attachment = $pdf->Output($filename,'S');


// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require(__DIR__ . "/../../vendor/autoload.php");
require(__DIR__ . "/../../config/config.php");

$subjText = get_field("dankesmail_subject", $row["bogen_postID"]); 
$emailText = get_field("dankesemail", $row["bogen_postID"]); 


$tags = array("[fname]", "[lname]", "[nosig]", "[bogenlink]");

$nosig = $row["bogen_nosig"];

if ($row["bogen_nosig"] = 1) {
    $nosig = "eine Unterschrift";
} else {
    $nosig .= " Unterschriften";
}

$replace = array($row["bogen_fname"], $row["bogen_lname"], $nosig, $actual_link . '/wp-content/themes/unterschreib-jetzt/bogen/' . $filename);

$emailSubject = str_replace($tags, $replace, $subjText);
$emailContent = str_replace($tags, $replace, $emailText);

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();
    $mail->Host       = $config["email-host"];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config["email-username"];
    $mail->Password   = $config["email-pw"];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $config["email-port"];

    //Recipients
    $mail->setFrom($config["email-username"], $i18n["email-from"]);
    $mail->addAddress($row["bogen_email"], $row["bogen_fname"] . " " . $row["bogen_lname"]);
    $mail->CharSet  = 'UTF-8'; // the same as 'utf-8'

    // Attachments
    $mail->AddStringAttachment($attachment, $filename);

    // Content
    $mail->isHTML(true);
    $mail->Subject = $emailSubject;
    $mail->Body    = $emailContent;

    $mail->send();
} catch (Exception $e) {
    echo("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
}

?>