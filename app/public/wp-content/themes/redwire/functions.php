<?php
/**
 * Redwire Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Redwire
 * @since 1.0.0
 */

require_once(__DIR__.'/vendor/autoload.php');

// Custom taxonomies
require_once(__DIR__.'/inc/taxonomies.php');

// Custom REST endpoints
require_once(__DIR__.'/inc/api.php');

// Custom shortcodes
require_once(__DIR__.'/inc/shortcodes.php');

// Custom query hooks
require_once(__DIR__.'/inc/query.php');

/**
 * Define Constants
 */
define('REDWIRE_VERSION', '1.0.0');

define('REDWIRE_WHITE', '#ffffff');
define('REDWIRE_BLACK', '#030305');
define('REDWIRE_GRAY_DARK', '#3b3e44');
define('REDWIRE_GRAY_MEDIUM', '#666666');
define('REDWIRE_GRAY_LIGHT', '#d7d7d7');
define('REDWIRE_RED', '#ef0000');

/**
 * Add theme colors for Gutenberg
 */
add_theme_support('editor-color-palette', array(
	array(
		'name' => 'White',
		'slug' => 'white',
		'color' => REDWIRE_WHITE,
	),
	array(
		'name' => 'Black',
		'slug' => 'black',
		'color' => REDWIRE_BLACK,
	),
	array(
		'name' => 'Dark Gray',
		'slug' => 'gray-dark',
		'color' => REDWIRE_GRAY_DARK,
	),
	array(
		'name' => 'Medium Gray',
		'slug' => 'gray-medium',
		'color' => REDWIRE_GRAY_MEDIUM,
	),
	array(
		'name' => 'Light Gray',
		'slug' => 'gray-light',
		'color' => REDWIRE_GRAY_LIGHT,
	),
	array(
		'name' => 'Red',
		'slug' => 'red',
		'color' => REDWIRE_RED,
	)
));

/**
 * Adjust font sizes for Gutenberg
 */
add_theme_support( 'editor-font-sizes', array(
	array(
        'name' => __('Small', 'redwire'),
        'size' => 12,
        'slug' => 'small'
    ),
    array(
        'name' => __('Normal', 'redwire'),
        'size' => 14,
        'slug' => 'Normal'
	),
    array(
        'name' => __('Medium', 'redwire'),
        'size' => 25,
        'slug' => 'medium'
    ),
    array(
        'name' => __('Large', 'redwire'),
        'size' => 30,
        'slug' => 'large'
	),
	array(
        'name' => __('Huge', 'redwire'),
        'size' => 48,
        'slug' => 'huge'
    ),
	array(
        'name' => __('Jumbo', 'redwire'),
        'size' => 72,
        'slug' => 'jumbo'
    )
) );

/**
 * Disable WooCommerce image zoom on hover
 */
add_action('wp', function() {
    remove_theme_support('wc-product-gallery-zoom');
}, 99);

/**
 * Enqueue backend styles and scripts
 */
function redwire_custom_block_styles($hook) {
	wp_enqueue_style('redwire-editor-css', get_stylesheet_directory_uri() . '/dist/styles/editor.css', array('astra-block-editor-styles'), REDWIRE_VERSION, 'all');
	wp_enqueue_script('redwire-editor-js', get_stylesheet_directory_uri() . '/dist/scripts/editor.js', array('wp-blocks', 'wp-dom'), REDWIRE_VERSION, true);
}
add_action('enqueue_block_editor_assets', 'redwire_custom_block_styles');

/**
 * Enqueue frontend styles and scripts
 */
