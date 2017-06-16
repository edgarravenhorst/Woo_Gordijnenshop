<?php get_header(); ?>
<header class="titlebar">
  <div class="container-fluid">
  <?php the_title() ?>
  </div>
</header>
<main class="page-content">
  <div class="container-fluid">
    <div class="row">
        <div class="col-12">
          <article id="page_content">
          <?php while ( have_posts() ) : the_post(); ?>
            <?php the_content(); ?>
          <?php endwhile; ?>
          </article>
        </div>
    </div>
  </div>
</main>
<?php get_footer(); ?>
