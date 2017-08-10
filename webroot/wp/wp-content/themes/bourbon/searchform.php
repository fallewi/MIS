<?php
/**
 * The template for displaying search results pages.
 *
 * @package bourbon Theme
 */
?>
<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ) ?>" >
	<div class="row collapse">
		<div class="small-12 columns">

			<div class="row collapse postfix-round">

			    <div class="small-10 columns">
			      <label class="screen-reader-text" for="s"><?php _e( 'Search for:', 'bourbon' ); ?></label>
			      <input id="searchinput" type="search" value="<?php echo get_search_query() ?>" name="s" id="s" autocomplete="off" />
			    </div>

			    <div class="small-2 columns">
			      <button id="searchsubmit" type="submit" class="button postfix" >
			      </button>
			    </div>

			</div>
		</div>
	</div><!-- row-12 -->
</form>
