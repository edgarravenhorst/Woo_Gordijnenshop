<?php
$per_page = $count;
$args = array(
  'post_type' => ['product_variation', 'product'],
  'orderby' => 'date',
  'order' => 'DESC',
  'posts_per_page' => $per_page,
  'paged' => get_query_var( 'paged' ),
);

if($category != "Alle categorieen") {
  $args['tax_query'] = array(
    array(
      'taxonomy'  => 'product_cat',
      'field'     => 'slug',
      'terms'     => [$category]
    )
  );
}

$products = new WP_Query($args);

if ( $products->have_posts() ) :
  ?>

  <section class="products">
    <div class="row">
      <?php while ( $products->have_posts() ) : $products->the_post(); ?>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <?php wc_get_template_part( 'content', 'product' ); ?>
        </div>

      <?php endwhile; ?>
    </div>
  </section>

  <?php
  wp_reset_postdata();
endif;
?>
