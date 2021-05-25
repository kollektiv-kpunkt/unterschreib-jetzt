<?php
global $wpdb;
require_once(__DIR__ . "/../../../../../wp-load.php");
global $i18n;
include __DIR__ . "/../../i18n/de.php";

$uuid = $_POST["uuid"];

$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}bogens` WHERE `bogen_UUID` = %s;", $uuid ), ARRAY_A );

//FPDF
require(__DIR__ . "/../../vendor/fpdf/tfpdf.php");
//QR Code Generator
require(__DIR__ . "/../../vendor/qrcode/qrcode.class.php");

$uuid = $row["bogen_UUID"];

if (!file_exists("./img/bogen-" . $row["bogen_postID"] . ".jpg")) {
    $url = get_field("bogen", $row["bogen_postID"]);
    $imagick = new Imagick();
    $imagick->setResolution(576,576);  
    $imagick->readImage($url);
    $imagick->resizeImage(2480,3508,Imagick::FILTER_CUBIC,1);
    $imagick->setCompressionQuality(80);
    $imagick->setImageFormat('jpg');
    $imagick->writeImage("./img/bogen-" . $row["bogen_postID"] . ".jpg");
}

$pre_name = get_field("pre_name", $row["bogen_postID"]);
$pre_birthday = get_field("pre_birthday", $row["bogen_postID"]);
$pre_strasse = get_field("pre_strasse", $row["bogen_postID"]);
$pre_plz = get_field("pre_plz", $row["bogen_postID"]);
$pre_ort = get_field("pre_ort", $row["bogen_postID"]);

$pdf = new tFPDF('P','mm','A4');
$pdf->AddPage();
$pdf->Image("./img/bogen-" . $row["bogen_postID"] . ".jpg",0,0,210);
$filename = "bogen-{$uuid}.pdf";
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"];
$qr_url = $actual_link . "/administration?view=register&uuid=" . $uuid;
$qrcode = new QRcode($qr_url, 'H');
$qrcode->displayFPDF($pdf, 10, 118, 20);
$pdf->SetFont('Arial','',6);
$pdf->Text(10, 142.5, $uuid);

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

$htmlemail = <<<EOD
    <!doctype html>
    <html>
    <head>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>{$emailSubject}</title>
        <style>
            /* -------------------------------------
                GLOBAL RESETS
            ------------------------------------- */
            
            /*All the styling goes here*/
            
            img {
                border: none;
                -ms-interpolation-mode: bicubic;
                max-width: 100%; 
            }

            body {
                background-color: #f6f6f6;
                font-family: sans-serif;
                -webkit-font-smoothing: antialiased;
                font-size: 14px;
                line-height: 1.4;
                margin: 0;
                padding: 0;
                -ms-text-size-adjust: 100%;
                -webkit-text-size-adjust: 100%; 
            }

            table {
                border-collapse: separate;
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
                width: 100%; }
                table td {
                font-family: sans-serif;
                font-size: 14px;
                vertical-align: top; 
            }

            /* -------------------------------------
                BODY & CONTAINER
            ------------------------------------- */

            .body {
                background-color: #f6f6f6;
                width: 100%; 
            }

            /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
            .container {
                display: block;
                margin: 0 auto !important;
                /* makes it centered */
                max-width: 580px;
                padding: 10px;
                width: 580px; 
            }

            /* This should also be a block element, so that it will fill 100% of the .container */
            .content {
                box-sizing: border-box;
                display: block;
                margin: 0 auto;
                max-width: 580px;
                padding: 10px; 
            }

            /* -------------------------------------
                HEADER, FOOTER, MAIN
            ------------------------------------- */
            .main {
                background: #ffffff;
                border-radius: 3px;
                width: 100%; 
            }

            .wrapper {
                box-sizing: border-box;
                padding: 20px; 
            }

            .content-block {
                padding-bottom: 10px;
                padding-top: 10px;
            }

            .footer {
                clear: both;
                margin-top: 10px;
                text-align: center;
                width: 100%; 
            }
                .footer td,
                .footer p,
                .footer span,
                .footer a {
                color: #999999;
                font-size: 12px;
                text-align: center; 
            }

            /* -------------------------------------
                TYPOGRAPHY
            ------------------------------------- */
            h1,
            h2,
            h3,
            h4 {
                color: #000000;
                font-family: sans-serif;
                font-weight: 400;
                line-height: 1.4;
                margin: 0;
                margin-bottom: 30px; 
            }

            h1 {
                font-size: 35px;
                font-weight: 300;
                text-align: center;
                text-transform: capitalize; 
            }

            p,
            ul,
            ol {
                font-family: sans-serif;
                font-size: 14px;
                font-weight: normal;
                margin: 0;
                margin-bottom: 15px; 
            }
                p li,
                ul li,
                ol li {
                list-style-position: inside;
                margin-left: 5px; 
            }

            a {
                color: #3498db;
                text-decoration: underline; 
            }

            /* -------------------------------------
                BUTTONS
            ------------------------------------- */
            .btn {
                box-sizing: border-box;
                width: 100%; }
                .btn > tbody > tr > td {
                padding-bottom: 15px; }
                .btn table {
                width: auto; 
            }
                .btn table td {
                background-color: #ffffff;
                border-radius: 5px;
                text-align: center; 
            }
                .btn a {
                background-color: #ffffff;
                border: solid 1px #3498db;
                border-radius: 5px;
                box-sizing: border-box;
                color: #3498db;
                cursor: pointer;
                display: inline-block;
                font-size: 14px;
                font-weight: bold;
                margin: 0;
                padding: 12px 25px;
                text-decoration: none;
                text-transform: capitalize; 
            }

            .btn-primary table td {
                background-color: #3498db; 
            }

            .btn-primary a {
                background-color: #3498db;
                border-color: #3498db;
                color: #ffffff; 
            }

            /* -------------------------------------
                OTHER STYLES THAT MIGHT BE USEFUL
            ------------------------------------- */
            .last {
                margin-bottom: 0; 
            }

            .first {
                margin-top: 0; 
            }

            .align-center {
                text-align: center; 
            }

            .align-right {
                text-align: right; 
            }

            .align-left {
                text-align: left; 
            }

            .clear {
                clear: both; 
            }

            .mt0 {
                margin-top: 0; 
            }

            .mb0 {
                margin-bottom: 0; 
            }

            .preheader {
                color: transparent;
                display: none;
                height: 0;
                max-height: 0;
                max-width: 0;
                opacity: 0;
                overflow: hidden;
                mso-hide: all;
                visibility: hidden;
                width: 0; 
            }

            .powered-by a {
                text-decoration: none; 
            }

            hr {
                border: 0;
                border-bottom: 1px solid #f6f6f6;
                margin: 20px 0; 
            }

            /* -------------------------------------
                RESPONSIVE AND MOBILE FRIENDLY STYLES
            ------------------------------------- */
            @media only screen and (max-width: 620px) {
                table[class=body] h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important; 
                }
                table[class=body] p,
                table[class=body] ul,
                table[class=body] ol,
                table[class=body] td,
                table[class=body] span,
                table[class=body] a {
                font-size: 16px !important; 
                }
                table[class=body] .wrapper,
                table[class=body] .article {
                padding: 10px !important; 
                }
                table[class=body] .content {
                padding: 0 !important; 
                }
                table[class=body] .container {
                padding: 0 !important;
                width: 100% !important; 
                }
                table[class=body] .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important; 
                }
                table[class=body] .btn table {
                width: 100% !important; 
                }
                table[class=body] .btn a {
                width: 100% !important; 
                }
                table[class=body] .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important; 
                }
            }

            /* -------------------------------------
                PRESERVE THESE STYLES IN THE HEAD
            ------------------------------------- */
            @media all {
                .ExternalClass {
                width: 100%; 
                }
                .ExternalClass,
                .ExternalClass p,
                .ExternalClass span,
                .ExternalClass font,
                .ExternalClass td,
                .ExternalClass div {
                line-height: 100%; 
                }
                .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important; 
                }
                #MessageViewBody a {
                color: inherit;
                text-decoration: none;
                font-size: inherit;
                font-family: inherit;
                font-weight: inherit;
                line-height: inherit;
                }
                .btn-primary table td:hover {
                background-color: #34495e !important; 
                }
                .btn-primary a:hover {
                background-color: #34495e !important;
                border-color: #34495e !important; 
                } 
            }

        </style>
    </head>
    <body class="">
        <span class="preheader">{$i18n["email-header"]}</span>
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td>&nbsp;</td>
            <td class="container">
            <div class="content">

                <!-- START CENTERED WHITE CONTAINER -->
                <table role="presentation" class="main">

                <!-- START MAIN CONTENT AREA -->
                <tr>
                    <td class="wrapper">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                        <td>
                            <em><h2 style="margin-bottom: 0.5rem; text-align: start;">{$i18n["email-header"]}!</h2></em>
                            <p>{$emailContent}</p>
                        </td>
                        </tr>
                    </table>
                    </td>
                </tr>

                <!-- END MAIN CONTENT AREA -->
                </table>
                <!-- END CENTERED WHITE CONTAINER -->

                <!-- START FOOTER -->
                <div class="footer">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                    <td class="content-block">
                        <span class="apple-link">unterschreib.jetzt, c/o Kreativ Kollektiv K. KlG, Weinbergstrasse 12, 8107 Buchs ZH</span>
                    </td>
                    </tr>
                    <tr>
                    <td class="content-block powered-by">
                        <a href="https://unterschreib.jetzt">Support unterschreib.jetzt</a>.
                    </td>
                    </tr>
                </table>
                </div>
                <!-- END FOOTER -->

            </div>
            </td>
            <td>&nbsp;</td>
        </tr>
        </table>
    </body>
    </html>

EOD;

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
    $mail->Body    = $htmlemail;

    $mail->send();
} catch (Exception $e) {
    echo("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
}


if ($row["bogen_drucker"] == 1):

    if (!file_exists("./img/brief-" . $row["bogen_postID"] . ".jpg")) {
        $url = get_field("brief", $row["bogen_postID"]);
        $imagick = new Imagick();
        $imagick->setResolution(576,576);  
        $imagick->readImage($url);
        $imagick->resizeImage(2480,3508,Imagick::FILTER_CUBIC,1);
        $imagick->setCompressionQuality(80);
        $imagick->setImageFormat('jpg');
        $imagick->writeImage("./img/brief-" . $row["bogen_postID"] . ".jpg");
    }

    $drucken_name = get_field("drucken_name", $row["bogen_postID"]);
    $drucken_address = get_field("drucken_address", $row["bogen_postID"]);

    $pdf = new tFPDF('P','mm','A4');
    $pdf->AddPage();
    $pdf->Image("./img/brief-" . $row["bogen_postID"] . ".jpg",0,0,210);
    $filename_noprint = "brief-{$uuid}.pdf";
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"];

    $pdf->SetFont('Arial','',12);

    $pdf->Text($drucken_name["x_wert"], $drucken_name["y_wert"], ucfirst(strtolower($row["bogen_fname"])) . " " . ucfirst(strtolower($row["bogen_lname"])));
    $pdf->Text($drucken_address["x_wert"], $drucken_address["y_wert"], ucfirst(strtolower($row["bogen_fname"])) . " " . ucfirst(strtolower($row["bogen_lname"])));
    $pdf->Text($drucken_address["x_wert"], $drucken_address["y_wert"] + 5, ucfirst(strtolower($row["bogen_address"])));
    $pdf->Text($drucken_address["x_wert"], $drucken_address["y_wert"] + 10, ucfirst(strtolower($row["bogen_plz"])) . " " . ucfirst(strtolower($row["bogen_ort"])));

    $attachment_noprint = $pdf->Output($filename_noprint,'S');

    $tags = array("[fname]", "[lname]", "[nosig]", "[bogenlink]");
    $nosig = $row["bogen_nosig"];

    if ($row["bogen_nosig"] = 1) {
        $nosig = "eine Unterschrift";
    } else {
        $nosig .= " Unterschriften";
    }

    $replace = array($row["bogen_fname"], $row["bogen_lname"], $nosig, $actual_link . '/wp-content/themes/unterschreib-jetzt/bogen/' . $filename);

    $emailNoprint = str_replace($tags, $replace, $i18n["email-noprint-cont"]);

    $htmlNoprint = <<<EOD
        <!doctype html>
        <html>
        <head>
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>{$i18n["email-noprint-subject"]}</title>
            <style>
                /* -------------------------------------
                    GLOBAL RESETS
                ------------------------------------- */
                
                /*All the styling goes here*/
                
                img {
                    border: none;
                    -ms-interpolation-mode: bicubic;
                    max-width: 100%; 
                }

                body {
                    background-color: #f6f6f6;
                    font-family: sans-serif;
                    -webkit-font-smoothing: antialiased;
                    font-size: 14px;
                    line-height: 1.4;
                    margin: 0;
                    padding: 0;
                    -ms-text-size-adjust: 100%;
                    -webkit-text-size-adjust: 100%; 
                }

                table {
                    border-collapse: separate;
                    mso-table-lspace: 0pt;
                    mso-table-rspace: 0pt;
                    width: 100%; }
                    table td {
                    font-family: sans-serif;
                    font-size: 14px;
                    vertical-align: top; 
                }

                /* -------------------------------------
                    BODY & CONTAINER
                ------------------------------------- */

                .body {
                    background-color: #f6f6f6;
                    width: 100%; 
                }

                /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
                .container {
                    display: block;
                    margin: 0 auto !important;
                    /* makes it centered */
                    max-width: 580px;
                    padding: 10px;
                    width: 580px; 
                }

                /* This should also be a block element, so that it will fill 100% of the .container */
                .content {
                    box-sizing: border-box;
                    display: block;
                    margin: 0 auto;
                    max-width: 580px;
                    padding: 10px; 
                }

                /* -------------------------------------
                    HEADER, FOOTER, MAIN
                ------------------------------------- */
                .main {
                    background: #ffffff;
                    border-radius: 3px;
                    width: 100%; 
                }

                .wrapper {
                    box-sizing: border-box;
                    padding: 20px; 
                }

                .content-block {
                    padding-bottom: 10px;
                    padding-top: 10px;
                }

                .footer {
                    clear: both;
                    margin-top: 10px;
                    text-align: center;
                    width: 100%; 
                }
                    .footer td,
                    .footer p,
                    .footer span,
                    .footer a {
                    color: #999999;
                    font-size: 12px;
                    text-align: center; 
                }

                /* -------------------------------------
                    TYPOGRAPHY
                ------------------------------------- */
                h1,
                h2,
                h3,
                h4 {
                    color: #000000;
                    font-family: sans-serif;
                    font-weight: 400;
                    line-height: 1.4;
                    margin: 0;
                    margin-bottom: 30px; 
                }

                h1 {
                    font-size: 35px;
                    font-weight: 300;
                    text-align: center;
                    text-transform: capitalize; 
                }

                p,
                ul,
                ol {
                    font-family: sans-serif;
                    font-size: 14px;
                    font-weight: normal;
                    margin: 0;
                    margin-bottom: 15px; 
                }
                    p li,
                    ul li,
                    ol li {
                    list-style-position: inside;
                    margin-left: 5px; 
                }

                a {
                    color: #3498db;
                    text-decoration: underline; 
                }

                /* -------------------------------------
                    BUTTONS
                ------------------------------------- */
                .btn {
                    box-sizing: border-box;
                    width: 100%; }
                    .btn > tbody > tr > td {
                    padding-bottom: 15px; }
                    .btn table {
                    width: auto; 
                }
                    .btn table td {
                    background-color: #ffffff;
                    border-radius: 5px;
                    text-align: center; 
                }
                    .btn a {
                    background-color: #ffffff;
                    border: solid 1px #3498db;
                    border-radius: 5px;
                    box-sizing: border-box;
                    color: #3498db;
                    cursor: pointer;
                    display: inline-block;
                    font-size: 14px;
                    font-weight: bold;
                    margin: 0;
                    padding: 12px 25px;
                    text-decoration: none;
                    text-transform: capitalize; 
                }

                .btn-primary table td {
                    background-color: #3498db; 
                }

                .btn-primary a {
                    background-color: #3498db;
                    border-color: #3498db;
                    color: #ffffff; 
                }

                /* -------------------------------------
                    OTHER STYLES THAT MIGHT BE USEFUL
                ------------------------------------- */
                .last {
                    margin-bottom: 0; 
                }

                .first {
                    margin-top: 0; 
                }

                .align-center {
                    text-align: center; 
                }

                .align-right {
                    text-align: right; 
                }

                .align-left {
                    text-align: left; 
                }

                .clear {
                    clear: both; 
                }

                .mt0 {
                    margin-top: 0; 
                }

                .mb0 {
                    margin-bottom: 0; 
                }

                .preheader {
                    color: transparent;
                    display: none;
                    height: 0;
                    max-height: 0;
                    max-width: 0;
                    opacity: 0;
                    overflow: hidden;
                    mso-hide: all;
                    visibility: hidden;
                    width: 0; 
                }

                .powered-by a {
                    text-decoration: none; 
                }

                hr {
                    border: 0;
                    border-bottom: 1px solid #f6f6f6;
                    margin: 20px 0; 
                }

                /* -------------------------------------
                    RESPONSIVE AND MOBILE FRIENDLY STYLES
                ------------------------------------- */
                @media only screen and (max-width: 620px) {
                    table[class=body] h1 {
                    font-size: 28px !important;
                    margin-bottom: 10px !important; 
                    }
                    table[class=body] p,
                    table[class=body] ul,
                    table[class=body] ol,
                    table[class=body] td,
                    table[class=body] span,
                    table[class=body] a {
                    font-size: 16px !important; 
                    }
                    table[class=body] .wrapper,
                    table[class=body] .article {
                    padding: 10px !important; 
                    }
                    table[class=body] .content {
                    padding: 0 !important; 
                    }
                    table[class=body] .container {
                    padding: 0 !important;
                    width: 100% !important; 
                    }
                    table[class=body] .main {
                    border-left-width: 0 !important;
                    border-radius: 0 !important;
                    border-right-width: 0 !important; 
                    }
                    table[class=body] .btn table {
                    width: 100% !important; 
                    }
                    table[class=body] .btn a {
                    width: 100% !important; 
                    }
                    table[class=body] .img-responsive {
                    height: auto !important;
                    max-width: 100% !important;
                    width: auto !important; 
                    }
                }

                /* -------------------------------------
                    PRESERVE THESE STYLES IN THE HEAD
                ------------------------------------- */
                @media all {
                    .ExternalClass {
                    width: 100%; 
                    }
                    .ExternalClass,
                    .ExternalClass p,
                    .ExternalClass span,
                    .ExternalClass font,
                    .ExternalClass td,
                    .ExternalClass div {
                    line-height: 100%; 
                    }
                    .apple-link a {
                    color: inherit !important;
                    font-family: inherit !important;
                    font-size: inherit !important;
                    font-weight: inherit !important;
                    line-height: inherit !important;
                    text-decoration: none !important; 
                    }
                    #MessageViewBody a {
                    color: inherit;
                    text-decoration: none;
                    font-size: inherit;
                    font-family: inherit;
                    font-weight: inherit;
                    line-height: inherit;
                    }
                    .btn-primary table td:hover {
                    background-color: #34495e !important; 
                    }
                    .btn-primary a:hover {
                    background-color: #34495e !important;
                    border-color: #34495e !important; 
                    } 
                }

            </style>
        </head>
        <body class="">
            <span class="preheader">{$i18n["email-noprint-subject"]}</span>
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
            <tr>
                <td>&nbsp;</td>
                <td class="container">
                <div class="content">

                    <!-- START CENTERED WHITE CONTAINER -->
                    <table role="presentation" class="main">

                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                            <td>
                                <p>{$emailNoprint}</p>
                            </td>
                            </tr>
                        </table>
                        </td>
                    </tr>

                    <!-- END MAIN CONTENT AREA -->
                    </table>
                    <!-- END CENTERED WHITE CONTAINER -->

                    <!-- START FOOTER -->
                    <div class="footer">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                        <td class="content-block">
                            <span class="apple-link">unterschreib.jetzt, c/o Kreativ Kollektiv K. KlG, Weinbergstrasse 12, 8107 Buchs ZH</span>
                        </td>
                        </tr>
                        <tr>
                        <td class="content-block powered-by">
                            <a href="https://unterschreib.jetzt">Support unterschreib.jetzt</a>.
                        </td>
                        </tr>
                    </table>
                    </div>
                    <!-- END FOOTER -->

                </div>
                </td>
                <td>&nbsp;</td>
            </tr>
            </table>
        </body>
        </html>

    EOD;

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
        $mail->addAddress(get_field("drucken_email", $row["bogen_postID"]));
        $mail->CharSet  = 'UTF-8'; // the same as 'utf-8'

        // Attachments
        $mail->AddStringAttachment($attachment, $filename);
        $mail->AddStringAttachment($attachment_noprint, $filename_noprint);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $i18n["email-noprint-subject"];
        $mail->Body    = $htmlNoprint;

        $mail->send();
    } catch (Exception $e) {
        echo("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }

endif;

?>