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
        <header class="titlebar">
          <h1><?php echo get_the_title(); ?></h1>
        </header>
      </div>
    </div>
    <div class="row">
      <article id="page_content">
        <div class="col-12">
          <?php while ( have_posts() ) : the_post(); ?>
            <?php the_content(); ?>
          <?php endwhile; ?>
        </div>
      </article>
    </div>
  </div>
</main>
<?php get_footer(); ?>
