<?php
/**
* Template Name: Admin
*/
get_header("admin");

if (!isset($_GET["view"])) {
    get_template_part("templates/admin/bogen-list");
}

if ($_GET["view"] == "bogen") {
    get_template_part("templates/admin/bogen");
}

if ($_GET["view"] == "register") {
    get_template_part("templates/admin/register");
}

if ($_GET["view"] == "mysheets") {
    get_template_part("templates/admin/my-sheets");
}

if ($_GET["view"] == "mailchimp") {
    get_template_part("templates/admin/mailchimp");
}

get_footer("admin");
?>