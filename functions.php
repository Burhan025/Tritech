<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Setup Theme
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

//* Set Localization (do not remove)
load_child_theme_textdomain( 'parallax', apply_filters( 'child_theme_textdomain', get_stylesheet_directory() . '/languages', 'parallax' ) );

//* Add Image upload to WordPress Theme Customizer
add_action( 'customize_register', 'parallax_customizer' );
function parallax_customizer(){
	require_once( get_stylesheet_directory() . '/lib/customize.php' );
}

//* Include Section Image CSS
include_once( get_stylesheet_directory() . '/lib/output.php' );

global $blogurl;
$blogurl = get_stylesheet_directory_uri();

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'parallax_enqueue_scripts_styles' );
function parallax_enqueue_scripts_styles() { 
	// Styles
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'oswald', 'https://fonts.googleapis.com/css?family=Oswald:400,700,300', array() );
	//wp_enqueue_script( 'oswald-dermibold', get_stylesheet_directory_uri() . '/fonts/oswald-dermi/Oswald-DemiBold.woff', array() );
	wp_enqueue_style( 'myriad_proregular', get_stylesheet_directory_uri() . '/fonts/myriad-pro-regular/myriad_pro.css', array() );


	wp_enqueue_style( 'customc', get_stylesheet_directory_uri() . '/custom.css', array() );
	wp_enqueue_style( 'mediaqueries', get_stylesheet_directory_uri() . '/mediaqueries.css', array() );
	wp_enqueue_style( 'fontawesomecss', get_stylesheet_directory_uri() . '/fonts/css/font-awesome.min.css', array() );

	wp_enqueue_style( 'fontawesomebrandscss', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/brands.min.css', array() );
	/*wp_enqueue_script( 'fontawesomebrands',   'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/webfonts/fa-brands-400.woff2', array() );*/
 

	// Scripts
	wp_enqueue_script( 'responsive-menu-js', get_stylesheet_directory_uri() . '/js/responsive-menu/responsive-menu.js', array( 'jquery' ), '1.0.0' );
}

// Removes Query Strings from scripts and styles
function remove_script_version( $src ){
  if ( strpos( $src, 'uploads/bb-plugin' ) !== false || strpos( $src, 'uploads/bb-theme' ) !== false ) {
    return $src;
  }
  else {
    $parts = explode( '?ver', $src );
    return $parts[0];
  }
}
//add_filter( 'script_loader_src', 'remove_script_version', 15, 1 );
//add_filter( 'style_loader_src', 'remove_script_version', 15, 1 );


//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//add_filter( 'genesis_header', 'genesis_search_primary_nav_menu', 10 );
function genesis_search_primary_nav_menu( $menu ){
    locate_template( array( 'searchform-header.php' ), true );
}

//* Add support for structural wraps
add_theme_support( 'genesis-structural-wraps', array(
	'header',
	'subnav',
	'footer-widgets',
	'footer',
) );

// Add Read More Link to Excerpts
add_filter('excerpt_more', 'get_read_more_link');
add_filter( 'the_content_more_link', 'get_read_more_link' );
function get_read_more_link() {
   return '...&nbsp;<a class="readmore" href="' . get_permalink() . '">Read&nbsp;More &raquo;</a>';
}

//* Add support for 4-column footer widgets
add_theme_support( 'genesis-footer-widgets', 2 );

//* Customize the entry meta in the entry header (requires HTML5 theme support)
add_filter( 'genesis_post_info', 'sp_post_info_filter' );
function sp_post_info_filter($post_info) {
	$post_info = '[post_date] [post_comments] [post_edit]';
	return $post_info;
}

//* Remove breadcrumbs and reposition them
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
//add_action( 'genesis_entry_header', 'genesis_do_breadcrumbs' );

// Widget - Latest News on home page
genesis_register_sidebar( array(
	'id'			=> 'home-latest-news',
	'name'			=> __( 'Latest News on Home Page', 'thrive' ),
	'description'	=> __( 'This is latest news home page widget', 'thrive' ),
) );

// Blog Widgets
genesis_register_sidebar( array(
	'id'			=> 'blog-sidebar',
	'name'			=> __( 'Blog Widgets', 'thrive' ),
	'description'	=> __( 'This is latest news widget', 'thrive' ),
) );

// Add Header Links Widget to Header
//add_action( 'genesis_before', 'header_widget', 1 );
	function header_widget() {
	if (is_active_sidebar( 'header-links' ) ) {
 	genesis_widget_area( 'header-links', array(
		'before' => '<div class="header-links">',
		'after'  => '</div>',
	) );
}}

// Previous / Next Post Navigation Filter For Genesis Pagination
add_filter( 'genesis_prev_link_text', 'gt_review_prev_link_text' );
function gt_review_prev_link_text() {
        $prevlink = '&laquo;';
        return $prevlink;
}
add_filter( 'genesis_next_link_text', 'gt_review_next_link_text' );
function gt_review_next_link_text() {
        $nextlink = '&raquo;';
        return $nextlink;
}

/* Subpage Header Backgrounds - Utilizes: Featured Images & Advanced Custom Fields Repeater Fields */

// AFC Repeater Setup - NOTE: Set Image Return Value to ID
// Row Field Name:
$rows = get_field('subpage_header_backgrounds', 5);
// Counts the rows and selects a random row
$row_count = count($rows);
$i = rand(0, $row_count - 1);
// Set Image size to be returned
$image_size = 'subpage-header';
// Get Image ID from the random row
$image_id = $rows[ $i ]['background_image'];
// Use Image ID to get Image Array
$image_array = wp_get_attachment_image_src($image_id, $image_size);
// Set "Default BG" to first value of the Image Array. $image_array[0] = URL;
$default_bg = $image_array[0]; 


