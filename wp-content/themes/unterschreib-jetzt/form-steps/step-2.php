<?php
require_once(__DIR__ . "/../../../../wp-load.php");
global $i18n;
include __DIR__ . "/../i18n/de.php";
?>

<aside class="step">
    <div class="form-outer">
        <div class="form-inner">
            <div class="form-cont">
                <h3 class="pagetitle show"><?= $i18n["personal-data"] ?></h3>
                <p><?= $i18n["personal-data-text"] ?></p>
                <form action="#" data-step="2" class="signform">
                    <input type="text" name="address" placeholder="<?= $i18n["address"] ?> *" required>
                    <input type="text" name="plz" placeholder="<?= $i18n["plz"] ?> *" required>
                    <input type="text" name="place" placeholder="<?= $i18n["place"] ?> *" required>
                    <input type="number" name="nosig" placeholder="<?= $i18n["nosig"] ?>">
                    <small class="form-helper"><?= $i18n["nosig-helper"] ?></small>
                    <div class="form-group" id="noprint-group">
                        <input type="checkbox" id="drucker" name="drucker" value="1">
                        <label for="drucker"><?= $i18n["printer"] ?></label>
                    </div>
                    <input type="hidden" name="uuid">
                    <button type="submit" class="fullwidth white"><?= $i18n["sign-submit"] ?></button>
                    <div class="form-alert"></div>
                </form>
            </div>
        </div>
    </div>
</aside>