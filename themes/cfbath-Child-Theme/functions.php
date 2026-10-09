<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'avada-parent-stylesheet', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function cft_type_fonts() {
	wp_enqueue_style(
		'cft-type-fonts',
		'https://fonts.googleapis.com/css?family=Archivo:400,600,700&display=swap',
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'cft_type_fonts', 20 );

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

/**
 * Mobile header phone icon.
 * Overrides Avada parent avada_modern_menu() so the number lives in the child theme.
 */
if ( ! function_exists( 'avada_modern_menu' ) ) {
	function avada_modern_menu() {
		$html = '';

		if ( 'modern' == Avada()->settings->get( 'mobile_menu_design' ) ) {
			$header_content_3 = Avada()->settings->get( 'header_v4_content' );

			$html .= '<div class="fusion-mobile-menu-icons">';
			if ( ! function_exists( 'ubermenu_get_menu_instance_by_theme_location' ) || ( function_exists( 'ubermenu_get_menu_instance_by_theme_location' ) && ! ubermenu_get_menu_instance_by_theme_location( 'main_navigation' ) ) ) {
				$html .= '<a href="#" class="fusion-icon fusion-icon-bars"></a><a href="tel:07825337397" style="line-height: 22px"><i class="fa fa-phone"></i></a>';
			}

			if ( ( 'v4' == Avada()->settings->get( 'header_layout' ) || 'Top' != Avada()->settings->get( 'header_position' ) ) && ( 'Tagline And Search' == $header_content_3 || 'Search' == $header_content_3 ) ) {
				$html .= '<a href="#" class="fusion-icon fusion-icon-search"></a>';
			}
			if ( class_exists( 'WooCommerce' ) && Avada()->settings->get( 'woocommerce_cart_link_main_nav' ) ) {
				$html .= '<a href="' . get_permalink( get_option( 'woocommerce_cart_page_id' ) ) . '" class="fusion-icon fusion-icon-shopping-cart"></a>';
			}
			$html .= '</div>';
		}
		return $html;
	}
}

/**
 * Home Instagram grid. Renders Smash Balloon when an account is connected;
 * admins see a setup link until then.
 */
function cft_instagram_feed_shortcode() {
	if ( ! shortcode_exists( 'instagram-feed' ) ) {
		return '';
	}

	global $wpdb;
	$sources_table = $wpdb->prefix . 'sbi_sources';
	$connected     = 0;
	$table_exists  = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $sources_table ) );
	if ( $table_exists ) {
		$connected = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$sources_table}" );
	}

	if ( $connected < 1 ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}
		$setup = admin_url( 'admin.php?page=sbi-feed-builder' );
		return '<p class="cft-ig-setup">Connect <strong>@the.bridge.sc</strong> in Smash Balloon to show six photos here. <a href="' . esc_url( $setup ) . '">Open Instagram setup</a></p>';
	}

	return do_shortcode( '[instagram-feed num=6 cols=6 colstablet=3 colsmobile=2 showheader=false showbutton=false showfollow=false imagepadding=4]' );
}
add_shortcode( 'cft_instagram_feed', 'cft_instagram_feed_shortcode' );

/**
 * Programmes only: three muted looping portrait clips under the page title.
 * Hooked on avada_before_main so it sits after the title bar, not inside it.
 */
