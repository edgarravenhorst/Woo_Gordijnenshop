<section class="post col-md-12">
  <section class="title">
    <h2><a href="<?php the_permalink();?>"><?php echo get_the_title(); ?></a></h2>
  </section>
  <section class="excerpt">
    <?php echo get_the_excerpt(); ?>
  </section>
  <a href="<?php the_permalink();?>">Lees meer</a>
</section>
