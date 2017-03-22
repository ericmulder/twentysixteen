<?php
/**
 * Twenty Sixteen functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

/**
 * Twenty Sixteen only works in WordPress 4.4 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.4-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
}

//force the use of a child theme!
add_action('after_switch_theme', 'wptakeoff_setup_options');
function wptakeoff_setup_options() {
	$my_theme = wp_get_theme();

	if($my_theme->get( 'Name' ) != 'WP Takeoff') {
		return;
	}

	$templatefolder = dirname(__FILE__) . '/wptakeoff-child';
	$childfolder = dirname(__FILE__) . '/../wptakeoff-child';
	if(!file_exists($childfolder . '/style.css')) {
		mkdir($childfolder);
		copy($templatefolder.'/style.css', $childfolder . '/style.css');
		copy($templatefolder.'/functions.php', $childfolder . '/functions.php');
		copy($templatefolder.'/screenshot.png', $childfolder . '/screenshot.png');
	}
	switch_theme('wptakeoff-child');

}

if ( ! function_exists( 'twentysixteen_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * Create your own twentysixteen_setup() function to override in a child theme.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed at WordPress.org. See: https://translate.wordpress.org/projects/wp-themes/twentysixteen
	 * If you're building a theme based on Twenty Sixteen, use a find and replace
	 * to change 'twentysixteen' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'twentysixteen' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for custom logo.
	 *
	 *  @since Twenty Sixteen 1.2
	 */
	add_theme_support( 'custom-logo', array(
		'flex-height' => true,
		'flex-width' => true,
	) );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 1200, 9999 );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'twentysixteen' ),
		'secundary'  => __( 'Secundary Menu', 'twentysixteen' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	/*
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
		'gallery',
		'status',
		'audio',
		'chat',
	) );
	*/

	// Indicate widget sidebars can use selective refresh in the Customizer.
	add_theme_support( 'customize-selective-refresh-widgets' );
}
endif; // twentysixteen_setup
add_action( 'after_setup_theme', 'twentysixteen_setup' );

/**
 * Sets the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'twentysixteen_content_width', 840 );
}
add_action( 'after_setup_theme', 'twentysixteen_content_width', 0 );

/**
 * Registers a widget area.
 *
 * @link https://developer.wordpress.org/reference/functions/register_sidebar/
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'twentysixteen' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'twentysixteen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	//footer widgets
	for($t = 1; $t <= get_theme_mod('footer_rows', '0'); $t++) {
		for($x = 1; $x <= get_theme_mod('footer'.$t.'_columns', '1'); $x++) {
			register_sidebar( array(
				'name'          => __( 'Content Footer ' . $t . '-' .$x , 'twentysixteen' ),
				'id'            => 'footer_'.$t.'_'.$x,
				'description'   => __( 'Appears in the footer.', 'twentysixteen' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			) );
		}
	}
}
add_action( 'widgets_init', 'twentysixteen_widgets_init' );

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'twentysixteen_javascript_detection', 0 );

/**
 * Enqueues scripts and styles.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_scripts() {

	// Theme stylesheet.
	wp_enqueue_style( 'twentysixteen-style', get_stylesheet_uri() );

	// Load the Internet Explorer specific stylesheet.
	wp_enqueue_style( 'twentysixteen-ie', get_template_directory_uri() . '/css/ie.css', array( 'twentysixteen-style' ), '20160816' );
	wp_style_add_data( 'twentysixteen-ie', 'conditional', 'lt IE 10' );

	// Load the Internet Explorer 8 specific stylesheet.
	wp_enqueue_style( 'twentysixteen-ie8', get_template_directory_uri() . '/css/ie8.css', array( 'twentysixteen-style' ), '20160816' );
	wp_style_add_data( 'twentysixteen-ie8', 'conditional', 'lt IE 9' );

	// Load the Internet Explorer 7 specific stylesheet.
	wp_enqueue_style( 'twentysixteen-ie7', get_template_directory_uri() . '/css/ie7.css', array( 'twentysixteen-style' ), '20160816' );
	wp_style_add_data( 'twentysixteen-ie7', 'conditional', 'lt IE 8' );

	// Load the html5 shiv.
	wp_enqueue_script( 'twentysixteen-html5', get_template_directory_uri() . '/js/html5.js', array(), '3.7.3' );
	wp_script_add_data( 'twentysixteen-html5', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'twentysixteen-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20160816', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'twentysixteen-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20160816' );
	}

	wp_enqueue_script( 'twentysixteen-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20160816', true );

	wp_localize_script( 'twentysixteen-script', 'screenReaderText', array(
		'expand'   => __( 'expand child menu', 'twentysixteen' ),
		'collapse' => __( 'collapse child menu', 'twentysixteen' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'twentysixteen_scripts' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @since Twenty Sixteen 1.0
 *
 * @param array $classes Classes for the body element.
 * @return array (Maybe) filtered body classes.
 */
