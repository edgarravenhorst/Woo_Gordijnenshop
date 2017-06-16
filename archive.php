<?php get_header(); ?>
<main class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <header class="titlebar">
          <!-- <h1><?php echo get_the_title(); ?></h1> -->
        </header>
      </div>
    </div>
    <div class="row">
      <article id="page_content">
        <?php
        while ( have_posts() ) : the_post();
        get_template_part('partials/content', 'listitem');
      endwhile;
      ?>
    </article>
  </div>
  <section class="post-pagination">
    <div class="col-12">
      <?php $args = array(
        'prev_next'          => true,
        'prev_text'          => __('<span class="prev"></span>'),
        'next_text'          => __('<span class="next"></span>'),
        'type'               => 'plain',
      );
      echo paginate_links($args); ?>
    </div>
  </section>
</div>
</main>
<?php get_footer(); ?>
