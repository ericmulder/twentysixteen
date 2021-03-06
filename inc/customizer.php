<?php
/**
 * Twenty Sixteen Customizer functionality
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

/**
 * Sets up the WordPress core custom header and custom background features.
 *
 * @since Twenty Sixteen 1.0
 *
 * @see twentysixteen_header_style()
 */
function twentysixteen_custom_header_and_background() {
	$color_scheme             = twentysixteen_get_color_scheme();
	$default_background_color = trim( $color_scheme[0], '#' );
	$default_text_color       = trim( $color_scheme[3], '#' );

	/**
	 * Filter the arguments used when adding 'custom-background' support in Twenty Sixteen.
	 *
	 * @since Twenty Sixteen 1.0
	 *
	 * @param array $args {
	 *     An array of custom-background support arguments.
	 *
	 *     @type string $default-color Default color of the background.
	 * }
	 */
	add_theme_support( 'custom-background', apply_filters( 'twentysixteen_custom_background_args', array(
		'default-color' => $default_background_color,
	) ) );

	/**
	 * Filter the arguments used when adding 'custom-header' support in Twenty Sixteen.
	 *
	 * @since Twenty Sixteen 1.0
	 *
	 * @param array $args {
	 *     An array of custom-header support arguments.
	 *
	 *     @type string $default-text-color Default color of the header text.
	 *     @type int      $width            Width in pixels of the custom header image. Default 1200.
	 *     @type int      $height           Height in pixels of the custom header image. Default 280.
	 *     @type bool     $flex-height      Whether to allow flexible-height header images. Default true.
	 *     @type callable $wp-head-callback Callback function used to style the header image and text
	 *                                      displayed on the blog.
	 * }
	 */
	add_theme_support( 'custom-header', apply_filters( 'twentysixteen_custom_header_args', array(
		'default-text-color'     => $default_text_color,
		'width'                  => 1200,
		'height'                 => 280,
		'flex-height'            => true,
		'wp-head-callback'       => 'twentysixteen_header_style',
	) ) );
}
add_action( 'after_setup_theme', 'twentysixteen_custom_header_and_background' );

if ( ! function_exists( 'twentysixteen_header_style' ) ) :
/**
 * Styles the header text displayed on the site.
 *
 * Create your own twentysixteen_header_style() function to override in a child theme.
 *
 * @since Twenty Sixteen 1.0
 *
 * @see twentysixteen_custom_header_and_background().
 */
function twentysixteen_header_style() {
	// If the header text option is untouched, let's bail.
	if ( display_header_text() ) {
		return;
	}

	// If the header text has been hidden.
	?>
	<style type="text/css" id="twentysixteen-header-css">
		.site-branding .site-title,
		.site-description {
			clip: rect(1px, 1px, 1px, 1px);
			position: absolute;
		}
	</style>
	<?php
}
endif; // twentysixteen_header_style

/**
 * Adds postMessage support for site title and description for the Customizer.
 *
 * @since Twenty Sixteen 1.0
 *
 * @param WP_Customize_Manager $wp_customize The Customizer object.
 */