function twentysixteen_body_classes( $classes ) {
	// Adds a class of custom-background-image to sites with a custom background image.
	if ( get_background_image() ) {
		$classes[] = 'custom-background-image';
	}

	// Adds a class of group-blog to sites with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of no-sidebar to sites without active sidebar.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'twentysixteen_body_classes' );

/**
 * Converts a HEX value to RGB.
 *
 * @since Twenty Sixteen 1.0
 *
 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
 * @return array Array containing RGB (red, green, and blue) values for the given
 *               HEX code, empty array otherwise.
 */
function twentysixteen_hex2rgb( $color ) {
	$color = trim( $color, '#' );

	if ( strlen( $color ) === 3 ) {
		$r = hexdec( substr( $color, 0, 1 ).substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ).substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ).substr( $color, 2, 1 ) );
	} else if ( strlen( $color ) === 6 ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
	} else {
		return array();
	}

	return array( 'red' => $r, 'green' => $g, 'blue' => $b );
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for content images
 *
 * @since Twenty Sixteen 1.0
 *
 * @param string $sizes A source size value for use in a 'sizes' attribute.
 * @param array  $size  Image size. Accepts an array of width and height
 *                      values in pixels (in that order).
 * @return string A source size value for use in a content image 'sizes' attribute.
 */
function twentysixteen_content_image_sizes_attr( $sizes, $size ) {
	$width = $size[0];

	840 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 62vw, 840px';

	if ( 'page' === get_post_type() ) {
		840 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
	} else {
		840 > $width && 600 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 61vw, (max-width: 1362px) 45vw, 600px';
		600 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
	}

	return $sizes;
}
add_filter( 'wp_calculate_image_sizes', 'twentysixteen_content_image_sizes_attr', 10 , 2 );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for post thumbnails
 *
 * @since Twenty Sixteen 1.0
 *
 * @param array $attr Attributes for the image markup.
 * @param int   $attachment Image attachment ID.
 * @param array $size Registered image size or flat array of height and width dimensions.
 * @return string A source size value for use in a post thumbnail 'sizes' attribute.
 */
