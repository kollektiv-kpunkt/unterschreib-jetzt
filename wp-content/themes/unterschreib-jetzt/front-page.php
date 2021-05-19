<?php
get_header()
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 

$feaimg = get_the_post_thumbnail_url($post, "large")
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

            // Check value exists.
            if( have_rows('content') ):

                // Loop through rows.
                while ( have_rows('content') ) : the_row();

                    // Case: Paragraph layout.
                    if( get_row_layout() == 'paragraph' ):
                        $text = get_sub_field('text');
                        // Do something...

                    // Case: Download layout.
                    elseif( get_row_layout() == 'download' ): 
                        $file = get_sub_field('file');
                        // Do something...

                    endif;

                // End loop.
                endwhile;

            // No value.
            else :
                // Do something...
            endif;

            ?>

        </div>
    </main>

    <aside>

    </aside>

<?php endwhile; else: ?>

    <h2><?php esc_html_e( '404 Error', 'phpforwp' ); ?></h2>
    <p><?php esc_html_e( 'Sorry, content not found.', 'phpforwp' ); ?></p>

<?php endif; ?>


<?php
wp_enqueue_style( 'frontpage', get_template_directory_uri() . "/style/pages/frontpage.css" );
wp_enqueue_style( 'page', get_template_directory_uri() . "/style/page.css" );
get_footer()
?>