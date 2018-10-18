<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package bourbon
 */
?>

	</div><!-- #content -->

  <?php if ( is_active_sidebar( 'footer-1' ) ) { ?>
    <footer id="colophon" class="footer" role="contentinfo">
      <div class="row">
        <div class="columns large-4">
					<?php dynamic_sidebar( 'footer-1' ); ?>
				</div>
        <div class="columns large-4">
					<?php dynamic_sidebar( 'footer-2' ); ?>
				</div>
        <div class="columns large-4">
					<?php dynamic_sidebar( 'footer-3' ); ?>
				</div>
      </div><!-- .row -->
    </footer><!-- #colophon -->
  <?php } ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
