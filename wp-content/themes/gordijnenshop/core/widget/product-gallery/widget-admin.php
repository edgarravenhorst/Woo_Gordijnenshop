<div id='<?= $this->id ?>'>
	<p>
		<label for="<?php echo $this->get_field_id('count'); ?>">Aantal producten:
			<input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo attribute_escape($count); ?>" />
		</label>
	</p>

    <p>
    <label for="<?php echo $this->get_field_id( 'category' ); ?>">Categorie:</label>

    <!--  <input class="checkbox" type="checkbox" <?php checked( $instance[ 'show_filter' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_filter' ); ?>" name="<?php echo $this->get_field_name( 'show_filter' ); ?>" /> -->


          <select name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>" class="widefat">


                  <option value="Alle categorieen" id="Alle categorieen">Alle categorieen</option>

                <?php
						$cats = get_terms('product_cat');
                foreach ($cats as $cat) {

                    if($category == $cat->slug)
                        echo '<option selected value="' . $cat->slug . '" id="' . $cat->name. '">'. $cat->name . '</option>';
                    else
											echo '<option value="' . $cat->slug . '" id="' . $cat->name. '">'. $cat->name . '</option>';

                }
                ?>

            </select>

    </p>
</div>
