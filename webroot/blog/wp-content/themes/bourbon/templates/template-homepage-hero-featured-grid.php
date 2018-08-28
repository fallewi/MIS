<?php
/**
* Template Name: Homepage Hero & Small Slider & Grid
*/
?>
<?php get_header(); ?>

<?php
global $burocrate_bourbon;
$featured_items = ( $burocrate_bourbon['featured-items'] ) ? $burocrate_bourbon['featured-items'] : '10';
$featured_columns = ( $burocrate_bourbon['featured-columns'] ) ? $burocrate_bourbon['featured-columns'] : '4';
$featured_divider = $burocrate_bourbon['featured-divider'] ? 'featured__divider' : '';
$featured_fullwidth = $burocrate_bourbon['featured-fullwidth'];
$featured_autoplay = $burocrate_bourbon['featured-autoplay'];
$cat_id = get_query_var( 'cat' );

?>

<div class="section__featured">

  <?php echo esc_attr( $featured_fullwidth ) ? '<div class="featured__fullwidth">' : '<div class="row"><div class="columns large-12">'; ?>

    <div id="slick-featured" data-slick='{"slidesToShow": <?php echo esc_attr( $featured_columns ); ?>, "slidesToScroll": 4, "autoplay": <?php echo ( $featured_autoplay ) ? 'true' : 'false'; ?>}' data-equalizer="featured">

      <?php $wp_query = new WP_Query( array(
        'cat' => $cat_id,
        'posts_per_page' => esc_attr( $featured_items ),
        'tax_query' => array(
          array(
            'taxonomy' => 'post_format',
            'field'    => 'slug',
            'terms'    => array( 'post-format-quote', 'post-format-image' ),
            'operator' => 'NOT IN',
          ),
        ),
      ));
      while( $wp_query->have_posts() ){ $wp_query->the_post(); ?>

        <div class="featured <?php echo esc_attr( $featured_divider ); ?>">

          <!-- Image -->
          <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
            <div class="featured__image" style=" background-image: url(<?php if ( has_post_thumbnail() ) get_thumbnail_url_only( 'bourbon_thumbnail_medium' ); ?>);">
            </div>
          </a>

          <div class="featured__wrap" data-equalizer-watch="featured">
            <!-- <div class="featured__category"><?php bourbon_entry_category(1); ?></div> -->
            <?php the_title( sprintf( '<h5 class="featured__title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h5>' ); ?>
          </div>
          <div class="featured__footer">
            <div class="featured__posted_on"><?php bourbon_posted_on( false ); ?></div>
            <div class="featured__comments"><?php bourbon_entry_comments(); ?></div>
          </div>
        </div>


        <?php }; wp_reset_postdata(); ?>

      </div>

      <?php echo esc_attr( $featured_fullwidth ) ? '</div>' : '</div></div>'; ?><!-- /columns --><!-- /row -->

</div><!-- section -->



    <?php global $burocrate_bourbon; ?>

    <div class="section__hero" style="background-image: url(<?php echo esc_url( $burocrate_bourbon['hero-background-image']['url'] ); ?>);">

      <div class="hero__fill" style="background-color: <?php echo esc_attr( $burocrate_bourbon['hero-background-color']['rgba'] ); ?>;"></div>

      <div class="row">
        <div class="columns large-12">

          <header class="page__header <?php echo esc_attr( $burocrate_bourbon['hero-text-inverse'] )  ? 'inverse' : ''; ?>" >

            <!-- Hero logo -->
            <?php if ( $burocrate_bourbon['hero-logo']['url'] ): ?>
              <div class="hero__logo"><img src="<?php echo esc_url( $burocrate_bourbon['hero-logo']['url'] ); ?>"></div>
            <?php endif ?>

            <!-- Hero title -->
            <?php if ( $burocrate_bourbon['hero-title'] ) : ?>
              <h1 class="page__title"><?php echo esc_attr( $burocrate_bourbon['hero-title'] ); ?></h1>
            <?php endif ?>

            <div class="page__description"><?php echo wp_kses( $burocrate_bourbon['hero-description'], 'default'); ?>
              <br><a href="<?php echo esc_url( $burocrate_bourbon['hero-extralink-url'] ); ?>" class="extralink"><?php echo esc_attr( $burocrate_bourbon['hero-extralink-title'] ); ?></a>
            </div>
          </header><!-- .page-header -->

        </div> <!-- columns -->
      </div> <!-- row -->
    </div>

    <!-- 3 Grid Masonry -->
    <div id="primary" class="content-area">
      <main id="main" class="site-main" role="main">

        <?php  if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
        elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
        else { $paged = 1; }
        $wp_query = new WP_Query( array(
          'post_type' => 'post',
          'posts_per_page' => 6,
          'paged' => $paged,
        )); ?>

        <?php if ( $wp_query->have_posts() ) : ?>

          <div class="row  masonry-container grid__template--3">
            <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

              <div class="columns large-4 medium-6 masonry-item">
                <?php if ( get_post_format() == 'quote' ): ?>
                  <?php get_template_part( 'content', 'quote' ); ?>
                <?php elseif ( get_post_format() == 'image' ): ?>
                  <?php get_template_part( 'content', 'image' ); ?>
                <?php else: ?>
                  <?php get_template_part( 'content', 'grid' ); ?>
                <?php endif; ?>
              </div><!-- columns -->

            <?php endwhile; ?>
          </div><!-- row -->

          <div class="row">
            <nav class="columns large-12 navigation post-navigation">

              <?php global $burocrate_bourbon;
              if ( $burocrate_bourbon['pagination'] == '1' AND !wp_is_mobile() ) {
                the_posts_pagination();
              } else {
                bourbon_posts_navigation();
              } ?>

            </nav>
          </div>

        <?php else : ?>

          <div class="row">
            <div class="columns large-9 large-centered">
              <?php get_template_part( 'content', 'none' ); ?>
            </div>
          </div>

        <?php endif; ?>

      </main><!-- #main -->
    </div><!-- #primary -->

    <!-- Infinite scroll + Masonry function -->
    <?php echo "<script type='text/javascript'>
			jQuery(function(){
			      var container = jQuery('.masonry-container');
			      container.imagesLoaded(function(){
			        container.masonry({
			          itemSelector: '.masonry-item',
			        });
			    });
			    container.infinitescroll({
			    	loading: {
							img: '" . esc_url( get_template_directory_uri() . '/img/preloader.gif' ) . "',
				      msgText: '',
				      finishedMsg  : '<p>" . __( 'All loaded', 'bourbon' ) . "</p>',
			    	},
			      navSelector  : '.navigation',
			      nextSelector : '.navigation a',
			      itemSelector : '.masonry-item',
			      debug: false,
			      errorCallback: function() {
			        jQuery('#infscr-loading').animate({opacity: .5},2000).fadeOut('normal');
			      }
			      },
			      function( newElements ) {
			        var newElems = jQuery( newElements );
			        newElems.imagesLoaded(function(){
			          container.masonry( 'appended', newElems, true );
			        });
			      }
			    );

			  });
			</script>"; ?>



    <?php get_footer(); ?>
