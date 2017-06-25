<form role="search" method="get" class="searchform group" action="<?php echo home_url( '/' ); ?>">
  <label>
    <input type="search" class="search-field"
    placeholder="<?php echo esc_attr_x( 'Zoeken', 'placeholder' ) ?>"
    value="<?php echo get_search_query() ?>" name="s"
    title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" />
    </label>
    <button class="btn" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
  </form>
