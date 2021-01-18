<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class MX_DDP_Enqueue_Scripts_Register
{

	/*
	* Registration of styles and scripts
	*/
	public static function mx_ddp_register()
	{

		// register scripts and styles
		add_action( 'wp_enqueue_scripts', array( 'MX_DDP_Enqueue_Scripts_Register', 'mx_ddp_enqueue' ) );

	}

		public static function mx_ddp_enqueue()
		{

			wp_enqueue_style( 'mx_ddp_style', MX_DDP_ABS_URL . 'css/style.css', array(), MX_DDP_VERSION, 'all' );

			// include Vue.js
				// dev version
				wp_enqueue_script( 'mx_ddp_vue_js', MX_DDP_ABS_URL . 'add/vue_js/vue.dev.js', array(), '29.05.20', true );

				// production version
				//wp_enqueue_script( 'mx_ddp_vue_js', MX_DDP_ABS_URL . 'add/vue_js/vue.production.js', array(), '29.05.20', true );
			
			wp_enqueue_script( 'mx_ddp_script', MX_DDP_ABS_URL . 'js/script.js', [ 'mx_ddp_vue_js', 'jquery' ], MX_DDP_VERSION, true );

			$agre_link = get_option( '_mx_simple_faq_agree_link' );

			if( !$agre_link ) {

				$agre_link = '#';

			}

			wp_localize_script( 'mx_ddp_script', 'mx_ddpdata_obj_front', array(

				'nonce' => wp_create_nonce( 'mx_ddpdata_nonce_request_front' ),

				'ajax_url' => admin_url( 'admin-ajax.php' ),

				'loading_img' => MX_DDP_ABS_URL . 'img/faq_sending.gif',

				'no_phot' => MX_DDP_ABS_URL . 'img/no-photo.jpg',


				'texts'	=> array(
					'error_getting' 	=> __( 'Error getting FAQ from database!', 'mx_ddp-domain' ),
					'no_questions'		=> __( 'There are no questions yet.', 'mx_ddp-domain' ),
					'nothing_found'		=> __( 'Nothing found!', 'mx_ddp-domain' )					
				)

			) );	
		
		}

}

MX_DDP_Enqueue_Scripts_Register::mx_ddp_register();