function cft_programmes_video_strip() {
	if ( ! is_page( 12401 ) ) {
		return;
	}

	$base  = trailingslashit( content_url( 'uploads/cft-programmes' ) );
	$clips = array(
		array(
			'mp4'     => 'cft-programmes-bike.mp4',
			'webm'    => 'cft-programmes-bike.webm',
			'poster'  => 'cft-programmes-bike.jpg',
			'primary' => false,
		),
		array(
			'mp4'     => 'cft-programmes-coach.mp4',
			'webm'    => 'cft-programmes-coach.webm',
			'poster'  => 'cft-programmes-coach.jpg',
			'primary' => true,
		),
		array(
			'mp4'     => 'cft-programmes-pullup.mp4',
			'webm'    => 'cft-programmes-pullup.webm',
			'poster'  => 'cft-programmes-pullup.jpg',
			'primary' => false,
		),
	);
	?>
	<section class="cft-programmes-strip" aria-label="Gym footage">
		<p class="screen-reader-text">Gym footage</p>
		<div class="cft-programmes-strip-inner">
			<?php foreach ( $clips as $clip ) : ?>
				<?php $is_primary = ! empty( $clip['primary'] ); ?>
				<video
					class="cft-programmes-clip<?php echo $is_primary ? ' is-primary' : ''; ?>"
					<?php echo $is_primary ? 'autoplay' : ''; ?>
					muted
					loop
					playsinline
					webkit-playsinline
					preload="<?php echo $is_primary ? 'auto' : 'none'; ?>"
					poster="<?php echo esc_url( $base . $clip['poster'] ); ?>"
					aria-hidden="true"
					disablepictureinpicture
					controlslist="nodownload nofullscreen noremoteplayback"
				>
					<source src="<?php echo esc_url( $base . $clip['webm'] ); ?>" type="video/webm">
					<source src="<?php echo esc_url( $base . $clip['mp4'] ); ?>" type="video/mp4">
				</video>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}
add_action( 'avada_before_main', 'cft_programmes_video_strip', 5 );

