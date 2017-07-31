<?php
/**
 * @package bourbon
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

  <div class="entry">

    <header class="entry__header">
      <?php the_title( sprintf( '<h1 class="entry__title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
    </header>

    <div class="entry__meta">
      <div class="entry__author"><?php printf('%s<span class="entry__author--in">in</span>', bourbon_author() )?></div>
      <div class="entry__category"><?php bourbon_entry_category(); ?></div>
      <div class="entry__posted_on right "><?php if ( 'post' == get_post_type() ) bourbon_posted_on(); ?></div>
      <div class="entry__comments right hide-for-small-only"><?php bourbon_entry_comments(); ?></div>
    </div>

    <figure>
          <div class="thumbnail__wrap">
            <div class="flex-video widescreen vimeo">
              <?php bourbon_content_extract_iframe(); ?>
            </div>
          </div>
    </figure>
    
    <div class="entry__content">
    <?php bourbon_content_without_iframe(); ?>
    </div>

    <div class="entry__footer">
      <div class="entry__tags right hide-for-small-only"><?php bourbon_entry_tags(); ?></div>
      <?php bourbon_theme_post_share(); ?>
    </div>

  </div><!-- entry -->

</article>