function twentysixteen_post_thumbnail_sizes_attr( $attr, $attachment, $size ) {
	if ( 'post-thumbnail' === $size ) {
		is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 60vw, (max-width: 1362px) 62vw, 840px';
		! is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 88vw, 1200px';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'twentysixteen_post_thumbnail_sizes_attr', 10 , 3 );

/**
 * Modifies tag cloud widget arguments to have all tags in the widget same font size.
 *
 * @since Twenty Sixteen 1.1
 *
 * @param array $args Arguments for tag cloud widget.
 * @return array A new modified arguments.
 */
function twentysixteen_widget_tag_cloud_args( $args ) {
	$args['largest'] = 1;
	$args['smallest'] = 1;
	$args['unit'] = 'em';
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'twentysixteen_widget_tag_cloud_args' );

/*
 * Required Plugins TGM
 */
require_once get_template_directory() . '/inc/required-plugins.php';

/**
 * Check to see if the current page is the login/register page
 * Use this in conjunction with is_admin() to separate the front-end from the back-end of your theme
 * @return bool
 */
if ( ! function_exists( 'is_login_page' ) ) {
  function is_login_page() {
    return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );
  }
}

/*
 * Add custom CSS from Customizer
 */
function wpto_icon_checker() {
    if ( ! is_admin() && ! is_login_page() ) {
        // Enqueue scripts, do theme magic, etc.
	if(get_theme_mod('use_icon_set', false)) {
		wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css' );
	}
    }
}

add_action( 'wp', 'wpto_icon_checker' );
add_action( 'wp_head', 'wpto_customizer_css');
function wpto_customizer_css()
{
	$google_fonts = array();
	$body_font = wpto_font_parser('body', $google_fonts);
	$heading_font = wpto_font_parser('heading', $google_fonts);
    ?>
	<!-- customizer override -->
         <style type="text/css">
	     html {
		background: <?php echo get_theme_mod('background_color', '#000'); ?>;
	     }
	     body {
		font-size: <?php echo get_theme_mod('body_size', false) ? get_theme_mod('body_size') . 'px' : 'inherit'; ?> !important;
		<?php if($body_font['font-family'] != 'inherit') : ?>
			font-family: '<?php echo $body_font['font-family']; ?>' !important;
		<?php endif; ?>
		<?php if($body_font['font-italic']) : ?>
			font-style: italic;
		<?php endif; ?>
		<?php if($body_font['font-weight']) : ?>
			font-weight: <?php echo $body_font['font-weight']; ?> !important;
		<?php endif; ?>
		<?php if($body_font['line-height']) : ?>
			line-height: <?php echo $body_font['line-height']; ?> !important;
		<?php endif; ?>
	     }
	     <?php if($body_font['line-height']) : ?>
		p {
			line-height: <?php echo $body_font['line-height']; ?> !important;
		}
	     <?php endif; ?>
             h1, h1 a, h2, h2 a, h3, h3 a, h4, h4 a, h5, h5 a, h6, h6 a, h7, h7 a  {
		color: <?php echo get_theme_mod('heading_color', '#43C6E4'); ?> !important;
		font-size: <?php echo get_theme_mod('heading_size', false) ? get_theme_mod('heading_size') . 'px' : 'inherit'; ?> !important;
		<?php if($heading_font['font-family'] != 'inherit') : ?>
			font-family: '<?php echo $heading_font['font-family']; ?>' !important;
		<?php endif; ?>
		<?php if($heading_font['font-italic']) : ?>
			font-style: italic;
		<?php endif; ?>
		<?php if($heading_font['font-weight']) : ?>
			font-weight: <?php echo $heading_font['font-weight']; ?> !important;
		<?php endif; ?>
		<?php if($heading_font['line-height']) : ?>
			line-height: <?php echo $heading_font['line-height']; ?> !important;
		<?php endif; ?>
	     }

	     <?php if(get_theme_mod('heading_size1', 'inherit') != 'inherit') : ?>
	     h1, h1 a {
		font-size: <?= get_theme_mod('heading_size1', 'inherit'); ?>px !important;
	     }
	     <?php endif; ?>

	     <?php if(get_theme_mod('heading_size2', 'inherit') != 'inherit') : ?>
	     h2, h2 a {
		font-size: <?= get_theme_mod('heading_size2', 'inherit'); ?>px !important;
	     }
	     <?php endif; ?>

	     <?php if(get_theme_mod('heading_size3', 'inherit') != 'inherit') : ?>
	     h3, h3 a {
		font-size: <?= get_theme_mod('heading_size3', 'inherit'); ?>px !important;
	     }
	     <?php endif; ?>

	     /* max width */
	     .site-header-main, .header-image {
		width: <?= get_theme_mod('header_max_width', '100%'); ?> !important;
		max-width: 100%;
		margin: 0 auto;
	     }
	     body:not(.page-template-full-width) .site-content {
		width: <?= get_theme_mod('body_max_width', '100%'); ?> !important;
		max-width: 100%;
		margin: 0 auto;
	     }
	     .site-footer {
		margin: 0 auto;
	     }
	     /** HEADER POSITIONS **/
	     <?php if(get_theme_mod('logo_position', 'left') == 'center') : ?>
			.site-branding {
				width: 100%;
				text-align: center;
				margin: 25px;
			}
	     <?php endif; ?>

	     <?php if(get_theme_mod('logo_position', 'left') == 'above') : ?>
			header#masthead.site-header .site-branding {
				padding-top: 10px;
				width: 100%;
			}
	     <?php endif; ?>

	     <?php if(get_theme_mod('menu1_position', 'left') == 'center') : ?>
			.site-header-menu {
				margin: 0;
			}
			#site-navigation {
				float: none;
			}
			.menu-hoofmenu-container {
				text-align: center;
			}
			ul#menu-hoofmenu.primary-menu {
				display: inline-block;
				width: auto;
			}

	     <?php endif; ?>
	     /** MENU **/
	     <?php
		$menu1_font = wpto_font_parser('menu1_font', $google_fonts);
	     ?>
	     .main-navigation a {
		color: <?= get_theme_mod('menu1_font_color', '#000'); ?> !important;
		font-family: <?= $menu1_font['font-family']; ?>;
		font-weight: <?= $menu1_font['font-weight']; ?>;
	     }
	     .menu-toggle .line {
		background-color: <?= get_theme_mod('menu1_font_color', '#000'); ?> !important;
	     }
	     .site-header-menu.toggled-on, .no-js .site-header-menu {
		width: 100%;
	     }
	     .site-header-menu.toggled-on #menu-hoofmenu {
		width: 100% !important;
		margin-top: 15px !important;
		display: block;
	     }
	     .main-navigation a:hover, .main-navigation .current-menu-item > a, .main-navigation .current-menu-ancestor > a {
		color: <?= get_theme_mod('menu1_font_color_active', '#999'); ?> !important;
	     }
	     /** Header sizes **/
	     <?php
	     if(get_theme_mod('header_sticky', false)) : ?>
		header#masthead.site-header.sticky {
			position: fixed;
			z-index: 999;
		}
	     	@media(min-width: 44.375em) {
			header#masthead.site-header.sticky {
				height: <?= get_theme_mod('header_sticky_height', '30px'); ?>;
				position: fixed;
			}
			header#masthead.site-header.sticky {
				height: <?= get_theme_mod('header_sticky_height', '30px'); ?>;
			}
			header#masthead.site-header.sticky .site-branding,
			header#masthead.site-header.sticky .site-header-menu,
			header#masthead.site-header.sticky .header-image {
				height: <?= get_theme_mod('header_sticky_height', '30px'); ?>;
				transition: height 500ms ease-in-out;
			}
			header#masthead.site-header.sticky .site-branding img {
				height: <?= get_theme_mod('header_sticky_height', '30px'); ?>;
				width: auto;
			}
			header#masthead.site-header.sticky li.menu-item a {
				line-height: <?= get_theme_mod('header_sticky_height', '100px'); ?>;
			}
		}
		header#masthead.site-header {
			position: absolute;
			z-index: 99;
			width: 100%;
		}
		.site-content {
			padding-top:<?= get_theme_mod('header_height', '100px'); ?>;
		}
		.sticky .site-content {
			padding-top:<?= get_theme_mod('header_sticky_height', '100px'); ?>;
		}
	     <?php endif; ?>

		.menu-toggle {
			top: <?= intval(get_theme_mod('header_height_responsive', '30')) / 2; ?>px;
		}
		.site-header-menu.toggled-on {
			top: <?= get_theme_mod('header_height_responsive', '30px'); ?>;
		}

	     <?php if(get_theme_mod('header_height', false)) : ?>
		header#masthead.site-header .site-header-menu {
			height: <?= get_theme_mod('header_height', '100px'); ?>;
			width: auto;
			transition: all .35s ease-in-out;
			-webkit-transition: all .35s ease-in-out
		}

		header#masthead.site-header {
			transition: all .35s ease-in-out;
			-webkit-transition: all .35s ease-in-out;
		}
		header#masthead.site-header li.menu-item a {
			line-height: <?= get_theme_mod('header_height', '100px'); ?>;
			transition: all .35s ease-in-out;
			-webkit-transition: all .35s ease-in-out
		}
		<?php
		//resize logo to line height if it floats
		if(in_array(get_theme_mod('logo_position'), array('left', 'right'))) : ?>
			header#masthead.site-header .site-branding,
			header#masthead.site-header .site-branding img,
			header#masthead.site-header .header-image {
				height: <?= get_theme_mod('header_height', '100px'); ?>;
				width: auto;
				transition: all .35s ease-in-out;
				-webkit-transition: all .35s ease-in-out
			}

		<?php endif; ?>
	     <?php endif; ?>

	     /* logo height */
	     <?php if(get_theme_mod('logo_height', 'auto') != 'auto') : ?>
			header#masthead.site-header .site-branding img, header#masthead.site-header .header-image {
				height: <?= get_theme_mod('logo_height', 'auto'); ?>;
				width: auto;
				max-width: 100%;
			}
	     <?php endif; ?>

	     header#masthead.site-header {
		background-color: <?= get_theme_mod('header_bg_color', '#fff'); ?>;
	     }

	     /** Footer **/
	     <?php for($t = 1; $t <= get_theme_mod('footer_rows', '0'); $t++) : ?>
		#footer_<?= $t ?>, #footer_<?= $t ?> h1, #footer_<?= $t ?> h2, #footer_<?= $t ?> h3, #footer_<?= $t ?> a {
			background: <?= get_theme_mod('footer'.$t.'_background', '#fff'); ?>;
			color: <?= get_theme_mod('footer'.$t.'_color', '#fff'); ?> !important;
		}
		#footer_<?= $t ?> ul {
			margin: 0;
		}
		#footer_<?= $t ?> ul li {
			list-style: none;
		}

	     <?php endfor; ?>
	     @media(max-width: 500px) {
		.site-footer .footer-row .footer-inner {
			padding: 15px;
		     }
	     }
         </style>

	<?php
	if(count($google_fonts))  :
		$google_string = '';
		foreach($google_fonts as $custom_font) :
			if($custom_font->{'font-url'}) :
				$google_string .= urlencode($custom_font->{'font-family'}) .  ($custom_font->{'font-variation'} != 'regular'? ':' . $custom_font->{'font-variation'} : '') . '|';
			endif;
		endforeach;
		$google_string = rtrim($google_string, '|');
		?>
		 <link href="https://fonts.googleapis.com/css?family=<?= $google_string; ?>" rel="stylesheet">
	<?php endif; ?>


    <?php
}

