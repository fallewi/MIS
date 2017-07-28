<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package bourbon
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<?php global $burocrate_bourbon; ?>
<div class="columns large-4 show-for-large-up">
<div id="secondary" class="widget-area <?php echo ( $burocrate_bourbon['sidebar-text-inverse'] ) ? 'inverse-widget' : '' ; ?>" role="complementary">
  <div class="row collapse">
    <?php dynamic_sidebar( 'sidebar-1' ); ?>
  </div>
</div>
</div>
