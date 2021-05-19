<?php
/**
* Template Name: Donate
*/
get_header()
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 

$feaimg = get_the_post_thumbnail_url($post, "large");
?>

<main>
    <div id="page-content">
        <a href="/" class="back-icon">
            <i class="ri-arrow-left-line"></i>
            <span class="buttonfont"><?= $i18n["back"] ?></span>
        </a>
        <h1 class="red pagetitle"><?= the_title() ?></h1>
        
        <p class="donate-lead"><?= the_field("spenden_short") ?></p>

        <img src="<?= $feaimg ?>" class="dropshadow feaimg" alt="">
        
        <div class="rnw-widget-container"></div>

        <script src="<?= the_field("raisenow_link") ?>"></script>
        <?= the_field("raisenow_script") ?>

        <style>
            :root {
                --tamaro-primary-color: var(--red);
                --tamaro-primary-color__hover: rgba(190, 22, 34, 0.75);
                --tamaro-primary-bg-color: rgba(228,2,45,0.03);
                --tamaro-border-color: var(--grey);
                --tamaro-bg-color: var(--white);
            }      
        </style>
        
        <div class="bottom-bar">
            <small><a href="https://unterschreib.jetzt" target="_blank">© unterschreib.jetzt.ch</a>, <?= date("Y") ?></small>
            <small><a href="<?= $i18n["dataprotec-page"] ?>"><?= $i18n["dataprotec"] ?></a></small>
            <small><a href="<?= $i18n["support-page"] ?>"><?= $i18n["support"] ?></a></small>
            <small><a style="text-decoration: none" href="https://www.kpunkt.ch">Built with love by <b>K.</b></a></small>
        </div>
    </div>
</main>


<?php endwhile; else: ?>

<h2><?php esc_html_e( '404 Error', 'phpforwp' ); ?></h2>
<p><?php esc_html_e( 'Sorry, content not found.', 'phpforwp' ); ?></p>

<?php endif; ?>


<?php
wp_enqueue_style( 'donate', get_template_directory_uri() . "/style/pages/donate.css" );
wp_enqueue_style( 'page', get_template_directory_uri() . "/style/page.css" );
get_footer()
?>