// Custom function for getting background images
function custom_background_image($postID = "") {
	// Variables
	global $default_bg;
	global $postID;
	
	$currentID = get_the_ID();
	$blogID = get_option( 'page_for_posts');
	$parentID = wp_get_post_parent_id( $currentID );

	// is_home detects if you're on the blog page- must be set in admin area
	if( is_home() ) {
		$currentID = $blogID;
	} 
	// Else if post page, set ID to BlogID.
	elseif( is_home() || is_single() || is_archive() ) {
		$currentID = $blogID;
	}
		// Current page has a parent
		if($parentID) {
			// Try to get parents custom background
			$parent_background = wp_get_attachment_image_src(get_post_thumbnail_id($parentID), 'subpage-header');
			// Set parent background if it exists
			if($parent_background) {
				$background_image = $parent_background[0];
			}
			// Set default background
			else {
				$background_image = $default_bg;
			}
		}
		// NO parent or no parent background: set default bg.
		else {
			$background_image = $default_bg;
		}

	// Current Page has a custom background: use that
	
	return $background_image;
}

/* Changing the Copyright text */
function genesischild_footer_creds_text () {
	global $blogurl;
 	echo '<div class="clearboth copy-line">
 			<div class="credits">
 				<span>Site by</span>
 				<a target="_blank" href="https://thriveagency.com">
 					<img class="svg" src="'.  $blogurl . '/images/thrive-logo.png" alt="Thrive Internet Marketing Agency - Arlington, TX Web Design & SEO">
 				</a>
 			</div>
 			<div class="copyright first">
 				<p><span id="copy">Copyright &copy; '. date("Y") .' - All rights reserved</span> <span class="format-pipe">&#124;</span>  
	 			<a href="/careers/">Careers</a>  <span class="format-pipe">&#124;</span>  
	 			<a href="/sitemap/">Site Map</a>  <span>&#124;</span>  
	 			<a href="/privacy-policy/">Privacy Policy</a>  
	 			</p>
 			</div>
 		  </div>';
}
add_filter( 'genesis_footer_creds_text', 'genesischild_footer_creds_text' );


//* Reposition the primary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Add Additional Image Sizes
add_image_size( 'Genesis-post-thumbnail', 270, 202, true );
add_image_size( 'page-carousel-thumbnail', 218, 196, true );
//add_image_size( 'subpage-header', 1920, 362, true );

// Button Shortcode for posts
// Usage: [button url="https://www.google.com"] Button Shortcode [/button]
function download_button($atts, $content = null) {
 extract( shortcode_atts( array(
          'url' => '#'
), $atts ) );
return '<a href="'.$url.'" class="button"><span>' . do_shortcode($content) . '</span></a>';
}
add_shortcode('button', 'download_button');

//* Declare WooCommerce support
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

// Advance Custom field for Scheme Markups will be output under wphead tag
add_action('wp_head', 'add_scripts_to_wphead');
function add_scripts_to_wphead() {
	if( get_field('custom_javascript') ):	
		the_field('custom_javascript', 5);
	endif;
}

//Add Widget Area above Footer
function genesischild_footerwidgetheader() {
	genesis_register_sidebar( array(
	'id' => 'footerwidgetheader',
	'name' => __( 'Widget Above Footer for Signup Form', 'genesis' ),
	'description' => __( 'This is the Widget area Above Footer for Signup Form', 'genesis' ),
	) );

}

add_action ('widgets_init','genesischild_footerwidgetheader');

//Hook Widget Area ABove Footer
function genesischild_footerwidgetheader_position ()  {
	echo '<div class="before-footer-signup-form"><div class="wrap">';
	genesis_widget_area ('footerwidgetheader');
	echo '</div></div>';

}
add_action ('genesis_before_footer','genesischild_footerwidgetheader_position', 5 );


// Prevent TinyMCE from stripping out schema.org metadata
function schema_TinyMCE_init($in)
{
    /**
     *   Edit extended_valid_elements as needed. For syntax, see
     *   http://www.tinymce.com/wiki.php/Configuration:valid_elements
     *
     *   NOTE: Adding an element to extended_valid_elements will cause TinyMCE to ignore
     *   default attributes for that element.
     *   Eg. a[title] would remove href unless included in new rule: a[title|href]
     */
    if(!empty($in['extended_valid_elements']))
        $in['extended_valid_elements'] .= ',';

    $in['extended_valid_elements'] .= '@[id|class|style|title|itemscope|itemtype|itemprop|datetime|rel],div,dl,ul,dt,dd,li,span,a|rev|charset|href|lang|tabindex|accesskey|type|name|href|target|title|class|onfocus|onblur]';

    return $in;
}
add_filter('tiny_mce_before_init', 'schema_TinyMCE_init' );

// Site Optimizations

// Remove Assets from HOME page only
function remove_home_assets() {
  if (is_front_page()) {
      
	  wp_dequeue_style('abc_style');
	  wp_dequeue_style('jetpack_css');
	  
  }
};
add_action( 'wp_enqueue_scripts', 'remove_home_assets', 999 );

// Remove Assets Globally 
function wpfiles_dequeue() {
	if (current_user_can( 'update_core' )) {
		return;
	}
	wp_deregister_script('wp-embed');
	
}
add_action( 'wp_enqueue_scripts', 'wpfiles_dequeue', 99 );

// remove jetpack.css from frontend
add_filter( 'jetpack_implode_frontend_css', '__return_false' );