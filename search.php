<?php get_header();

global $query_string;

$query_args = explode("&", $query_string);
$search_query = array();

if( strlen($query_string) > 0 ) {
  foreach($query_args as $key => $string) {
    $query_split = explode("=", $string);
    $search_query[$query_split[0]] = urldecode($query_split[1]);
  } // foreach
} //if

$search = new WP_Query($search_query);

?>
<main class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <header class="titlebar">
          <h1><?php echo __('Zoeken', 'coderehab'); ?></h1>
        </header>
      </div>
    </div>
    <div class="row">
      <?php if (isset($search_query['s']) && $search_query['s'] != '' && $search->have_posts()) { ?>
        <div class="col-12">
          <section class="results">
            <?php
            echo '<span class="count">' . $search->found_posts . ' </span>' ;
            echo __('resultaten voor', 'coderehab');
            echo '<span class="query"> "' . $search_query['s'] . '"</span>';
            ?>
          </section>
        </div>

        <?php while ( $search->have_posts() ) : $search->the_post();?>
          <?php get_template_part('partials/content', 'listitem') ?>
        <?php endwhile; ?>
        
        <?php wp_reset_query(); ?>
        <?php wp_reset_postdata(); ?>

        <?php } else { ?>
          <div class="col-12">
            <?php echo __('Er zijn geen resultaten gevonden', 'coderehab'); ?>
          </div>
          <?php } ?>

          <div class="post-pagination col-12">
            <?php $args = array(
              'prev_next'          => true,
              'prev_text'          => __('<span class="prev"></span>'),
              'next_text'          => __('<span class="next"></span>'),
              'type'               => 'plain',
            );
            echo paginate_links($args); ?>
          </div>

        </div>
      </div>
    </main>
    <?php get_footer(); ?>
