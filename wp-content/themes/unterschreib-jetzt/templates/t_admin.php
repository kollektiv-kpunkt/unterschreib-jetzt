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

get_footer("admin");
?>