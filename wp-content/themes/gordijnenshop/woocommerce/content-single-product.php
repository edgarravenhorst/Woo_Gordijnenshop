<?php
/**
* The template for displaying product content in the single-product.php template
*
* This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
*
* HOWEVER, on occasion WooCommerce will need to update template files and you
* (the theme developer) will need to copy the new files to your theme to
* maintain compatibility. We try to do this as little as possible, but it does
* happen. When this occurs the version of the template file will be bumped and
* the readme will list any important changes.
*
* @see 	    https://docs.woocommerce.com/document/template-structure/
* @author 		WooThemes
* @package 	WooCommerce/Templates
* @version     3.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php
/**
* woocommerce_before_single_product hook.
*
* @hooked wc_print_notices - 10
*/
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php post_class(""); ?>>
	<div id="main-description" class="row">

		<div class="col-md-7"><?php
		woocommerce_template_single_excerpt();
		woocommerce_template_single_meta();

		woocommerce_template_single_price();
		woocommerce_template_single_add_to_cart();

		?></div>

		<div class="col-md-5">
			<?php
			woocommerce_show_product_sale_flash();
			woocommerce_show_product_images();
			//do_action( 'woocommerce_before_single_product_summary' );
			?>
		</div>

		<div class="col-9">



			<?php
			/**
			* woocommerce_single_product_summary hook.
			*
			* @hooked woocommerce_template_single_title - 5
			* @hooked woocommerce_template_single_rating - 10
			* @hooked woocommerce_template_single_price - 10
			* @hooked woocommerce_template_single_excerpt - 20
			* @hooked woocommerce_template_single_add_to_cart - 30
			* @hooked woocommerce_template_single_meta - 40
			* @hooked woocommerce_template_single_sharing - 50
			* @hooked WC_Structured_Data::generate_product_data() - 60
			*/

			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
			do_action( 'woocommerce_single_product_summary' );
			?>

		</div>
	</div>

	<!-- <div class="col-md-3">
	<?php
	/**
	* woocommerce_sidebar hook.
	*
	* @hooked woocommerce_get_sidebar - 10
	*/

	dynamic_sidebar( 'page-sidebar' );
	//do_action( 'woocommerce_sidebar' );
	?>

</div> -->

<div class="row">

	<div class="col-12">

		<?php
		/**
		* woocommerce_after_single_product_summary hook.
		*
		* @hooked woocommerce_output_product_data_tabs - 10
		* @hooked woocommerce_upsell_display - 15
		* @hooked woocommerce_output_related_products - 20
		*/
		remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
		remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
		do_action( 'woocommerce_after_single_product_summary' );
		?>
	</div>
</div>


</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
