<?php
global $i18n;
include __DIR__ . "/../i18n/de.php";
?>

<aside class="step">
    <div class="form-outer">
        <div class="form-inner">
            <div class="form-cont">
                <h3 class="pagetitle show"><?= $i18n["thx"] ?>, <span id="fname"></span></h3>
                <p><?= $i18n["thx-text"] ?></p>
                <p style="margin-top:1.5rem"><?= $i18n["donate-text"] ?></p>
                <div class="buttongrid">
                    <a href="<?= $i18n["donate-page"] ?>?rnw-amount=10" class="button grid-button white"><?= $i18n["donate-10"] ?></a>
                    <a href="<?= $i18n["donate-page"] ?>?rnw-amount=30" class="button grid-button white"><?= $i18n["donate-30"] ?></a>
                    <a href="<?= $i18n["donate-page"] ?>?rnw-amount=50" class="button grid-button white"><?= $i18n["donate-50"] ?></a>
                    <a href="<?= $i18n["donate-page"] ?>?rnw-amount=80" class="button grid-button white"><?= $i18n["donate-80"] ?></a>
                </div>
            </div>
        </div>
    </div>
</aside>