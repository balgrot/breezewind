<?php
/**
 * Breezewind WordPress Theme Functions
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);

/**
 * Theme Setup
 */
function breezewind_setup() {
    // Add theme support for various features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style'
    ));
    
    // Add support for responsive embeds
    add_theme_support('responsive-embeds');
    
    // Add support for editor styles
    add_theme_support('editor-styles');
    
    // Set content width
    global $content_width;
    if (!isset($content_width)) {
        $content_width = 1200;
    }
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'breezewind'),
        'footer'  => esc_html__('Footer Menu', 'breezewind'),
    ));
    
    // Add support for post formats
    add_theme_support('post-formats', array(
        'aside',
        'image',
        'video',
        'quote',
        'link',
        'gallery',
        'status',
        'audio',
        'chat'
    ));
}
add_action('after_setup_theme', 'breezewind_setup');

/**
 * Enqueue styles and scripts
 */
function breezewind_scripts() {
    // Enqueue main stylesheet
    wp_enqueue_style('breezewind-style', get_stylesheet_uri(), array(), '1.0.0');
    // Enqueue tailwind styles
    wp_enqueue_style('tailwindcss-style', get_template_directory_uri() . '/src/output.css', array(), '1.0.0');
    
    // Enqueue Flowbite Scripts
    wp_enqueue_script('flowbite-script', get_template_directory_uri() . '/js/flowbite.min.js', array(), '1.0.0', true);

    // Enqueue custom JavaScript for mobile menu
    //wp_enqueue_script('breezewind-script', get_template_directory_uri() . '/js/theme.js', array('jquery'), '1.0.0', true);


    
    // Enqueue comment reply script on singular posts/pages
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'breezewind_scripts');

/**
 * Register widget areas
 */
function breezewind_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'breezewind'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here to appear in your sidebar.', 'breezewind'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => esc_html__('Footer Widget Area', 'breezewind'),
        'id'            => 'footer-widget-area',
        'description'   => esc_html__('Add widgets here to appear in your footer.', 'breezewind'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'breezewind_widgets_init');

/**
 * Custom excerpt length
 */
function breezewind_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'breezewind_excerpt_length');

/**
 * Custom excerpt more text
 */
function breezewind_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'breezewind_excerpt_more');

/**
 * Add custom image sizes
 */
function breezewind_image_sizes() {
    add_image_size('breezewind-featured', 800, 400, true);
    add_image_size('breezewind-thumbnail', 300, 200, true);
}
add_action('after_setup_theme', 'breezewind_image_sizes');

/**
 * Customize the login page
 */
function breezewind_login_logo() {
    if (has_custom_logo()) {
        $custom_logo_id = get_theme_mod('custom_logo');
        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
        if ($logo) {
            echo '<style type="text/css">
                #login h1 a, .login h1 a {
                    background-image: url(' . $logo[0] . ');
                    height: 80px;
                    width: 320px;
                    background-size: contain;
                    background-repeat: no-repeat;
                }
            </style>';
        }
    }
}
add_action('login_enqueue_scripts', 'breezewind_login_logo');

/**
 * Change login logo URL
 */
function breezewind_login_logo_url() {
    return home_url();
}
add_filter('login_headerurl', 'breezewind_login_logo_url');

/**
 * Change login logo title
 */
function breezewind_login_logo_url_title() {
    return get_bloginfo('name');
}
add_filter('login_headertitle', 'breezewind_login_logo_url_title');

/**
 * Add support for Gutenberg editor styles
 */
function breezewind_add_editor_styles() {
    add_editor_style('editor-style.css');
}
add_action('admin_init', 'breezewind_add_editor_styles');

/**
 * Filter the categories archive widget to add a post count span
 */
function breezewind_cat_count_span($links) {
    $links = str_replace('</a> (', '</a> <span class="count">(', $links);
    $links = str_replace(')', ')</span>', $links);
    return $links;
}
add_filter('wp_list_categories', 'breezewind_cat_count_span');

/**
 * Filter the archives widget to add a post count span
 */
function breezewind_archive_count_span($links) {
    $links = str_replace('</a>&nbsp;(', '</a> <span class="count">(', $links);
    $links = str_replace(')', ')</span>', $links);
    return $links;
}
add_filter('get_archives_link', 'breezewind_archive_count_span');

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments
 */
function breezewind_pingback_header() {
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('wp_head', 'breezewind_pingback_header');

/**
 * Customizer additions
 */
function breezewind_customize_register($wp_customize) {
    // Add a setting for footer text
    $wp_customize->add_section('breezewind_footer', array(
        'title'    => __('Footer Settings', 'breezewind'),
        'priority' => 120,
    ));
    
    $wp_customize->add_setting('footer_text', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('footer_text', array(
        'label'    => __('Footer Text', 'breezewind'),
        'section'  => 'breezewind_footer',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'breezewind_customize_register');

/**
 * Display custom footer text
 */
function breezewind_footer_text() {
    $footer_text = get_theme_mod('footer_text', '');
    if (!empty($footer_text)) {
        echo '<p>' . esc_html($footer_text) . '</p>';
    }
}

/**
 * Add mobile menu toggle functionality
 */
function breezewind_mobile_menu_script() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('.menu-toggle').click(function() {
            $(this).toggleClass('active');
            $('.nav-menu').toggleClass('active');
        });
    });
    </script>
    <?php
}
//add_action('wp_footer', 'breezewind_mobile_menu_script');

/**
 * Security enhancements
 */
// Remove WordPress version from head
remove_action('wp_head', 'wp_generator');

// Disable file editing in WordPress admin
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

/**
 * Performance optimizations
 */
// Remove emoji scripts
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Remove query strings from static resources
function breezewind_remove_query_strings($src) {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'breezewind_remove_query_strings', 10, 1);
add_filter('script_loader_src', 'breezewind_remove_query_strings', 10, 1);

?>