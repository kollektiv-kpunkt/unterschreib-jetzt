<?php
get_header()
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 

$feaimg = get_the_post_thumbnail_url($post, "large");

$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

$mobimsg_encode = urlencode(get_field("mobi_nachrichten") . " " . $current_url);

$required = number_format(get_field("anzahl_unterschriften"), 0, ",", "'");

$promised = number_format($wpdb->get_results("SELECT sum(bogen_nosig) as promised FROM {$wpdb->prefix}bogens")[0]->promised, 0, ",", "'");

$percentage = $wpdb->get_results("SELECT sum(bogen_nosig) as promised FROM {$wpdb->prefix}bogens")[0]->promised / get_field("anzahl_unterschriften") * 100;

if ($percentage >= 100) {
    $percentage = 100;
}

$mobiText = get_field("mobi_short");
$tags = array("[required]", "[promised]");
$replace = array($required, $promised);

$mobi_short = str_replace($tags, $replace, $mobiText);

?>

    <main>
        <div id="page-content">
            <h1 class="red pagetitle"><?= the_title() ?></h1>
            <div class="excerpt"><p><?= the_field("auszug") ?></p></div>
            <div class="detail-cont">
                <div class="detail buttonfont">
                    <i class="ri-map-pin-2-line"></i>
                    <span><?= the_field("ort") ?></span>
                </div>
                <div class="detail buttonfont">
                    <i class="ri-calendar-event-fill"></i>
                    <span><?= the_field("datum") ?></span>
                </div>
                <div class="detail buttonfont">
                    <i class="ri-map-pin-2-line"></i>
                    <span><a href="https://<?=the_field("webseite") ?>" target="_blank"><?= the_field("webseite") ?></a></span>
                </div>
                <div class="detail buttonfont">
                    <i class="ri-user-follow-line"></i>
                    <span><a href="mailto:<?= the_field("kontakt") ?>"><?= $i18n["contact"] ?></a></span>
                </div>
            </div>

            <img src="<?= $feaimg ?>" class="dropshadow feaimg" alt="">
            <?php
            if( have_rows('builder') ):
                $numrows = count( get_field('builder'));
                $i = 1;
                while ( have_rows('builder') ) : the_row(); ?>
                <div class="row <?= get_row_layout() ?><?php print ($i == $numrows) ? " last" : "";?><?php print ($i == 1) ? " first" : "";?>" id="row-id-<?= get_the_ID()?>-<?= $i ?>"> <?php
                    if( get_row_layout() == 'text' ):
                        get_template_part("modules/builder/simple-text");
                    elseif( get_row_layout() == 'image' ):
                        get_template_part("modules/builder/simple-img");
                    endif; ?>
                </div> 
                <?php
                $i++;
                endwhile;
            else :
            endif;
            ?>
            <div class="cta-cont">
                <h2 class="red"><?= $i18n["help-us"] ?></h2>
                <p class="cta-text"><?= the_field("mobi_cta") ?></p>
                <div class="buttongrid">
                    <a href="https://api.whatsapp.com/send?text=<?= $mobimsg_encode?>" target="_blank" class="button grid-button" id="whatsapp"><?= $i18n["share-whatsapp"] ?></a>
                    <a href="https://t.me/share/url?url=<?=urlencode($current_url)?>&text=<?= $mobimsg_encode ?>" target="_blank" class="button grid-button" id="telegram"><?= $i18n["share-telegram"] ?></a>
                    <a href="https://twitter.com/intent/tweet?text=<?= $mobimsg_encode ?>" target="_blank" class="button grid-button" id="twitter"><?= $i18n["share-twitter"] ?></a>
                    <a href="#" target="_blank" class="button grid-button" id="email"><?= $i18n["share-email"] ?></a>
                </div>
            </div>
            <div class="bottom-bar">
                <small><a href="https://unterschreib.jetzt" target="_blank">© unterschreib.jetzt.ch</a>, <?= date("Y") ?></small>
                <small><a href="<?= $i18n["dataprotec-page"] ?>"><?= $i18n["dataprotec"] ?></a></small>
                <small><a href="<?= $i18n["support-page"] ?>"><?= $i18n["support"] ?></a></small>
                <small><a style="text-decoration: none" href="https://www.kpunkt.ch">Built with love by <b>K.</b></a></small>
            </div>
        </div>
    </main>
    <div id="ajax">
        <aside>
            <div id="mobile-fab"><span><h4><?= $i18n["sign-now"] ?></h4><i class="ri-arrow-up-s-line"></i></span></div>
            <div class="form-outer">
                <div class="form-inner">
                    <div class="form-cont">
                        <h3 class="pagetitle"><?= $i18n["sign-now"] ?></h3>
                        <div class="close-icon">
                            <i class="ri-close-line"></i>
                            <span class="buttonfont"><?= $i18n["close"] ?></span>
                        </div>
                        <p class="mobi-short"><?= $mobi_short ?></p>
                        <div id="progress-container">
                            <div id="arrow-container" style="margin-left: 0%">
                                <div id="arrow-inner">
                                    <small><span id="arrow-percentage"></span>%</small>
                                    <i class="ri-arrow-down-line"></i>
                                </div>
                            </div>
                            <div id="progress-outer">
                                <div id="progress-inner" style="width: 0%">
                                </div>
                            </div>
                        </div>
                        <form action="#" data-step="1" class="signform">
                            <input type="text" name="fname" placeholder="<?= $i18n["fname"] ?> *" required>
                            <input type="text" name="lname" placeholder="<?= $i18n["lname"] ?> *" required>
                            <input type="text" name="email" placeholder="<?= $i18n["email"] ?> *" required>
                            <input type="tel" name="phone" placeholder="<?= $i18n["phone"] ?>">
                            <div class="form-group">
                                <input type="checkbox" id="optin" name="optin" value="1" checked>
                                <label for="optin"><?= $i18n["optin"] ?></label>
                            </div>
                            <input type="hidden" name="uuid" value="<?= uniqid("signature_") ?>">
                            <input type="hidden" name="postID" value="<?= get_the_ID() ?>">
                            <button type="submit" class="fullwidth white"><?= $i18n["sign-submit"] ?></button>
                            <div class="form-alert"></div>
                            <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
                        </form>
                    </div>
                </div>
            </div>
        </aside>
    </div>

<?php endwhile; else: ?>

    <h2><?php esc_html_e( '404 Error', 'phpforwp' ); ?></h2>
    <p><?php esc_html_e( 'Sorry, content not found.', 'phpforwp' ); ?></p>

<?php endif; ?>


<script src="<?=get_template_directory_uri()?>/vendor/jquery.countTo.js"></script>
<script>
    setTimeout(() => {
        jQuery('#arrow-percentage').countTo({from: 0, to: <?= $percentage ?>});
        jQuery('#arrow-container').css('margin-left', '<?= $percentage ?>%')
        jQuery('#progress-inner').css('width', '<?= $percentage ?>%')
    }, 250);
</script>

<?php
wp_enqueue_style( 'frontpage', get_template_directory_uri() . "/style/pages/frontpage.css" );
wp_enqueue_style( 'page', get_template_directory_uri() . "/style/page.css" );
wp_enqueue_script( 'frontpage', get_template_directory_uri() . '/js/pages/frontpage.js', array ( 'jquery' ), 1.1, true);
wp_enqueue_script( 'formsubmit', get_template_directory_uri() . '/js/elements/formsubmit.js', array ( 'jquery' ), 1.1, true);
get_footer()
?>