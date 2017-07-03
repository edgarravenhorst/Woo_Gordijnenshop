<?php get_header(); ?>
<header class="titlebar">
  <div class="container-fluid">
  <h1><?php the_title() ?></h1>
  </div>
</header>
<main class="page-content">
  <div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-8">
          <article id="page_content">
          <?php while ( have_posts() ) : the_post(); ?>
            <?php the_content(); ?>
          <?php endwhile; ?>
          </article>
        </div>
        <div class="col-12 col-md-4">
          <?php dynamic_sidebar( 'page-sidebar' ); ?>
        </div>
    </div>
  </div>
</main>

<?php get_footer(); ?>
