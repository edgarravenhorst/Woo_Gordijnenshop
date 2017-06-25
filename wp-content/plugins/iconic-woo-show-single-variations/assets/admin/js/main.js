(function($, document) {

	var ajax = {

		cache: function() {
			ajax.vars = {};
			ajax.els = {};

			ajax.vars.count = [];

			ajax.els.process_overlay = $('.process-overlay');
			ajax.els.process = $('.process');
			ajax.els.process_content = $('.process__content--processing');
			ajax.els.process_loading = $('.process__content--loading');
			ajax.els.process_complete = $('.process__content--complete');
			ajax.els.process_from = $('.process__count-from');
			ajax.els.process_to = $('.process__count-to');
			ajax.els.process_total = $('.process__count-total');
			ajax.els.process_loading_bar_fill = $('.process__loading-bar-fill');
		},

		on_ready: function() {
			ajax.cache();
			ajax.watch_triggers();
		},

		/**
		 * Watch AJAX triggers.
		 */
		watch_triggers: function() {

			$('[data-iconic-wssv-ajax]').on( 'click', function(){

				var action = $( this ).data( 'iconic-wssv-ajax' );

				if( null == ajax[ action ] ) { return false; }

				ajax[ action ].run();

			});

			$('.process__close').on( 'click', function(){

				ajax.process.hide();

			});

		},

		/**
		 * Process product visibility.
		 */
		process_product_visibility: {

			run: function() {

				var limit = 10;

				ajax.process.show( 'loading' );

				ajax.get_count( 'product', function( count ){

					ajax.process.update_count( 1, limit, count );
					ajax.process.show( 'content' );

					ajax.batch( 'process_product_visibility', count, limit, 0, function( processing, new_offset ){

						if( ! processing ) {
							ajax.process.show( 'complete' );
							ajax.process.set_percentage( count, count );
						} else {
							var to = new_offset+limit;

							to = to >= count ? count : to;

							ajax.process.update_count( new_offset, to, count );
							ajax.process.set_percentage( new_offset, count );
						}

					});

				});

			}

		},

		/**
		 * Process modal.
		 */
		process: {

			/**
			 * Show.
			 */
			show: function( type ) {

				type = typeof type === "undefined" ? "content" : type;

				ajax.els.process_overlay.show();
				ajax.els.process.show();

				if( type === "loading" ) {
					ajax.els.process_loading.show();
					ajax.els.process_complete.hide();
					ajax.els.process_content.hide();
				} else if( type === "complete" ) {
					ajax.els.process_loading.hide();
					ajax.els.process_complete.show();
					ajax.els.process_content.hide();
				} else {
					ajax.els.process_loading.hide();
					ajax.els.process_complete.hide();
					ajax.els.process_content.show();
				}

			},

			/**
			 * Hide.
			 */
			hide: function() {

				ajax.els.process_overlay.hide();
				ajax.els.process.hide();
				ajax.els.process_loading.show();
				ajax.els.process_complete.hide();
				ajax.els.process_content.hide();
				ajax.process.reset_percentage();

			},

			/**
			 * Update count.
			 *
			 * @param int count_from
			 * @param int count_to
			 * @param int count_total
			 */
			update_count: function( count_from, count_to, count_total ) {

				ajax.els.process_from.text( count_from );
				ajax.els.process_to.text( count_to );
				ajax.els.process_total.text( count_total );

			},

			/**
			 * Set percentage.
			 *
			 * @param int complete
			 * @param int total
			 */
			set_percentage: function( complete, total ) {

				var percentage = (complete/total)*100;

				ajax.els.process_loading_bar_fill.css( 'width', percentage+'%' );

			},

			/**
			 * Reset percentage.
			 */
			reset_percentage: function() {

				ajax.els.process_loading_bar_fill.css( 'width', '0%' );

			}

		},

		/**
		 * Batch process.
		 */
		batch: function( action, total, limit, offset, callback ) {

			var processing = true,
				data = {
					'action': 'iconic_wssv_'+action,
					'limit': limit,
					'offset': offset
				};

			jQuery.post(ajaxurl, data, function( response ) {

				var new_offset = offset+limit;

				if( new_offset < total ) {
					ajax.batch( action, total, limit, new_offset, callback );
				} else {
					processing = false;
				}

				if( typeof callback === 'function' ) {
					callback( processing, new_offset );
				}

			});

		},

		/**
		 * Get count of products.
		 *
		 * @return int
		 */
		get_count: function( type, callback ){

			if( null != ajax.vars.count[ type ] ) {
				if( typeof callback === 'function' ) {
					callback( ajax.vars.count[ type ] );
				}
				return;
			}

			var data = {
				'action': 'iconic_wssv_get_'+type+'_count'
			};

			jQuery.post(ajaxurl, data, function(response) {

				if( typeof callback === 'function' ) {
					callback( response.count );
				}

				ajax.vars.count[ type ] = response.count;

			});

			return;
		}

	};

	$(document).ready( ajax.on_ready() );

}(jQuery, document));