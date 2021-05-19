<?php

add_theme_support( 'post-thumbnails' );
add_theme_support( 'title-tag' );


/* SCRIPTS AND STYLES */
function add_theme_scripts() {
    wp_enqueue_style( 'remixicon', "https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" );
    wp_enqueue_style( 'style', get_stylesheet_uri() );
    wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array ( 'jquery' ), 1.1, true);
    wp_enqueue_script( 'style', get_template_directory_uri() . '/js/style.js', array ( 'jquery' ), 1.1, false);
    wp_enqueue_script( 'ajax-forms', get_template_directory_uri() . '/js/elements/forms.js', array ( 'jquery' ), 1.1, true);
    wp_enqueue_script( 'hyphens1', get_template_directory_uri() . '/js/hyphenopoly.app.js', array ( 'jquery' ), 1.1, false);
    wp_enqueue_script( 'hyphens2', get_template_directory_uri() . '/vendor/hyphenopoly/Hyphenopoly_Loader.js', array ( 'jquery' ), 1.1, false);
}
add_action( 'wp_enqueue_scripts', 'add_theme_scripts' );


// Define path and URL to the ACF plugin.
define( 'MY_ACF_PATH', get_stylesheet_directory() . '/lib/acf/' );
define( 'MY_ACF_URL', get_stylesheet_directory_uri() . '/lib/acf/' );

// Include the ACF plugin.
include_once( MY_ACF_PATH . 'acf.php' );

// Customize the url setting to fix incorrect asset URLs.
add_filter('acf/settings/url', 'my_acf_settings_url');
function my_acf_settings_url( $url ) {
    return MY_ACF_URL;
}

add_filter('acf/settings/save_json', 'set_acf_json_save_folder');
function set_acf_json_save_folder( $path ) {
    $path = MY_ACF_PATH . '/acf-json';
    return $path;
}
add_filter('acf/settings/load_json', 'add_acf_json_load_folder');
function add_acf_json_load_folder( $paths ) {
    unset($paths[0]);
    $paths[] = MY_ACF_PATH . '/acf-json';;
    return $paths;
}


function create_pages_on_theme_activation(){
    
    // Set the title, template, etc
    $new_page_title     = __('Wir brauchen deine Hilfe!','home'); // Page's title
    $page_check = get_page_by_title($new_page_title);   // Check if the page already exists
    // Store the above data in an array
    $new_page = array(
        'post_type'     => 'page', 
        'post_title'    => $new_page_title,
        'post_content'  => $new_page_content,
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_name'     => 'home'
    );
    // If the page doesn't already exist, create it
    if(!isset($page_check->ID)){
        $new_page_id = wp_insert_post($new_page);
        if(!empty($new_page_template)){
            update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
        }
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $new_page_id );
    }
}

add_action( 'after_switch_theme', 'create_pages_on_theme_activation' );


require_once get_template_directory() . '/lib/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'los_register_required_plugins' );

function los_register_required_plugins() {
	$plugins = array( 

        array(
            'name' => 'Classic Editor',
            'slug' => 'classic-editor',
            'source'    => __DIR__ . '/lib/classic-editor.zip',
            'required' => true,
            'force_activation' => false,
            'force_deactivation' => true
        ),

        array(
            'name' => 'Require Featured Image',
            'slug' => 'require-featured-image',
            'source'    => __DIR__ . '/lib/require-featured-image.zip',
            'required' => true,
            'force_activation' => false,
            'force_deactivation' => true
        ),
        
        array(
            'name' => 'The SEO Framework',
            'slug' => 'autodescription',
            'source'    => __DIR__ . '/lib/autodescription.zip',
            'required' => true,
            'force_activation' => false,
            'force_deactivation' => true
        ),

    );

    $config = array(
        'id'           => 'tgm_act',               // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'parent_slug'  => 'themes.php',            // Parent menu slug.
        'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
    );

    
    tgmpa( $plugins, $config );
}