<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'avada-parent-stylesheet', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

function sd_nav_menu_attr( $atts, $item, $args )
{
	// The ID of the target menu item
	$menu_target = 8016;

	// inspect $item
	if ($item->ID == $menu_target) {
	$atts['data-toggle'] = 'modal';
    $atts['data-target'] = '.contact-us-popup';
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'sd_nav_menu_attr', 10, 3 );

function remove_dashboard_meta() {
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'admin_init', 'remove_dashboard_meta' );

function change_footer_admin () {  
  echo 'CrossFit Bath V.1';  
}  
	
add_filter('admin_footer_text', 'change_footer_admin');

/*-----------------------------------------------------------------------------------*/
/*	Customize Login Page
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'custom_login_logo_url' ) ) {
    function custom_login_logo_url() {
        return home_url();
    }
}
add_filter( 'login_headerurl', 'custom_login_logo_url' );

if ( ! function_exists( 'custom_login_logo_url_title' ) ) {
    function custom_login_logo_url_title() {
        return get_bloginfo('name');
    }
}
add_filter( 'login_headertitle', 'custom_login_logo_url_title' );

if ( ! function_exists( 'custom_login_style' ) ) {
    function custom_login_style() {
        wp_enqueue_style( 'login-style', get_template_directory_uri()."/css/login-style.css", false );
    }
}
add_action( 'login_enqueue_scripts', 'custom_login_style' );

/*-----------------------------------------------------------------------------------*/
// CUSTOM: Disable comments
/*-----------------------------------------------------------------------------------*/

// Disable support for comments and trackbacks in post types
function df_disable_comments_post_types_support() {
	$post_types = get_post_types();
	foreach ($post_types as $post_type) {
		if(post_type_supports($post_type, 'comments')) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}
}
add_action('admin_init', 'df_disable_comments_post_types_support');

// Close comments on the front-end
function df_disable_comments_status() {
	return false;
}
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);

// Hide existing comments
function df_disable_comments_hide_existing_comments($comments) {
	$comments = array();
	return $comments;
}
add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function df_disable_comments_admin_menu() {
	remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'df_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function df_disable_comments_admin_menu_redirect() {
	global $pagenow;
	if ($pagenow === 'edit-comments.php') {
		wp_redirect(admin_url()); exit;
	}
}
add_action('admin_init', 'df_disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function df_disable_comments_dashboard() {
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'df_disable_comments_dashboard');

// Remove comments links from admin bar
function df_disable_comments_admin_bar() {
	if (is_admin_bar_showing()) {
		remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
	}
}
add_action('init', 'df_disable_comments_admin_bar');

/*-----------------------------------------------------------------------------------*/
// Remove comment-reply.min.js from footer
/*-----------------------------------------------------------------------------------*/
function crunchify_clean_header_hook(){
	wp_deregister_script( 'comment-reply' );
         }
add_action('init','crunchify_clean_header_hook');

/*-----------------------------------------------------------------------------------*/
// Clean theme
/*-----------------------------------------------------------------------------------*/

add_action('init', 'disable_thumbs');
function disable_thumbs(){ 
remove_image_size('portfolio-full'); 
remove_image_size('portfolio-one'); 
//remove_image_size('portfolio-two'); 
remove_image_size('portfolio-three'); 
remove_image_size('portfolio-four'); 
//remove_image_size('portfolio-five'); 
remove_image_size('portfolio-six'); 
//remove_image_size('recent-works-thumbnail');
}

/*-----------------------------------------------------------------------------------*/
// Hide WP version strings from generator meta tag - http://wordpress.stackexchange.com/questions/211467/remove-json-api-links-in-header-html
/*-----------------------------------------------------------------------------------*/

function wpmudev_remove_version() {
return '';
}
add_filter('the_generator', 'wpmudev_remove_version');
remove_action( 'wp_head',      'rest_output_link_wp_head'              );
remove_action( 'wp_head',      'wp_oembed_add_discovery_links'         );
remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );

/*-----------------------------------------------------------------------------------*/
// Remove avada portfolio
/*-----------------------------------------------------------------------------------*/
function wpse28782_remove_menu_items() {
        remove_menu_page( 'edit.php?post_type=avada_portfolio' );
}
add_action( 'admin_menu', 'wpse28782_remove_menu_items' );

/*-----------------------------------------------------------------------------------*/
// Gravity Forms: disable view counter without invalid callback fatal errors
/*-----------------------------------------------------------------------------------*/
function cft_remove_invalid_gravity_forms_view_counter_callback() {
	global $wp_filter;

	foreach ( array( 'gform_disable_view_counter', 'gform_disable_view_counter_1' ) as $hook_name ) {
		if ( empty( $wp_filter[ $hook_name ] ) || ! $wp_filter[ $hook_name ] instanceof WP_Hook ) {
			continue;
		}

		foreach ( $wp_filter[ $hook_name ]->callbacks as $priority => $callbacks ) {
			foreach ( $callbacks as $callback_id => $callback ) {
				if ( isset( $callback['function'] ) && 'disable-gf-count-cf' === $callback['function'] ) {
					unset( $wp_filter[ $hook_name ]->callbacks[ $priority ][ $callback_id ] );
				}
			}

			if ( empty( $wp_filter[ $hook_name ]->callbacks[ $priority ] ) ) {
				unset( $wp_filter[ $hook_name ]->callbacks[ $priority ] );
			}
		}
	}
}

add_action( 'plugins_loaded', 'cft_remove_invalid_gravity_forms_view_counter_callback', 99 );
add_action( 'init', 'cft_remove_invalid_gravity_forms_view_counter_callback', 99 );
add_action( 'wp', 'cft_remove_invalid_gravity_forms_view_counter_callback', 1 );
add_filter( 'gform_disable_view_counter', '__return_true' );

/*-----------------------------------------------------------------------------------*/
// Homepage hero: scroll cue + keep peek height after Avada fullscreen JS
/*-----------------------------------------------------------------------------------*/
function cft_home_scroll_cue() {
	if ( ! is_front_page() ) {
		return;
	}
	?>
	<script>
	(function () {
		function placeCue() {
			var container = document.getElementById('sliders-container');
			if (!container || document.querySelector('.home-scroll-cue')) return;
			var cue = document.createElement('a');
			cue.href = '#main';
			cue.className = 'home-scroll-cue';
			cue.setAttribute('aria-label', 'Scroll to more content');
			cue.innerHTML = '<span class="home-scroll-cue-label">Scroll</span><span class="home-scroll-cue-icon" aria-hidden="true"></span>';
			container.appendChild(cue);
		}

		function setHeroPeek() {
			if (!document.body.classList.contains('home')) return;
			var peek = window.matchMedia('(max-width: 850px)').matches ? 56 : 72;
			var h = Math.max(window.innerHeight - peek, 420);
			var selectors = [
				'.home .fusion-slider-container',
				'.home .fusion-slider-container .flexslider',
				'.home .fusion-slider-container .flexslider .slides',
				'.home .fusion-slider-container .flexslider .slides > li'
			];
			document.querySelectorAll(selectors.join(',')).forEach(function (el) {
				el.style.setProperty('height', h + 'px', 'important');
				el.style.setProperty('max-height', h + 'px', 'important');
			});
		}

		function init() {
			placeCue();
			setHeroPeek();
			setTimeout(setHeroPeek, 150);
			setTimeout(setHeroPeek, 600);
		}

		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', init);
		} else {
			init();
		}
		window.addEventListener('load', function () {
			setHeroPeek();
			setTimeout(setHeroPeek, 300);
		});
		window.addEventListener('resize', setHeroPeek);
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'cft_home_scroll_cue', 40 );