//custom mobile logo
add_filter('get_custom_logo', 'wpto_mobile_logo', 10, 2);
function wpto_mobile_logo($html, $blog_id) {
	$custom_logo_id = get_theme_mod( 'responsive_logo' );
	// We have a logo. Logo is go.
	$mobile_html = '';
	if ( $custom_logo_id ) {
		$mobile_html = sprintf( '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>',
			esc_url( home_url( '/' ) ),
			wp_get_attachment_image( $custom_logo_id, 'full', false, array(
				'class'    => 'responsive-logo',
				'itemprop' => 'logo',
			) )
		);
	}
	return $html . $mobile_html;
}

function wpto_font_parser($custom_font_label, &$google_fonts) {
	$custom_font = get_theme_mod($custom_font_label . '_font', 'default');
	if($custom_font != 'default') {
	   $custom_font = json_decode($custom_font);
	   $google_fonts[] = $custom_font;
	   $font_family = $custom_font->{'font-family'};
	   $font_variation = $custom_font->{'font-variation'};
	   if(strpos($font_variation, 'italic') !== false) {
		$font_italic = 'italic';
		$font_weight = str_replace('italic','',$font_variation);
	   } else {
		$font_italic = false;
		$font_weight = $font_variation;
	   }
	} else {
	   $font_family = 'inherit';
	   $font_italic = false;
	   $font_weight = false;
	}

	//check line-height
	$line_height = get_theme_mod($custom_font_label . '_line_height');

	return array(
		'font-family' => $font_family,
		'font-italic' => $font_italic,
		'font-weight' => $font_weight,
		'line-height' => $line_height,
	);
}

if(!function_exists('print_pre')) {
	function print_pre($obj) {
		echo '<pre style="border-radius:10px;background:white;color:black">';
		print_r($obj);
		echo '</pre>';
	}
}

/** UPDATE CHECKER **/
//Initialize the update checker.
require 'theme-updates/theme-update-checker.php';
$example_update_checker = new ThemeUpdateChecker(
    'wptakeoff',
    'https://emdevelopment.nl/admin_tools/wp/wptakeoff/version.json'
);
//set composer updates from local plugin folder
add_action( 'vc_before_init', 'wpto_vcSetAsTheme' );
function wpto_vcSetAsTheme() {
    vc_set_as_theme();
}