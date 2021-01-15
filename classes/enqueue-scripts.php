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
			
			wp_enqueue_script( 'mx_ddp_script', MX_DDP_ABS_URL . 'js/script.js', array( 'mx_ddp_vue_js', 'jquery' ), MX_DDP_VERSION, true );

			$agre_link = get_option( '_mx_simple_faq_agree_link' );

			if( !$agre_link ) {

				$agre_link = '#';

			}

			wp_localize_script( 'mx_ddp_script', 'mx_ddpdata_obj_front', array(

				'nonce' => wp_create_nonce( 'mx_ddpdata_nonce_request_front' ),

				'ajax_url' => admin_url( 'admin-ajax.php' ),

				'loading_img' => MX_DDP_ABS_URL . 'img/faq_sending.gif',

				'no_user_phot' => MX_DDP_ABS_URL . 'img/no-user-photo.png',


				'texts'	=> array(
					'search' 			=> __( 'Search', 'mx_ddp-domain' ),
					'find' 				=> __( 'Find ...', 'mx_ddp-domain' ),
					'make_question' 	=> __( 'Make a question', 'mx_ddp-domain' ),
					'error_getting' 	=> __( 'Error getting FAQ from database!', 'mx_ddp-domain' ),
					'call_to_question' 	=> __( 'If you have any questions, write to us, and we will answer you.', 'mx_ddp-domain' ),
					'p_your_name' 		=> __( 'Your name', 'mx_ddp-domain' ),
					'your_name' 		=> __( 'Enter your name', 'mx_ddp-domain' ),
					'p_your_email' 		=> __( 'Your email', 'mx_ddp-domain' ),
					'your_email' 		=> __( 'Enter your email', 'mx_ddp-domain' ),
					'your_email_failed'	=> __( 'Invalid email format', 'mx_ddp-domain' ),
					'subject'			=> __( 'Question Title', 'mx_ddp-domain' ),
					'enter_subject'		=> __( 'Enter Question\'s Title', 'mx_ddp-domain' ),
					'agre_text'			=> __( 'I consent to the processing of personal data in accordance with', 'mx_ddp-domain' ),
					'agre_doc_name'		=> __( 'Regulation', 'mx_ddp-domain' ),
					'agre_failed'		=> __( 'You must give consent to the processing of personal data', 'mx_ddp-domain' ),
					'your_message'		=> __( 'Enter your message', 'mx_ddp-domain' ),
					'your_message_failed'=> __( 'Enter your question', 'mx_ddp-domain' ),
					'submit'			=> __( 'Submit', 'mx_ddp-domain' ),
					'success_sent'		=> __( 'Your question has been sent. Thank!', 'mx_ddp-domain' ),
					'no_questions'		=> __( 'There are no questions yet.', 'mx_ddp-domain' ),
					'nothing_found'		=> __( 'Nothing found!', 'mx_ddp-domain' ),
					'agre_link'			=> $agre_link
					
				)

			) );	
		
		}

}

MX_DDP_Enqueue_Scripts_Register::mx_ddp_register();