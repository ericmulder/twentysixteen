<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

		</div><!-- .site-content -->

		<footer id="colophon" class="site-footer" role="contentinfo">
			<?php
			//footer widgets
			for($t = 1; $t <= get_theme_mod('footer_rows', '0'); $t++) : ?>
			<div class="footer-row" id="footer_<?= $t ?>">
				<div class="footer-inner" style="max-width:<?= apply_filters('footer_'.$t.'_width', get_theme_mod('footer_max_width', '100%')); ?>">
					<?php
					$numCols = get_theme_mod('footer'.$t.'_columns', '1');
					for($x = 1; $x <=  $numCols; $x++) : ?>
						<div class="footer-column" id="footer_<?= $t ?>_<?= $x ?>" style="float:left;width:<?= 100/$numCols ?>%;">
							<?php do_action('footer_' . $t . '_' . $x); ?>
							<?php dynamic_sidebar( 'footer_' . $t . '_' . $x ); ?>
						</div>
					<?php endfor; ?>
				</div>
				<div class="cleardit" />
			</div>
			<?php endfor; ?>



		</footer><!-- .site-footer -->
	</div><!-- .site-inner -->
</div><!-- .site -->

<?php wp_footer(); ?>
</body>
</html>