function redwire_enqueue_assets() {
	$asset_path = $_SERVER['HTTP_HOST'] == 'redwire.local' ? 'http://localhost:9000' : get_stylesheet_directory_uri();

	wp_enqueue_style('redwire-css',  $asset_path . '/dist/styles/main.css', array('astra-theme-css'), REDWIRE_VERSION, 'all');
    wp_enqueue_script('redwire-js', $asset_path . '/dist/scripts/main.js', array('jquery'), REDWIRE_VERSION, true);

    if(is_page('about')) {
        wp_enqueue_script('parallax-js', "https://cdn.jsdelivr.net/parallax.js/1.4.2/parallax.min.js", [], REDWIRE_VERSION);
    }

    if(is_page('careers')) {
        wp_enqueue_script('redwire-careers-js', $asset_path . '/dist/scripts/careers.js', array('redwire-js'), REDWIRE_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'redwire_enqueue_assets', 15);

/**
 * Disable featured image in search results
 */
add_filter('astra_featured_image_enabled', function($featured_image) {
	if (is_search()) {
		return false;
	}

	return $featured_image;
});

/**
 * Customize "Read More" link
 */
add_filter('astra_post_read_more', function() {
	return 'Read More';
});

/**
 * Add Business Unit cobranding
 */

/************************************************************************
 *
 * Is this page or post a Business Unit page
 *
 ***********************************************************************/

add_action( 'init', 'bu_subheader_menu' );
function bu_subheader_menu() {
  register_nav_menu('business-unit-subheader-menu',__( 'Business Unit Subheader Menu' ));
}

function redwire_bu_subheader() {

    global $post;

    $business_unit = is_bu();

    if ($business_unit) {

        echo '<div class="bu-subheader-container"><div class="ast-container"><div class="bu-subheader">';
        echo '<div class="bu-subheader-title"><h2>' . $business_unit['bu_name'] . '</h2></div>';
        echo '<div class="bu-subheader-navigation">';
        wp_nav_menu(array(
            'theme_location' => 'business-unit-subheader-menu', 
            'container_class' => 'business-unit-subheader-menu', 
            'menu_class' => 'ast-nav-menu ast-flex ast-justify-content-flex-end  submenu-with-border',
            'fallback_cb' => false
        ));
        echo '</div>';
        echo '</div></div></div>';

    }


}
add_action( 'astra_main_header_bar_bottom', 'redwire_bu_subheader' );

// Is page a parent, child or any ancestor of the page
function is_bu($post_id = null) {

    global $post;

    if ($post_id === null) {
        $post_id = $post->ID;
    }
    
    $bus = array(
        array(
            'bu_name' => 'Adcole Space',
            'bu_slug' => 'adcole-space',
            'bu_home_id' => 1181,
            'bu_cat_id' => 54,
            'cta_bg' => '/wp-content/uploads/2021/06/parker-solar-probe-nasa-scaled-1.jpg',
            'logo' => array( // using wp_get_attachment_image_src() return format
                '/wp-content/themes/redwire/dist/images/redwire-adcole-cobranded-logo.png', // url
                481, // width
                56, // height
                false // is_intermediate
            )
        ),
        array(
            'bu_name' => 'Deep Space Systems',
            'bu_slug' => 'deep-space-systems',
            'bu_home_id' => 1243,
            'bu_cat_id' => 55,
            'cta_bg' => '',
            'logo' => array( // using wp_get_attachment_image_src() return format
                '/wp-content/themes/redwire/dist/images/redwire-dss-cobranded-logo.png', // url
                594, // width
                56, // height
                false // is_intermediate
            )
        ),
        array(
            'bu_name' => 'Made In Space',
            'bu_slug' => 'made-in-space',
            'bu_home_id' => 1254,
            'bu_cat_id' => 56,
            'cta_bg' => '',
            'logo' => array( // using wp_get_attachment_image_src() return format
                '/wp-content/themes/redwire/dist/images/redwire-mis-cobranded-logo.png', // url
                494, // width
                56, // height
                false // is_intermediate
            )
        ),
        array(
            'bu_name' => 'LoadPath',
            'bu_slug' => 'loadpath',
            'bu_home_id' => 1705,
            'bu_cat_id' => 90,
            'cta_bg' => '',
            'logo' => array( // using wp_get_attachment_image_src() return format
                '/wp-content/themes/redwire/dist/images/redwire-mis-cobranded-logo.png', // url
                494, // width
                56, // height
                false // is_intermediate
            )
        ),
        array(
            'bu_name' => 'Deployable Space Systems',
            'bu_slug' => 'deployable-space-systems',
            'bu_home_id' => 31667,
            'bu_cat_id' => 91,
            'cta_bg' => '',
            'logo' => array( // using wp_get_attachment_image_src() return format
                '/wp-content/themes/redwire/dist/images/redwire-mis-cobranded-logo.png', // url
                494, // width
                56, // height
                false // is_intermediate
            )
        ),
        array(
            'bu_name' => 'Oakman Aerospace',
            'bu_slug' => 'oakman-aerospace',
            'bu_home_id' => 31674,
            'bu_cat_id' => 92,
            'cta_bg' => '',
            'logo' => array( // using wp_get_attachment_image_src() return format
                '/wp-content/themes/redwire/dist/images/redwire-mis-cobranded-logo.png', // url
                494, // width
                56, // height
                false // is_intermediate
            )
        ),
        array(
            'bu_name' => 'Roccor',
            'bu_slug' => 'roccor',
            'bu_home_id' => 31679,
            'bu_cat_id' => 93,
            'cta_bg' => '',
            'logo' => array( // using wp_get_attachment_image_src() return format
                '/wp-content/themes/redwire/dist/images/redwire-mis-cobranded-logo.png', // url
                494, // width
                56, // height
                false // is_intermediate
            )
        ),
    );

    $ancestors = get_post_ancestors($post_id);
    $root = count($ancestors) - 1;
    $parent = $ancestors[$root];

    $business_unit = false;

    foreach($bus as $bu) {
        
        if(has_term($bu['bu_cat_id'], 'business_unit', $post_id) || 
            ($post_id == $bu['bu_home_id'] ||
             $post->post_parent == $bu['bu_home_id'] ||
             in_array($bu['bu_home_id'], $ancestors)
            )
        ) {
            $business_unit = $bu;
            break;
        } 
   
    }

    if ($business_unit === false) {
        return false;
    } else {
        return $business_unit;
    }

}


function redwire_filter_astra_replace_header_logo( $logo ) {

  global $post;

  $business_unit = is_bu();

  if ($business_unit !== false) {
    $logo = $business_unit['logo'];

  }
  return $logo;
}
//add_filter( 'astra_replace_header_logo', 'redwire_filter_astra_replace_header_logo' );

function redwire_filter_astra_theme_bu_dynamic_css ( $css ) {

            $bu = is_bu();

            if (is_array($bu)) {
                $css = $css . ' .is-bu header .site-logo-img .custom-logo-link img.custom-logo { max-width: inherit; }';
            }
            
            return $css;

}
//add_action( 'astra_theme_dynamic_css', 'redwire_filter_astra_theme_bu_dynamic_css' );


function redwire_filter_bu_body_classes($classes) {

    $bu = is_bu();

    if (is_array($bu)) {
        $classes[] = 'is-bu';
        $classes[] = $bu['bu_slug'];
    }
 
    return $classes;
     
}
add_filter( 'body_class','redwire_filter_bu_body_classes' );

function test_bu() {
    return "Test BU";
}

/**
 * WPForms, update BU name field
 *
 * @param array $fields Sanitized entry field values/properties.
 * @param array $entry Original $_POST global.
 * @param array $form_data Form settings/data
 * @return array $fields
 */
function redwire_wpforms_update_bu_name_field( $fields, $entry, $form_data ) {

    // Only run on my form with ID = 1916
    if (absint($form_data['id']) !== 1916) {
        return $fields;
    }
   
    // Retrieve and insert Business Unit Name into Business Unit Name Field (field ID 6)
    $business_unit = is_bu(intval($fields[6]['value']));

    if ($business_unit) {
        $fields[6]['value'] = $business_unit['bu_name'];
    } else {
        $fields[6]['value'] = "Redwire";
    }

    return $fields;

}
add_filter( 'wpforms_process_filter', 'redwire_wpforms_update_bu_name_field', 10, 3 );
//add_action( 'wpforms_process', 'redwire_wpforms_update_bu_name_field', 10, 3 );


/**
 * Add data-image-src for parallax
 */
add_filter('astra_attr_site_output', function($output) {
    if (is_front_page() || is_page('about')) :
        return $output . 'data-position="0px, 235px" data-androidFix="false" data-bleed="100" data-speed="0.4" data-parallax="scroll" data-image-src="' . get_stylesheet_directory_uri() . '/Planet_Stars.jpg' . '"';
    endif;
    return $output;
});

// Custom Elementor widgets
add_action( 'elementor/widgets/widgets_registered', function() {
    if (class_exists('\Elementor\Plugin')):
        require_once(__DIR__.'/inc/elementor.php');

        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Redwire\ProductParametersWidget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Redwire\ProductConfigurationWidget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Redwire\ProductApplicationsWidget() );
    endif;
});

/**
 * Replace Astra footer widget menu with 
 * native WP menu so that conditional menus 
 * will work on each business units' pages.
 */

add_action( 'init', 'conditional_footer_menu' );
function conditional_footer_menu() {
  register_nav_menu('conditional-footer-menu',__( 'Conditional Footer Menu' ));
}

add_action( 'astra_footer_content_top', 'add_conditional_footer_menu' ); 
function add_conditional_footer_menu() {
wp_nav_menu( array( 'theme_location' => 'conditional-footer-menu', 'container_class' => 'conditional-footer-menu footer-adv-overlay' ) );

}

?>
