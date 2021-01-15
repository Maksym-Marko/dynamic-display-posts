<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class MX_DDP_Add_Shortcodes
{


	/*
	* Registration of styles and scripts
	*/
	public static function mx_add_shortcodes()
	{

		// ddp's form
		add_shortcode( 'mx_ddp_page_template', array( 'MX_DDP_Add_Shortcodes', 'mxffi_add_ddp_template' ) );

		// Display list of questions
		
	}

		// form
		public static function mxffi_add_ddp_template( $atts )
		{
			ob_start(); 

			$post_type = 'post';

			if( isset( $atts['post_type'] ) ) {

				$post_type = $atts['post_type'];

			}

			?>

			<script>

				window.mx_ddp_post_type = '<?php echo $post_type; ?>';

				window.mx_ddp_tax_query = [];
				
			</script>

			<?php if( isset( $atts['taxonomy'] ) AND isset( $atts['term_ids'] ) ) : ?>

				<?php 
					$taxonomy = $atts['taxonomy'];

					$term_ids = explode( ',', $atts['term_ids'] );	
				?>

				<script>

					window.mx_ddp_post_type = '<?php echo $post_type; ?>'
				
					window.mx_ddp_tax_query = [

						<?php foreach ( $term_ids as $key => $value ) : ?>

							{
								taxonomy: '<?php echo $taxonomy; ?>',
								field: 'id',
								terms: '<?php echo $value; ?>'
							},

						<?php endforeach; ?>
					];

				</script>

			<?php endif; ?>			
		
			<div id="mx_ddp">

				<!-- search -->
				<mx_ddp_search
					:pageloading="pageLoading"
					@mx-search-request="searchQuestion"
				></mx_ddp_search>

				<!-- list of items -->
				<mx_ddp_list_items
					:getddpitems="ddpItems"
					:parsejsonerror="parseJSONerror"
					:pageloading="pageLoading"
					:load_img="loadImg"
					:no_items="noItemsDisplay"
				></mx_ddp_list_items>

				<!-- pagination -->
				<!-- <mx_ddp_pagination
					:pageloading="pageLoading"
					v-if="!parseJSONerror"
					:ddpcount="ddpCount"
					:ddpperpage="ddpPerPage"
					:ddpcurrentpage="ddpCurrentPage"					
					@get-ddp-page="changeddpPage"
				></mx_ddp_pagination>	 -->	

				<mx_ddp_load_more_button
					:pageloading="pageLoading"
					v-if="!parseJSONerror"
					:ddpcount="ddpCount"
					:ddpperpage="ddpPerPage"
					:ddpcurrentpage="ddpCurrentPage"
					@load_more="loadMoreItems"
					:load_img="loadImg"
					:load_more_progress="load_more_progress"
				></mx_ddp_load_more_button>

				<!-- form -->
				<!-- <mx_ddp_form></mx_ddp_form> -->

			</div>

			<?php return ob_get_clean();
		}

}

MX_DDP_Add_Shortcodes::mx_add_shortcodes();

?>