<?php
$id = get_sub_field("image_img");
$src = wp_get_attachment_image_src( $id, 'large' );
$srcset = wp_get_attachment_image_srcset( $id, 'large' );
$sizes = wp_get_attachment_image_sizes( $id, 'large' );
$alt = get_post_meta( $id, '_wp_attachment_image_alt', true);
$imgdesc = get_sub_field("image_description");
?>
<img class="simple-img" src="<?php echo esc_attr( $src );?>"
    srcset="<?php echo esc_attr( $srcset ); ?>"
    sizes="<?php echo esc_attr( $sizes );?>"
    alt="<?php echo esc_attr( $alt );?>" />
<?php if ($imgdesc != "") { ?> <p class="img-desc"><small><?= $imgdesc ?></small></p> <?php } ?>

<?php
wp_enqueue_style( 'simple-img', get_template_directory_uri() . "/style/modules/simple-img.css" );
?>