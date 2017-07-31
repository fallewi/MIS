<?php
/**
 * Part: Hero Section
 */
?>

<?php if ( is_category() ) { ?>

  <?php $cat_id = get_query_var( 'cat' ); ?>

  <div class="category__hero" <?php echo bourbon_get_mask_color( $cat_id ) ? 'style="background-color:'.bourbon_get_mask_color( $cat_id ).'"' : ''; ?>>
    <div class="row ">
      <div class="columns large-12 large-centered">
        <figure>
          <?php if ( bourbon_get_category_image( $cat_id ) ) { ?>
          <div class="category__image">
            <img src="<?php echo bourbon_get_category_image( $cat_id ); ?>" alt="" />
          </div>
          <?php } ?>
        </figure>
        <header class="category__header <?php echo bourbon_get_hero_text_color( $cat_id ); ?>" <?php echo bourbon_get_category_color( $cat_id ) ? 'style="color:'.bourbon_get_category_color( $cat_id ).'"' : ''; ?>> <!-- Add inverse class -->
          <?php echo bourbon_get_the_archive_title(); ?>
          <?php echo balanceTags(bourbon_the_archive_description( '<div class="category__description">', '</div>' )); ?>
        </header><!-- .page-header -->

        <?php global $burocrate_bourbon; ?>

      </div>
    </div><!-- row -->
  </div><!-- hero -->

<?php } elseif ( is_home() ) { ?>

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

<?php } elseif ( is_archive() AND ! is_category() ) { ?>

  <!-- Archive Hero (not Category) -->
  <div class="archive__hero">
    <div class="row">
      <div class="columns large-12">
        <header class="archive__header">
            <?php echo bourbon_get_the_archive_title();?>
        </header><!-- .page-header -->
      </div>
    </div>
  </div>

<?php } elseif ( is_search() ) { ?>

  <div class="search__hero">
    <div class="row">
      <div class="columns large-12">
        <header class="search__header">
            <?php printf( __( '<h2 class="archive__name">Search Results</h2><h1 class="search__title">%s</h1>', 'bourbon' ), get_search_query() ); ?>
        </header><!-- .page-header -->
        <div class="row">
          <div class="columns large-6 large-centered"><?php get_search_form(); ?></div>
        </div>
      </div>
    </div>
  </div>

<?php } elseif ( is_404() ) { ?>

  <div class="search__hero">
    <div class="row">
      <div class="columns large-12">
        <header class="search__header">
            <h1 class="search__title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'bourbon' ); ?></h1>
        </header><!-- .page-header -->
        <div class="row">
          <div class="columns large-6 large-centered"><?php get_search_form(); ?></div>
        </div>
      </div>
    </div>
  </div>

<?php } ?>