function cft_programmes_video_strip_play() {
	if ( ! is_page( 12401 ) ) {
		return;
	}
	?>
	<script>
	(function () {
		var mq = window.matchMedia('(max-width: 850px)');
		document.querySelectorAll('.cft-programmes-strip video').forEach(function (video) {
			video.muted = true;
			video.defaultMuted = true;
			video.setAttribute('muted', '');
			video.playsInline = true;
			if (mq.matches && !video.classList.contains('is-primary')) {
				return;
			}
			var play = video.play();
			if (play && play.catch) {
				play.catch(function () {});
			}
		});
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'cft_programmes_video_strip_play', 30 );

function cft_sc_body_class( $classes ) {
	if ( is_page( 'the-bridge' ) ) {
		$classes[] = 'cft-sc-page';
	}
	if ( is_page( array( 'community', 'bring-a-friend', 'fittest-in-ba', 'charity-throwdown', 'mumfit', 'new-member-meet-and-greet', 'quiz-night' ) ) ) {
		$classes[] = 'cft-community-page';
	}
	if ( is_page( 'community' ) ) {
		$classes[] = 'cft-community-hub';
	}
	return $classes;
}
add_filter( 'body_class', 'cft_sc_body_class' );

/**
 * Home has no title bar. The slider logo is an H2 whose only content is a
 * link to this page, so Google's first heading is the URL. Keep the logo,
 * and give the page a real H1.
 */
function cft_home_heading_buffer_start() {
	if ( is_front_page() ) {
		ob_start( 'cft_home_heading_buffer' );
	}
}
add_action( 'template_redirect', 'cft_home_heading_buffer_start' );

function cft_home_heading_buffer( $html ) {
	if ( ! is_string( $html ) || '' === $html ) {
		return $html;
	}

	$html = preg_replace(
		'/<div class="heading ([^"]*)"><h2 style="([^"]*)">(.*?)<\/h2><\/div>/s',
		'<div class="heading $1"><div class="cft-slide-logo" style="$2">$3</div></div>',
		$html,
		1
	);

	$html = preg_replace(
		'/<div class="caption ([^"]*)"><h3 style="([^"]*)">(.*?)<\/h3><\/div>/s',
		'<div class="caption $1"><p class="cft-slide-caption" style="$2">$3</p></div>',
		$html,
		1
	);

	$h1       = '<h1 class="cft-home-h1">CrossFit and open gym in Trowbridge</h1>';
	$replaced = preg_replace(
		'/(<h2 style="text-align: left; margin-top: 0px !important;">)/',
		$h1 . '$1',
		$html,
		1,
		$count
	);
	if ( $count ) {
		$html = $replaced;
	}

	return $html;
}

/**
 * Community hub: three muted portrait clips under the title.
 */
function cft_community_video_strip() {
	if ( ! is_page( 'community' ) ) {
		return;
	}

	$base  = trailingslashit( content_url( 'uploads/cft-community' ) );
	$clips = array(
		array(
			'stem'    => 'cft-comm-bar',
			'primary' => false,
		),
		array(
			'stem'    => 'cft-comm-pull',
			'primary' => true,
		),
		array(
			'stem'    => 'cft-comm-step',
			'primary' => false,
		),
	);
	?>
	<section class="cft-comm-strip" aria-label="Gym footage">
		<p class="screen-reader-text">Gym footage</p>
		<div class="cft-comm-strip-inner">
			<?php foreach ( $clips as $clip ) : ?>
				<?php $is_primary = ! empty( $clip['primary'] ); ?>
				<video
					class="cft-comm-clip<?php echo $is_primary ? ' is-primary' : ''; ?>"
					<?php echo $is_primary ? 'autoplay' : ''; ?>
					muted
					loop
					playsinline
					webkit-playsinline
					preload="<?php echo $is_primary ? 'auto' : 'none'; ?>"
					poster="<?php echo esc_url( $base . $clip['stem'] . '.jpg' ); ?>"
					aria-hidden="true"
					disablepictureinpicture
					controlslist="nodownload nofullscreen noremoteplayback"
				>
					<source src="<?php echo esc_url( $base . $clip['stem'] . '.webm' ); ?>" type="video/webm">
					<source src="<?php echo esc_url( $base . $clip['stem'] . '.mp4' ); ?>" type="video/mp4">
				</video>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}
add_action( 'avada_before_main', 'cft_community_video_strip', 5 );

function cft_community_video_strip_play() {
	if ( ! is_page( 'community' ) ) {
		return;
	}
	?>
	<script>
	(function () {
		var mq = window.matchMedia('(max-width: 850px)');
		document.querySelectorAll('.cft-comm-strip video').forEach(function (video) {
			video.muted = true;
			video.defaultMuted = true;
			video.setAttribute('muted', '');
			video.playsInline = true;
			if (mq.matches && !video.classList.contains('is-primary')) {
				return;
			}
			var play = video.play();
			if (play && play.catch) {
				play.catch(function () {});
			}
		});
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'cft_community_video_strip_play', 30 );

/**
 * Bring a friend: WodBoard popup for form 526|1083|9183a314dd9570eb.
 * The loader does not open the form. The button on the page does.
 */
/**
 * Reusable FAQ accordion. Question is the first line, answer is the rest.
 *
 * [cft_faq]
 * [cft_faq_item]
 * Question text
 * Answer text, including links.
 * [/cft_faq_item]
 * [/cft_faq]
 */
function cft_faq_shortcode( $atts, $content = null ) {
	$atts = shortcode_atts(
		array(
			'title' => 'Questions people ask',
		),
		$atts,
		'cft_faq'
	);

	preg_match_all( '/\[cft_faq_item\](.*?)\[\/cft_faq_item\]/s', (string) $content, $matches );
	if ( empty( $matches[1] ) ) {
		return '';
	}

	$html  = '<section class="cft-faq">';
	$title = trim( (string) $atts['title'] );
	if ( '' !== $title ) {
		$html .= '<h2>' . esc_html( $title ) . '</h2>';
	}
	$html .= '<div class="cft-faq-list">';

	foreach ( $matches[1] as $raw ) {
		$raw = trim( $raw );
		if ( '' === $raw ) {
			continue;
		}
		$parts    = preg_split( "/\r\n|\n|\r/", $raw, 2 );
		$question = trim( $parts[0] );
		$answer   = isset( $parts[1] ) ? trim( $parts[1] ) : '';
		if ( '' === $question ) {
			continue;
		}
		$html .= '<details class="cft-faq-item">';
		$html .= '<summary><h3>' . esc_html( $question ) . '</h3></summary>';
		$html .= '<div class="cft-faq-a">' . wpautop( wp_kses_post( $answer ) ) . '</div>';
		$html .= '</details>';
	}

	$html .= '</div></section>';
	return $html;
}
add_shortcode( 'cft_faq', 'cft_faq_shortcode' );

function cft_community_disable_wpautop() {
	if ( is_page( array( 'community', 'bring-a-friend', 'fittest-in-ba', 'charity-throwdown', 'mumfit', 'new-member-meet-and-greet', 'quiz-night' ) ) ) {
		remove_filter( 'the_content', 'wpautop' );
		remove_filter( 'the_content', 'shortcode_unautop' );
	}
}
add_action( 'wp', 'cft_community_disable_wpautop' );

function cft_bring_a_friend_form() {
	if ( ! is_page( 'bring-a-friend' ) ) {
		return;
	}
	echo '<script>(function(e,v,i,n,j,m){m=e.getElementById("evini_popup_script");n=m||e.createElement(v);n.dataset.eviniFormId=[n.dataset.eviniFormId||\'\',"526|1083|9183a314dd9570eb"].join(",");;if(!m){n.id="evini_popup_script";n.async=1;n.src=i;j=e.querySelector(v);j.parentNode.insertBefore(n,j)}})(document,"script","https://www.wodboard.com/popup.js?t=1774958702");</script>';
}
add_action( 'wp_footer', 'cft_bring_a_friend_form', 20 );

/**
 * Muted looping open-gym clips on The Bridge S&C page (hours + kit rows).
 */
function cft_sc_clip_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'clip' => '' ), $atts );
	$clip = sanitize_key( $atts['clip'] );
	if ( ! in_array( $clip, array( 'kit', 'film' ), true ) ) {
		return '';
	}

	$base = trailingslashit( content_url( 'uploads' ) );
	$stem = ( 'film' === $clip ) ? 'cft-gym-film' : 'cft-gym-sc-' . $clip;
	$ver  = ( 'film' === $clip ) ? '20261009a' : '20260930d';
	$title = 'Walkthrough of The Bridge Strength & Conditioning open gym in Trowbridge';
	ob_start();
	?>
	<video
		class="cft-sc-clip"
		autoplay
		muted
		loop
		playsinline
		webkit-playsinline
		preload="auto"
		poster="<?php echo esc_url( $base . $stem . '.webp?v=' . $ver ); ?>"
		title="<?php echo esc_attr( $title ); ?>"
		aria-hidden="true"
		disablepictureinpicture
		controlslist="nodownload nofullscreen noremoteplayback"
	>
		<source src="<?php echo esc_url( $base . $stem . '.webm?v=' . $ver ); ?>" type="video/webm">
		<source src="<?php echo esc_url( $base . $stem . '.mp4?v=' . $ver ); ?>" type="video/mp4">
	</video>
	<?php
	return ob_get_clean();
}
add_shortcode( 'cft_sc_clip', 'cft_sc_clip_shortcode' );

function cft_sc_clip_play() {
	if ( ! is_page( 'the-bridge' ) ) {
		return;
	}
	?>
	<script>
	(function () {
		document.querySelectorAll('video.cft-sc-clip').forEach(function (video) {
			video.muted = true;
			video.defaultMuted = true;
			video.setAttribute('muted', '');
			video.playsInline = true;
			var play = video.play();
			if (play && play.catch) {
				play.catch(function () {});
			}
		});
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'cft_sc_clip_play', 30 );

/**
 * Avada registers portfolio URLs from $smof_data['portfolio_slug'].
 * After a rewrite flush that global is empty, so stories 404 at
 * /testimonials/{slug}/ while the listing still links there.
 */
function cft_portfolio_testimonials_slug() {
	register_post_type(
		'avada_portfolio',
		array(
			'labels'          => array(
				'name'          => 'Portfolio',
				'singular_name' => 'Portfolio',
			),
			'public'          => true,
			'has_archive'     => true,
			'rewrite'         => array(
				'slug' => 'testimonials',
			),
			'supports'        => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', 'page-attributes', 'post-formats' ),
			'can_export'      => true,
		)
	);
}
add_action( 'init', 'cft_portfolio_testimonials_slug', 20 );

function cft_mobile_nav_open_class() {
	?>
	<script>
	(function () {
		function syncNav() {
			var open = false;
			document.querySelectorAll('.fusion-mobile-nav-holder').forEach(function (el) {
				if (el.classList.contains('fusion-mobile-sticky-nav-holder')) {
					return;
				}
				if (el.offsetHeight > 0 && window.getComputedStyle(el).display !== 'none') {
					open = true;
				}
			});
			document.documentElement.classList.toggle('cft-nav-open', open);
		}
		document.addEventListener('click', function () {
			window.setTimeout(syncNav, 0);
		});
		window.addEventListener('resize', syncNav);
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'cft_mobile_nav_open_class', 50 );

function cft_home_meta_description() {
	if ( ! is_front_page() || defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
		return;
	}

	$description = 'Open gym from 4am to 11pm, seven days a week, plus CrossFit classes at The Bridge in Trowbridge. Free parking for 30 cars. Book a free taster.';
	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}
add_action( 'wp_head', 'cft_home_meta_description', 1 );

function cft_community_document_title( $title ) {
	if ( is_page( 'community' ) ) {
		return 'Gym Events and Community | CrossFit Trowbridge';
	}
	if ( is_page( 'fittest-in-ba' ) ) {
		return 'Fittest in BA 2026 | Fitness Competition, Trowbridge';
	}
	if ( is_page( 'bring-a-friend' ) ) {
		return 'Bring a Friend to CrossFit | CrossFit Trowbridge';
	}
	if ( is_page( 'mumfit' ) ) {
		return 'MumFit: Postnatal Fitness Classes in Trowbridge';
	}
	if ( is_page( 'charity-throwdown' ) ) {
		return 'Charity Pairs Throwdown | CrossFit Trowbridge';
	}
	if ( is_page( 'new-member-meet-and-greet' ) ) {
		return 'New Member Meet and Greet | CrossFit Trowbridge';
	}
	if ( is_page( 'quiz-night' ) ) {
		return 'Quiz Night, 13 November | CrossFit Trowbridge';
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'cft_community_document_title' );

function cft_community_meta_description() {
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
		return;
	}

	$description = '';
	if ( is_page( 'community' ) ) {
		$description = 'Partner workouts, MumFit, Fittest in BA, charity throwdowns and quiz nights at The Bridge in Trowbridge. See what\'s on, who it\'s for and how to book.';
	} elseif ( is_page( 'fittest-in-ba' ) ) {
		$description = 'Fittest in BA finds the fittest athlete in the BA postcode. Three events in 90 minutes at The Bridge, Trowbridge, on Saturday 17 October 2026.';
	} elseif ( is_page( 'bring-a-friend' ) ) {
		$description = 'Bring a friend to a coached class at The Bridge, Trowbridge. Next partner workout Wednesday 14 October. If they join, your next month is half price.';
	} elseif ( is_page( 'mumfit' ) ) {
		$description = 'MumFit is a postnatal fitness class at CrossFit Trowbridge. Train with your baby, no childcare needed. Mondays and Fridays at 12:30pm at The Bridge.';
	} elseif ( is_page( 'charity-throwdown' ) ) {
		$description = 'An annual pairs fitness competition at The Bridge, Trowbridge, raising money for Child.org and Make 2nds Count. Three workouts, any ability, cake after.';
	} elseif ( is_page( 'new-member-meet-and-greet' ) ) {
		$description = 'New to CrossFit Trowbridge? Join the meet and greet on the last Saturday of each month: group workout, coffee and cake on us, and time with the coaches.';
	} elseif ( is_page( 'quiz-night' ) ) {
		$description = 'CrossFit Trowbridge quiz night on Friday 13 November. £5 per person, teams of about six. Book your place on WodBoard, just like a class.';
	}
	if ( ! $description ) {
		return;
	}

	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}
add_action( 'wp_head', 'cft_community_meta_description', 1 );