function twentysixteen_customize_register( $wp_customize ) {
	$color_scheme = twentysixteen_get_color_scheme();

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector' => '.site-title a',
			'container_inclusive' => false,
			'render_callback' => 'twentysixteen_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector' => '.site-description',
			'container_inclusive' => false,
			'render_callback' => 'twentysixteen_customize_partial_blogdescription',
		) );
	}

	/** SITE SETUP VARS **/
	/** HEADER SECTION **/
	$wp_customize->add_setting( 'responsive_logo', array(
		'default'           => '',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'responsive_logo',
			array(
				'label'      => __( 'Responsive Logo', 'twentysixteen' ),
				'section'    => 'title_tagline',
				'settings'   => 'responsive_logo',
				'priority'   => 8.5
			)
		)

	);

	$wp_customize->add_setting( 'header_max_width', array(
		'default'           => '100%',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'header_max_width', array(
		'label'       => __( 'Header Max Width', 'twentysixteen' ),
		'section'     => 'title_tagline',
		'priority'   => 102
	) );

	$wp_customize->add_setting( 'body_max_width', array(
		'default'           => '100%',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'body_max_width', array(
		'label'       => __( 'Body Max Width', 'twentysixteen' ),
		'section'     => 'title_tagline',
		'priority'   => 103
	) );

	$wp_customize->add_setting( 'footer_max_width', array(
		'default'           => '100%',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'footer_max_width', array(
		'label'       => __( 'Footer Max Width', 'twentysixteen' ),
		'section'     => 'title_tagline',
		'priority'   => 104
	) );



	// Add color scheme setting and control.
	$wp_customize->add_setting( 'color_scheme', array(
		'default'           => 'default',
		'sanitize_callback' => 'twentysixteen_sanitize_color_scheme',
		'transport'         => 'postMessage',
	) );

	// Remove the core header textcolor control, as it shares the main text color.
	$wp_customize->remove_control( 'header_textcolor' );

	/** COLOR SECTION **/
	$color_section = $wp_customize->get_section('colors');
	//move down fonts property
	$color_section->title = "Fonts & Colors";
	$color_section->priority = 91;

	// Add page background color setting and control.
	$wp_customize->add_setting( 'page_background_color', array(
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'page_background_color', array(
		'label'       => __( 'Content Background Color', 'twentysixteen' ),
		'section'     => 'colors',
	) ) );

	// Add link color setting and control.
	$wp_customize->add_setting( 'link_color', array(
		'default'           => $color_scheme[2],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color', array(
		'label'       => __( 'Link Color', 'twentysixteen' ),
		'section'     => 'colors',
	) ) );

	// Add main text color setting and control.
	$wp_customize->add_setting( 'main_text_color', array(
		'default'           => $color_scheme[3],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'main_text_color', array(
		'label'       => __( 'Main Text Color', 'twentysixteen' ),
		'section'     => 'colors',
	) ) );

	// Add secondary text color setting and control.
	$wp_customize->add_setting( 'secondary_text_color', array(
		'default'           => $color_scheme[4],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'secondary_text_color', array(
		'label'       => __( 'Secondary Text Color', 'twentysixteen' ),
		'section'     => 'colors',
	) ) );

	/** FONTS SECTION **/

	//add font dropdown
	$font_choices = array('default' => 'Default');
	//add system fonts
	$font_value = array(
				'font-family' => 'Trebuchet MS',
				'font-variation' => 'normal',
				'font-url' => false,
			);
	$font_label = 'Trebuchet MS';
	$font_choices[json_encode($font_value)] = $font_label;

	$google_fonts = json_decode(file_get_contents('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyDHfN0BA48kB6I8nfHbY0r9BKft6etHzUY'));
	foreach($google_fonts->items as $font) {
		foreach($font->files as $variant => $file) {
			$font_label = $font->family . ' - ' . $variant;
			$font_value = array(
				'font-family' => $font->family,
				'font-variation' => $variant,
				'font-url' => $file,
			);
			$font_choices[json_encode($font_value)] = $font_label;
		}
	}

	// heading font
	$wp_customize->add_setting( 'heading_font', array(
		'default'           => 'Open Sans',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'heading_font', array(
		'label'       => __( 'Heading Font', 'twentysixteen' ),
		'section'     => 'colors',
		'type' 	      => 'select',
		'choices'     => $font_choices
	) );

	$wp_customize->add_setting( 'heading_size', array(
		'default'           => '16',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'heading_size', array(
		'label'       => __( 'Heading Font (px)', 'twentysixteen' ),
		'section'     => 'colors',
	) );

	$wp_customize->add_setting( 'heading_line_height', array(
		'default'           => 'auto',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'heading_line_height', array(
		'label'       => __( 'Body Line Height', 'twentysixteen' ),
		'section'     => 'colors',
	) );

	$wp_customize->add_setting( 'heading_color', array(
		'default'           => '#000',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'heading_color', array(
		'label'       => __( 'Heading Color', 'twentysixteen' ),
		'section'     => 'colors',
	) ) );

	/* custom fonts per heading */
	$wp_customize->add_setting( 'heading_size1', array(
		'default'           => 'inherit',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'heading_size1', array(
		'label'       => __( 'Heading 1 Font (px)', 'twentysixteen' ),
		'section'     => 'colors',
	) );

	$wp_customize->add_setting( 'heading_size2', array(
		'default'           => 'inherit',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'heading_size2', array(
		'label'       => __( 'Heading 2 Font (px)', 'twentysixteen' ),
		'section'     => 'colors',
	) );

	$wp_customize->add_setting( 'heading_size3', array(
		'default'           => 'inherit',
		'transport'         => 'refresh',

	) );

	$wp_customize->add_control( 'heading_size3', array(
		'label'       => __( 'Heading 3 Font (px)', 'twentysixteen' ),
		'section'     => 'colors',
	) );

	// heading font
	$wp_customize->add_setting( 'body_font', array(
		'default'           => 'Open Sans',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'body_font', array(
		'label'       => __( 'Body Font', 'twentysixteen' ),
		'section'     => 'colors',
		'type' 	      => 'select',
		'choices'     => $font_choices
	) );

	$wp_customize->add_setting( 'body_size', array(
		'default'           => '16',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'body_size', array(
		'label'       => __( 'Body Font (px)', 'twentysixteen' ),
		'section'     => 'colors',
	) );


	$wp_customize->add_setting( 'body_line_height', array(
		'default'           => 'auto',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'body_line_height', array(
		'label'       => __( 'Body Line Height', 'twentysixteen' ),
		'section'     => 'colors',
	) );

	$wp_customize->add_setting( 'body_color', array(
		'default'           => '#000',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'body_color', array(
		'label'       => __( 'Body Color', 'twentysixteen' ),
		'section'     => 'colors',
	) ) );

	$wp_customize->add_setting( 'use_icon_set', array(
		'default'           => false,
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'use_icon_set', array(
		'label'       => __( 'Include iconset (font awesome)', 'twentysixteen' ),
		'section'     => 'colors',
		'type' 	      => 'checkbox',
		'choices'     => $font_choices
	) );
	/** EOF FONTS SECTION **/

	/** HEADER SECTION **/
	$header_section = $wp_customize->get_section('header_image');
	$header_section->title = "Header";

	$wp_customize->add_setting( 'logo_position', array(
		'default'           => 'left',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'logo_position', array(
		'label'       => __( 'Logo Position', 'twentysixteen' ),
		'section'     => 'header_image',
		'type' 	      => 'select',
		'choices'     => array(
			'left' => 'Left',
			'above' => 'Above',
			'center' => 'Center',
			'right' => 'Right',
		)
	) );

	$wp_customize->add_setting( 'logo_height', array(
		'default'           => 'auto',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'logo_height', array(
		'label'       => __( 'Logo Height', 'twentysixteen' ),
		'section'     => 'header_image',
	) );

	$wp_customize->add_setting( 'menu1_position', array(
		'default'           => 'left',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'menu1_position', array(
		'label'       => __( 'Menu 1 Position', 'twentysixteen' ),
		'section'     => 'header_image',
		'type' 	      => 'select',
		'choices'     => array(
			'left' => 'Left',
			'center' => 'Center',
			'right' => 'Right',
		)
	) );

	$wp_customize->add_setting( 'menu1_font', array(
		'default'           => 'Open Sans',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'menu1_font', array(
		'label'       => __( 'Menu 1 Font', 'twentysixteen' ),
		'section'     => 'header_image',
		'type' 	      => 'select',
		'choices'     => $font_choices
	) );

	$wp_customize->add_setting( 'menu1_font_color', array(
		'default'           => '#000',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'menu1_font_color', array(
		'label'       => __( 'Color', 'twentysixteen' ),
		'section'     => 'header_image',
	) ) );

	$wp_customize->add_setting( 'menu1_font_color_active', array(
		'default'           => '#999',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'menu1_font_color_active', array(
		'label'       => __( 'Active Color', 'twentysixteen' ),
		'section'     => 'header_image',
	) ) );

	$wp_customize->add_setting( 'header_height', array(
		'default'           => '30px',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'header_height', array(
		'label'       => __( 'Header Height', 'twentysixteen' ),
		'section'     => 'header_image',
	) );

	$wp_customize->add_setting( 'header_height_responsive', array(
		'default'           => '30px',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'header_height_responsive', array(
		'label'       => __( 'Header Height (mobile)', 'twentysixteen' ),
		'section'     => 'header_image',
	) );

	$wp_customize->add_setting( 'header_bg_color', array(
		'default'           => '#fff',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_bg_color', array(
		'label'       => __( 'Header Background Color', 'twentysixteen' ),
		'section'     => 'header_image',
	) ) );

	$wp_customize->add_setting( 'header_sticky', array(
		'default'           => false,
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'header_sticky', array(
		'label'       => __( 'Sticky Header', 'twentysixteen' ),
		'section'     => 'header_image',
		'type' 	      => 'checkbox',
	) );

	$wp_customize->add_setting( 'header_sticky_height', array(
		'default'           => '30px',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'header_sticky_height', array(
		'label'       => __( 'Sticky Header Height', 'twentysixteen' ),
		'section'     => 'header_image',
	) );



	/** EOF HEADER **/

	/** FOOTER **/
	//add font section
	$wp_customize->add_section( 'footer' , array(
		'title'      => 'Footer',
		'priority'   => 61,
	    ) );

	$wp_customize->add_setting( 'footer_rows', array(
		'default'           => '0',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'footer_rows', array(
		'label'       => __( 'Aantal rows', 'twentysixteen' ),
		'section'     => 'footer',
		'description' => 'Save & refresh browser voor extra opties'
	) );


	for($t = 1; $t <= get_theme_mod('footer_rows', '0'); $t++) {
		$wp_customize->add_setting( 'footer'.$t.'_columns', array(
			'default'           => '1',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control('footer'.$t.'_columns', array(
			'label'       => sprintf(__( 'Footer %s Aantal kolommen', 'twentysixteen' ), $t),
			'section'     => 'footer',
		));

		$wp_customize->add_setting( 'footer'.$t.'_columns_aspect', array(
			'default'           => '',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control('footer'.$t.'_columns_aspect', array(
			'label'       => sprintf(__( 'Footer %s Kolommen Verhouding', 'twentysixteen' ), $t),
			'section'     => 'footer',
			'description' => 't.o.v 100%. Dus bijvoorbeeld 50/50, 33/77 of 33/33/33'
		));

		$wp_customize->add_setting( 'footer'.$t.'_color', array(
			'default'           => '#fff',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control(new WP_Customize_Color_Control( $wp_customize, 'footer'.$t.'_color', array(
			'label'       => __( 'Font Color', 'twentysixteen' ),
			'section'     => 'footer',
		) ) );

		$wp_customize->add_setting( 'footer'.$t.'_background', array(
			'default'           => '',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control(new WP_Customize_Color_Control( $wp_customize, 'footer'.$t.'_background', array(
			'label'       => __( 'Background Color', 'twentysixteen' ),
			'section'     => 'footer',
		) ) );

	}

	/** EOF FOOTER **/


}
add_action( 'customize_register', 'twentysixteen_customize_register', 11 );

/**
 * Render the site title for the selective refresh partial.
 *
 * @since Twenty Sixteen 1.2
 * @see twentysixteen_customize_register()
 *
 * @return void
 */
function twentysixteen_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @since Twenty Sixteen 1.2
 * @see twentysixteen_customize_register()
 *
 * @return void
 */
function twentysixteen_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Registers color schemes for Twenty Sixteen.
 *
 * Can be filtered with {@see 'twentysixteen_color_schemes'}.
 *
 * The order of colors in a colors array:
 * 1. Main Background Color.
 * 2. Page Background Color.
 * 3. Link Color.
 * 4. Main Text Color.
 * 5. Secondary Text Color.
 *
 * @since Twenty Sixteen 1.0
 *
 * @return array An associative array of color scheme options.
 */
function twentysixteen_get_color_schemes() {
	/**
	 * Filter the color schemes registered for use with Twenty Sixteen.
	 *
	 * The default schemes include 'default', 'dark', 'gray', 'red', and 'yellow'.
	 *
	 * @since Twenty Sixteen 1.0
	 *
	 * @param array $schemes {
	 *     Associative array of color schemes data.
	 *
	 *     @type array $slug {
	 *         Associative array of information for setting up the color scheme.
	 *
	 *         @type string $label  Color scheme label.
	 *         @type array  $colors HEX codes for default colors prepended with a hash symbol ('#').
	 *                              Colors are defined in the following order: Main background, page
	 *                              background, link, main text, secondary text.
	 *     }
	 * }
	 */
	return apply_filters( 'twentysixteen_color_schemes', array(
		'default' => array(
			'label'  => __( 'Default', 'twentysixteen' ),
			'colors' => array(
				'#1a1a1a',
				'#ffffff',
				'#007acc',
				'#1a1a1a',
				'#686868',
			),
		),
		'dark' => array(
			'label'  => __( 'Dark', 'twentysixteen' ),
			'colors' => array(
				'#262626',
				'#1a1a1a',
				'#9adffd',
				'#e5e5e5',
				'#c1c1c1',
			),
		),
		'gray' => array(
			'label'  => __( 'Gray', 'twentysixteen' ),
			'colors' => array(
				'#616a73',
				'#4d545c',
				'#c7c7c7',
				'#f2f2f2',
				'#f2f2f2',
			),
		),
		'red' => array(
			'label'  => __( 'Red', 'twentysixteen' ),
			'colors' => array(
				'#ffffff',
				'#ff675f',
				'#640c1f',
				'#402b30',
				'#402b30',
			),
		),
		'yellow' => array(
			'label'  => __( 'Yellow', 'twentysixteen' ),
			'colors' => array(
				'#3b3721',
				'#ffef8e',
				'#774e24',
				'#3b3721',
				'#5b4d3e',
			),
		),
	) );
}

if ( ! function_exists( 'twentysixteen_get_color_scheme' ) ) :
/**
 * Retrieves the current Twenty Sixteen color scheme.
 *
 * Create your own twentysixteen_get_color_scheme() function to override in a child theme.
 *
 * @since Twenty Sixteen 1.0
 *
 * @return array An associative array of either the current or default color scheme HEX values.
 */
function twentysixteen_get_color_scheme() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );
	$color_schemes       = twentysixteen_get_color_schemes();

	if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
		return $color_schemes[ $color_scheme_option ]['colors'];
	}

	return $color_schemes['default']['colors'];
}
endif; // twentysixteen_get_color_scheme

if ( ! function_exists( 'twentysixteen_get_color_scheme_choices' ) ) :
/**
 * Retrieves an array of color scheme choices registered for Twenty Sixteen.
 *
 * Create your own twentysixteen_get_color_scheme_choices() function to override
 * in a child theme.
 *
 * @since Twenty Sixteen 1.0
 *
 * @return array Array of color schemes.
 */
function twentysixteen_get_color_scheme_choices() {
	$color_schemes                = twentysixteen_get_color_schemes();
	$color_scheme_control_options = array();

	foreach ( $color_schemes as $color_scheme => $value ) {
		$color_scheme_control_options[ $color_scheme ] = $value['label'];
	}

	return $color_scheme_control_options;
}
endif; // twentysixteen_get_color_scheme_choices


if ( ! function_exists( 'twentysixteen_sanitize_color_scheme' ) ) :
/**
 * Handles sanitization for Twenty Sixteen color schemes.
 *
 * Create your own twentysixteen_sanitize_color_scheme() function to override
 * in a child theme.
 *
 * @since Twenty Sixteen 1.0
 *
 * @param string $value Color scheme name value.
 * @return string Color scheme name.
 */
function twentysixteen_sanitize_color_scheme( $value ) {
	$color_schemes = twentysixteen_get_color_scheme_choices();

	if ( ! array_key_exists( $value, $color_schemes ) ) {
		return 'default';
	}

	return $value;
}
endif; // twentysixteen_sanitize_color_scheme

/**
 * Enqueues front-end CSS for color scheme.
 *
 * @since Twenty Sixteen 1.0
 *
 * @see wp_add_inline_style()
 */
function twentysixteen_color_scheme_css() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );

	// Don't do anything if the default color scheme is selected.
	if ( 'default' === $color_scheme_option ) {
		return;
	}

	$color_scheme = twentysixteen_get_color_scheme();

	// Convert main text hex color to rgba.
	$color_textcolor_rgb = twentysixteen_hex2rgb( $color_scheme[3] );

	// If the rgba values are empty return early.
	if ( empty( $color_textcolor_rgb ) ) {
		return;
	}

	// If we get this far, we have a custom color scheme.
	$colors = array(
		'background_color'      => $color_scheme[0],
		'page_background_color' => $color_scheme[1],
		'link_color'            => $color_scheme[2],
		'main_text_color'       => $color_scheme[3],
		'secondary_text_color'  => $color_scheme[4],
		'border_color'          => vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.2)', $color_textcolor_rgb ),

	);

	$color_scheme_css = twentysixteen_get_color_scheme_css( $colors );

	wp_add_inline_style( 'twentysixteen-style', $color_scheme_css );
}
add_action( 'wp_enqueue_scripts', 'twentysixteen_color_scheme_css' );

/**
 * Binds the JS listener to make Customizer color_scheme control.
 *
 * Passes color scheme data as colorScheme global.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_customize_control_js() {
	wp_enqueue_script( 'color-scheme-control', get_template_directory_uri() . '/js/color-scheme-control.js', array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), '20160816', true );
	wp_localize_script( 'color-scheme-control', 'colorScheme', twentysixteen_get_color_schemes() );
}
add_action( 'customize_controls_enqueue_scripts', 'twentysixteen_customize_control_js' );

/**
 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_customize_preview_js() {
	wp_enqueue_script( 'twentysixteen-customize-preview', get_template_directory_uri() . '/js/customize-preview.js', array( 'customize-preview' ), '20160816', true );
}
add_action( 'customize_preview_init', 'twentysixteen_customize_preview_js' );

/**
 * Returns CSS for the color schemes.
 *
 * @since Twenty Sixteen 1.0
 *
 * @param array $colors Color scheme colors.
 * @return string Color scheme CSS.
 */
function twentysixteen_get_color_scheme_css( $colors ) {
	$colors = wp_parse_args( $colors, array(
		'background_color'      => '',
		'page_background_color' => '',
		'link_color'            => '',
		'main_text_color'       => '',
		'secondary_text_color'  => '',
		'border_color'          => '',
	) );

	return <<<CSS
	/* Color Scheme */

	/* Background Color */
	body {
		background-color: {$colors['background_color']};
	}

	/* Page Background Color */
	.site {
		background-color: {$colors['page_background_color']};
	}

	mark,
	ins,
	button,
	button[disabled]:hover,
	button[disabled]:focus,
	input[type="button"],
	input[type="button"][disabled]:hover,
	input[type="button"][disabled]:focus,
	input[type="reset"],
	input[type="reset"][disabled]:hover,
	input[type="reset"][disabled]:focus,
	input[type="submit"],
	input[type="submit"][disabled]:hover,
	input[type="submit"][disabled]:focus,
	.menu-toggle.toggled-on,
	.menu-toggle.toggled-on:hover,
	.menu-toggle.toggled-on:focus,
	.pagination .prev,
	.pagination .next,
	.pagination .prev:hover,
	.pagination .prev:focus,
	.pagination .next:hover,
	.pagination .next:focus,
	.pagination .nav-links:before,
	.pagination .nav-links:after,
	.widget_calendar tbody a,
	.widget_calendar tbody a:hover,
	.widget_calendar tbody a:focus,
	.page-links a,
	.page-links a:hover,
	.page-links a:focus {
		color: {$colors['page_background_color']};
	}

	/* Link Color */
	.menu-toggle:hover,
	.menu-toggle:focus,
	a,
	.main-navigation a:hover,
	.main-navigation a:focus,
	.dropdown-toggle:hover,
	.dropdown-toggle:focus,
	.social-navigation a:hover:before,
	.social-navigation a:focus:before,
	.post-navigation a:hover .post-title,
	.post-navigation a:focus .post-title,
	.tagcloud a:hover,
	.tagcloud a:focus,
	.site-branding .site-title a:hover,
	.site-branding .site-title a:focus,
	.entry-title a:hover,
	.entry-title a:focus,
	.entry-footer a:hover,
	.entry-footer a:focus,
	.comment-metadata a:hover,
	.comment-metadata a:focus,
	.pingback .comment-edit-link:hover,
	.pingback .comment-edit-link:focus,
	.comment-reply-link,
	.comment-reply-link:hover,
	.comment-reply-link:focus,
	.required,
	.site-info a:hover,
	.site-info a:focus {
		color: {$colors['link_color']};
	}

	mark,
	ins,
	button:hover,
	button:focus,
	input[type="button"]:hover,
	input[type="button"]:focus,
	input[type="reset"]:hover,
	input[type="reset"]:focus,
	input[type="submit"]:hover,
	input[type="submit"]:focus,
	.pagination .prev:hover,
	.pagination .prev:focus,
	.pagination .next:hover,
	.pagination .next:focus,
	.widget_calendar tbody a,
	.page-links a:hover,
	.page-links a:focus {
		background-color: {$colors['link_color']};
	}

	input[type="date"]:focus,
	input[type="time"]:focus,
	input[type="datetime-local"]:focus,
	input[type="week"]:focus,
	input[type="month"]:focus,
	input[type="text"]:focus,
	input[type="email"]:focus,
	input[type="url"]:focus,
	input[type="password"]:focus,
	input[type="search"]:focus,
	input[type="tel"]:focus,
	input[type="number"]:focus,
	textarea:focus,
	.tagcloud a:hover,
	.tagcloud a:focus,
	.menu-toggle:hover,
	.menu-toggle:focus {
		border-color: {$colors['link_color']};
	}

	/* Main Text Color */
	body,
	blockquote cite,
	blockquote small,
	.main-navigation a,
	.menu-toggle,
	.dropdown-toggle,
	.social-navigation a,
	.post-navigation a,
	.pagination a:hover,
	.pagination a:focus,
	.widget-title a,
	.site-branding .site-title a,
	.entry-title a,
	.page-links > .page-links-title,
	.comment-author,
	.comment-reply-title small a:hover,
	.comment-reply-title small a:focus {
		color: {$colors['main_text_color']};
	}

	blockquote,
	.menu-toggle.toggled-on,
	.menu-toggle.toggled-on:hover,
	.menu-toggle.toggled-on:focus,
	.post-navigation,
	.post-navigation div + div,
	.pagination,
	.widget,
	.page-header,
	.page-links a,
	.comments-title,
	.comment-reply-title {
		border-color: {$colors['main_text_color']};
	}

	button,
	button[disabled]:hover,
	button[disabled]:focus,
	input[type="button"],
	input[type="button"][disabled]:hover,
	input[type="button"][disabled]:focus,
	input[type="reset"],
	input[type="reset"][disabled]:hover,
	input[type="reset"][disabled]:focus,
	input[type="submit"],
	input[type="submit"][disabled]:hover,
	input[type="submit"][disabled]:focus,
	.menu-toggle.toggled-on,
	.menu-toggle.toggled-on:hover,
	.menu-toggle.toggled-on:focus,
	.pagination:before,
	.pagination:after,
	.pagination .prev,
	.pagination .next,
	.page-links a {
		background-color: {$colors['main_text_color']};
	}

	/* Secondary Text Color */

	/**
	 * IE8 and earlier will drop any block with CSS3 selectors.
	 * Do not combine these styles with the next block.
	 */
	body:not(.search-results) .entry-summary {
		color: {$colors['secondary_text_color']};
	}

	blockquote,
	.post-password-form label,
	a:hover,
	a:focus,
	a:active,
	.post-navigation .meta-nav,
	.image-navigation,
	.comment-navigation,
	.widget_recent_entries .post-date,
	.widget_rss .rss-date,
	.widget_rss cite,
	.site-description,
	.author-bio,
	.entry-footer,
	.entry-footer a,
	.sticky-post,
	.taxonomy-description,
	.entry-caption,
	.comment-metadata,
	.pingback .edit-link,
	.comment-metadata a,
	.pingback .comment-edit-link,
	.comment-form label,
	.comment-notes,
	.comment-awaiting-moderation,
	.logged-in-as,
	.form-allowed-tags,
	.site-info,
	.site-info a,
	.wp-caption .wp-caption-text,
	.gallery-caption,
	.widecolumn label,
	.widecolumn .mu_register label {
		color: {$colors['secondary_text_color']};
	}

	.widget_calendar tbody a:hover,
	.widget_calendar tbody a:focus {
		background-color: {$colors['secondary_text_color']};
	}

	/* Border Color */
	fieldset,
	pre,
	abbr,
	acronym,
	table,
	th,
	td,
	input[type="date"],
	input[type="time"],
	input[type="datetime-local"],
	input[type="week"],
	input[type="month"],
	input[type="text"],
	input[type="email"],
	input[type="url"],
	input[type="password"],
	input[type="search"],
	input[type="tel"],
	input[type="number"],
	textarea,
	.main-navigation li,
	.main-navigation .primary-menu,
	.menu-toggle,
	.dropdown-toggle:after,
	.social-navigation a,
	.image-navigation,
	.comment-navigation,
	.tagcloud a,
	.entry-content,
	.entry-summary,
	.page-links a,
	.page-links > span,
	.comment-list article,
	.comment-list .pingback,
	.comment-list .trackback,
	.comment-reply-link,
	.no-comments,
	.widecolumn .mu_register .mu_alert {
		border-color: {$colors['main_text_color']}; /* Fallback for IE7 and IE8 */
		border-color: {$colors['border_color']};
	}

	hr,
	code {
		background-color: {$colors['main_text_color']}; /* Fallback for IE7 and IE8 */
		background-color: {$colors['border_color']};
	}

	@media screen and (min-width: 56.875em) {
		.main-navigation li:hover > a,
		.main-navigation li.focus > a {
			color: {$colors['link_color']};
		}

		.main-navigation ul ul,
		.main-navigation ul ul li {
			border-color: {$colors['border_color']};
		}

		.main-navigation ul ul:before {
			border-top-color: {$colors['border_color']};
			border-bottom-color: {$colors['border_color']};
		}

		.main-navigation ul ul li {
			background-color: {$colors['page_background_color']};
		}

		.main-navigation ul ul:after {
			border-top-color: {$colors['page_background_color']};
			border-bottom-color: {$colors['page_background_color']};
		}
	}

CSS;
}


/**
 * Outputs an Underscore template for generating CSS for the color scheme.
 *
 * The template generates the css dynamically for instant display in the
 * Customizer preview.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_color_scheme_css_template() {
	$colors = array(
		'background_color'      => '{{ data.background_color }}',
		'page_background_color' => '{{ data.page_background_color }}',
		'link_color'            => '{{ data.link_color }}',
		'main_text_color'       => '{{ data.main_text_color }}',
		'secondary_text_color'  => '{{ data.secondary_text_color }}',
		'border_color'          => '{{ data.border_color }}',
	);
	?>
	<script type="text/html" id="tmpl-twentysixteen-color-scheme">
		<?php echo twentysixteen_get_color_scheme_css( $colors ); ?>
	</script>
	<?php
}
add_action( 'customize_controls_print_footer_scripts', 'twentysixteen_color_scheme_css_template' );

/**
 * Enqueues front-end CSS for the page background color.
 *
 * @since Twenty Sixteen 1.0
 *
 * @see wp_add_inline_style()
 */
function twentysixteen_page_background_color_css() {
	$color_scheme          = twentysixteen_get_color_scheme();
	$default_color         = $color_scheme[1];
	$page_background_color = get_theme_mod( 'page_background_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $page_background_color === $default_color ) {
		return;
	}

	$css = '
		/* Custom Page Background Color */
		.site {
			background-color: %1$s;
		}

		mark,
		ins,
		button,
		button[disabled]:hover,
		button[disabled]:focus,
		input[type="button"],
		input[type="button"][disabled]:hover,
		input[type="button"][disabled]:focus,
		input[type="reset"],
		input[type="reset"][disabled]:hover,
		input[type="reset"][disabled]:focus,
		input[type="submit"],
		input[type="submit"][disabled]:hover,
		input[type="submit"][disabled]:focus,
		.menu-toggle.toggled-on,
		.menu-toggle.toggled-on:hover,
		.menu-toggle.toggled-on:focus,
		.pagination .prev,
		.pagination .next,
		.pagination .prev:hover,
		.pagination .prev:focus,
		.pagination .next:hover,
		.pagination .next:focus,
		.pagination .nav-links:before,
		.pagination .nav-links:after,
		.widget_calendar tbody a,
		.widget_calendar tbody a:hover,
		.widget_calendar tbody a:focus,
		.page-links a,
		.page-links a:hover,
		.page-links a:focus {
			color: %1$s;
		}

		@media screen and (min-width: 56.875em) {
			.main-navigation ul ul li {
				background-color: %1$s;
			}

			.main-navigation ul ul:after {
				border-top-color: %1$s;
				border-bottom-color: %1$s;
			}
		}
	';

	wp_add_inline_style( 'twentysixteen-style', sprintf( $css, $page_background_color ) );
}
add_action( 'wp_enqueue_scripts', 'twentysixteen_page_background_color_css', 11 );

/**
 * Enqueues front-end CSS for the link color.
 *
 * @since Twenty Sixteen 1.0
 *
 * @see wp_add_inline_style()
 */
function twentysixteen_link_color_css() {
	$color_scheme    = twentysixteen_get_color_scheme();
	$default_color   = $color_scheme[2];
	$link_color = get_theme_mod( 'link_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $link_color === $default_color ) {
		return;
	}

	$css = '
		/* Custom Link Color */
		.menu-toggle:hover,
		.menu-toggle:focus,
		a,
		.main-navigation a:hover,
		.main-navigation a:focus,
		.dropdown-toggle:hover,
		.dropdown-toggle:focus,
		.social-navigation a:hover:before,
		.social-navigation a:focus:before,
		.post-navigation a:hover .post-title,
		.post-navigation a:focus .post-title,
		.tagcloud a:hover,
		.tagcloud a:focus,
		.site-branding .site-title a:hover,
		.site-branding .site-title a:focus,
		.entry-title a:hover,
		.entry-title a:focus,
		.entry-footer a:hover,
		.entry-footer a:focus,
		.comment-metadata a:hover,
		.comment-metadata a:focus,
		.pingback .comment-edit-link:hover,
		.pingback .comment-edit-link:focus,
		.comment-reply-link,
		.comment-reply-link:hover,
		.comment-reply-link:focus,
		.required,
		.site-info a:hover,
		.site-info a:focus {
			color: %1$s;
		}

		mark,
		ins,
		button:hover,
		button:focus,
		input[type="button"]:hover,
		input[type="button"]:focus,
		input[type="reset"]:hover,
		input[type="reset"]:focus,
		input[type="submit"]:hover,
		input[type="submit"]:focus,
		.pagination .prev:hover,
		.pagination .prev:focus,
		.pagination .next:hover,
		.pagination .next:focus,
		.widget_calendar tbody a,
		.page-links a:hover,
		.page-links a:focus {
			background-color: %1$s;
		}

		input[type="date"]:focus,
		input[type="time"]:focus,
		input[type="datetime-local"]:focus,
		input[type="week"]:focus,
		input[type="month"]:focus,
		input[type="text"]:focus,
		input[type="email"]:focus,
		input[type="url"]:focus,
		input[type="password"]:focus,
		input[type="search"]:focus,
		input[type="tel"]:focus,
		input[type="number"]:focus,
		textarea:focus,
		.tagcloud a:hover,
		.tagcloud a:focus,
		.menu-toggle:hover,
		.menu-toggle:focus {
			border-color: %1$s;
		}

		@media screen and (min-width: 56.875em) {
			.main-navigation li:hover > a,
			.main-navigation li.focus > a {
				color: %1$s;
			}
		}
	';

	wp_add_inline_style( 'twentysixteen-style', sprintf( $css, $link_color ) );
}
add_action( 'wp_enqueue_scripts', 'twentysixteen_link_color_css', 11 );

/**
 * Enqueues front-end CSS for the main text color.
 *
 * @since Twenty Sixteen 1.0
 *
 * @see wp_add_inline_style()
 */
function twentysixteen_main_text_color_css() {
	$color_scheme    = twentysixteen_get_color_scheme();
	$default_color   = $color_scheme[3];
	$main_text_color = get_theme_mod( 'main_text_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $main_text_color === $default_color ) {
		return;
	}

	// Convert main text hex color to rgba.
	$main_text_color_rgb = twentysixteen_hex2rgb( $main_text_color );

	// If the rgba values are empty return early.
	if ( empty( $main_text_color_rgb ) ) {
		return;
	}

	// If we get this far, we have a custom color scheme.
	$border_color = vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.2)', $main_text_color_rgb );

	$css = '
		/* Custom Main Text Color */
		body,
		blockquote cite,
		blockquote small,
		.main-navigation a,
		.menu-toggle,
		.dropdown-toggle,
		.social-navigation a,
		.post-navigation a,
		.pagination a:hover,
		.pagination a:focus,
		.widget-title a,
		.site-branding .site-title a,
		.entry-title a,
		.page-links > .page-links-title,
		.comment-author,
		.comment-reply-title small a:hover,
		.comment-reply-title small a:focus {
			color: %1$s
		}

		blockquote,
		.menu-toggle.toggled-on,
		.menu-toggle.toggled-on:hover,
		.menu-toggle.toggled-on:focus,
		.post-navigation,
		.post-navigation div + div,
		.pagination,
		.widget,
		.page-header,
		.page-links a,
		.comments-title,
		.comment-reply-title {
			border-color: %1$s;
		}

		button,
		button[disabled]:hover,
		button[disabled]:focus,
		input[type="button"],
		input[type="button"][disabled]:hover,
		input[type="button"][disabled]:focus,
		input[type="reset"],
		input[type="reset"][disabled]:hover,
		input[type="reset"][disabled]:focus,
		input[type="submit"],
		input[type="submit"][disabled]:hover,
		input[type="submit"][disabled]:focus,
		.menu-toggle.toggled-on,
		.menu-toggle.toggled-on:hover,
		.menu-toggle.toggled-on:focus,
		.pagination:before,
		.pagination:after,
		.pagination .prev,
		.pagination .next,
		.page-links a {
			background-color: %1$s;
		}

		/* Border Color */
		fieldset,
		pre,
		abbr,
		acronym,
		table,
		th,
		td,
		input[type="date"],
		input[type="time"],
		input[type="datetime-local"],
		input[type="week"],
		input[type="month"],
		input[type="text"],
		input[type="email"],
		input[type="url"],
		input[type="password"],
		input[type="search"],
		input[type="tel"],
		input[type="number"],
		textarea,
		.main-navigation li,
		.main-navigation .primary-menu,
		.menu-toggle,
		.dropdown-toggle:after,
		.social-navigation a,
		.image-navigation,
		.comment-navigation,
		.tagcloud a,
		.entry-content,
		.entry-summary,
		.page-links a,
		.page-links > span,
		.comment-list article,
		.comment-list .pingback,
		.comment-list .trackback,
		.comment-reply-link,
		.no-comments,
		.widecolumn .mu_register .mu_alert {
			border-color: %1$s; /* Fallback for IE7 and IE8 */
			border-color: %2$s;
		}

		hr,
		code {
			background-color: %1$s; /* Fallback for IE7 and IE8 */
			background-color: %2$s;
		}

		@media screen and (min-width: 56.875em) {
			.main-navigation ul ul,
			.main-navigation ul ul li {
				border-color: %2$s;
			}

			.main-navigation ul ul:before {
				border-top-color: %2$s;
				border-bottom-color: %2$s;
			}
		}
	';

	wp_add_inline_style( 'twentysixteen-style', sprintf( $css, $main_text_color, $border_color ) );
}
add_action( 'wp_enqueue_scripts', 'twentysixteen_main_text_color_css', 11 );

/**
 * Enqueues front-end CSS for the secondary text color.
 *
 * @since Twenty Sixteen 1.0
 *
 * @see wp_add_inline_style()
 */
function twentysixteen_secondary_text_color_css() {
	$color_scheme    = twentysixteen_get_color_scheme();
	$default_color   = $color_scheme[4];
	$secondary_text_color = get_theme_mod( 'secondary_text_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $secondary_text_color === $default_color ) {
		return;
	}

	$css = '
		/* Custom Secondary Text Color */

		/**
		 * IE8 and earlier will drop any block with CSS3 selectors.
		 * Do not combine these styles with the next block.
		 */
		body:not(.search-results) .entry-summary {
			color: %1$s;
		}

		blockquote,
		.post-password-form label,
		a:hover,
		a:focus,
		a:active,
		.post-navigation .meta-nav,
		.image-navigation,
		.comment-navigation,
		.widget_recent_entries .post-date,
		.widget_rss .rss-date,
		.widget_rss cite,
		.site-description,
		.author-bio,
		.entry-footer,
		.entry-footer a,
		.sticky-post,
		.taxonomy-description,
		.entry-caption,
		.comment-metadata,
		.pingback .edit-link,
		.comment-metadata a,
		.pingback .comment-edit-link,
		.comment-form label,
		.comment-notes,
		.comment-awaiting-moderation,
		.logged-in-as,
		.form-allowed-tags,
		.site-info,
		.site-info a,
		.wp-caption .wp-caption-text,
		.gallery-caption,
		.widecolumn label,
		.widecolumn .mu_register label {
			color: %1$s;
		}

		.widget_calendar tbody a:hover,
		.widget_calendar tbody a:focus {
			background-color: %1$s;
		}
	';

	wp_add_inline_style( 'twentysixteen-style', sprintf( $css, $secondary_text_color ) );
}
add_action( 'wp_enqueue_scripts', 'twentysixteen_secondary_text_color_css', 11 );
