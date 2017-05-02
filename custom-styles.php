<?php
//load wp
# No need for the template engine
define( 'WP_USE_THEMES', false );
# Load WordPress Core
// Assuming we're in a subdir: "~/wp-content/plugins/current_dir"
require_once( '../../../wp-load.php' );

//prevent direct access
header( "Content-type: text/css; charset: UTF-8" );

//get value from options, settings etc
$google_fonts = array();
$body_font = wpto_font_parser('body', $google_fonts);
$heading_font = wpto_font_parser('heading', $google_fonts);
$menu1_font = wpto_font_parser('menu1_font', $google_fonts);

if(count($google_fonts))  :
    $google_string = '';
    foreach($google_fonts as $custom_font) :
        if($custom_font->{'font-url'}) :
            $google_string .= urlencode($custom_font->{'font-family'}) .  ($custom_font->{'font-variation'} != 'regular'? ':' . $custom_font->{'font-variation'} : '') . '|';
        endif;
    endforeach;
    $google_string = rtrim($google_string, '|');
    ?>
    @import url('https://fonts.googleapis.com/css?family=<?= $google_string; ?>');
<?php endif; ?>


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
body:not(.page-template-full-width) #container {
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
    .admin-bar .site-header-menu.toggled-on {
        top: <?= preg_replace("/[^0-9]/","",get_theme_mod('header_height_responsive', '30px')) + 46; ?>px;
    }


<?php if(get_theme_mod('header_height', false)) : ?>
   @media(min-width: 44.375em) {
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
   }
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