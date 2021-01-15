<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class MX_DDP_Database_Talk
{

	/*
	* Registration of styles and scripts
	*/
	public static function mxffi_db_ajax()
	{

		// get count of ddp items
		add_action( 'wp_ajax_mx_get_count_ddp_items', array( 'MX_DDP_Database_Talk', 'mx_get_count_ddp_items' ) );

			add_action( 'wp_ajax_nopriv_mx_get_count_ddp_items', array( 'MX_DDP_Database_Talk', 'mx_get_count_ddp_items' ) );

		// get ddp items
		add_action( 'wp_ajax_mx_get_ddp_items', array( 'MX_DDP_Database_Talk', 'mx_get_ddp_items' ) );

			add_action( 'wp_ajax_nopriv_mx_get_ddp_items', array( 'MX_DDP_Database_Talk', 'mx_get_ddp_items' ) );

		// search items
		add_action( 'wp_ajax_mx_search_ddp_items', array( 'MX_DDP_Database_Talk', 'mx_search_ddp_items' ) );

			add_action( 'wp_ajax_nopriv_mx_search_ddp_items', array( 'MX_DDP_Database_Talk', 'mx_search_ddp_items' ) );

		// load more items
		add_action( 'wp_ajax_mx_ddp_load_more_items', array( 'MX_DDP_Database_Talk', 'mx_ddp_load_more_items' ) );

			add_action( 'wp_ajax_nopriv_mx_ddp_load_more_items', array( 'MX_DDP_Database_Talk', 'mx_ddp_load_more_items' ) );
			

	}

		// load more
		public static function mx_ddp_load_more_items()
		{

			if( empty( $_POST['nonce'] ) ) wp_die();

			if( wp_verify_nonce( $_POST['nonce'], 'mxvjfepcdata_nonce_request_front' ) ) {

				$query = sanitize_text_field( $_POST['query'] );

				$current_page = sanitize_text_field( $_POST['current_page'] );

				$current_page = intval( $current_page );

				$ddp_per_page = sanitize_text_field( $_POST['ddp_per_page'] );

				$current_page = ( $current_page * $ddp_per_page ) - $ddp_per_page;

				global $wpdb;

				$posts_table = $wpdb->prefix . 'posts';

				$term_relationships_table = $wpdb->prefix . 'term_relationships';

				$sql_str = "SELECT ID, post_title, post_date, post_title, post_content, post_excerpt FROM $posts_table
						WHERE post_type = '$post_type'
							AND post_status = 'publish'
							AND ( post_title LIKE '%$query%'
								OR post_content LIKE '%$query%' )
						ORDER BY post_date DESC
						LIMIT $current_page, $ddp_per_page";

				if( isset( $_POST["tax_query"] ) ) {

					$tax_id = $_POST["tax_query"][0]['terms'];

					$sql_str = "SELECT ID, post_title, post_date, post_title, post_content, post_excerpt				
						FROM $posts_table
						INNER JOIN $term_relationships_table ON $posts_table.ID = $term_relationships_table.object_id
						WHERE $term_relationships_table.term_taxonomy_id = $tax_id
							AND $posts_table.post_status = 'publish'
							AND ( $posts_table.post_title LIKE '%$query%'
								OR $posts_table.post_content LIKE '%$query%' )
						ORDER BY post_date DESC
						LIMIT $current_page, $ddp_per_page
					";

				}

				$posts_id_results = $wpdb->get_results( $sql_str );				

				foreach ( $posts_id_results as $key => $value ) {

					$the_thumbnail = get_the_post_thumbnail_url( $value->ID );

					$posts_id_results[$key]->the_thumbnail = $the_thumbnail;

					$the_permalink = get_the_permalink( $value->ID );

					$posts_id_results[$key]->the_permalink = $the_permalink;
			
				}				

				$items_stuff = json_encode( $posts_id_results );

				echo $items_stuff;

				wp_die();

			}

		}

		// add question
		public static function add_new_ddp()
		{
			
			if( empty( $_POST['nonce'] ) ) wp_die();

			if( wp_verify_nonce( $_POST['nonce'], 'mxvjfepcdata_nonce_request_front' ) ) {

				$post_ID = wp_insert_post(

					array(

						'post_title' 	=> sanitize_text_field( $_POST['subject'] ),
						'post_content'	=> sanitize_textarea_field( $_POST['message'] ),
						'post_type' 	=> 'leeds_people',
						'post_status' 	=> 'verification'

					)

				);

				if( gettype( $post_ID ) == 'integer' ) {

					// user name
					update_post_meta( $post_ID, '_mxffi_user_name', sanitize_text_field( $_POST['user_name'] ) );

					// user email
					update_post_meta( $post_ID, '_mxffi_user_email', sanitize_email( $_POST['user_email'] ) );

					$email = get_option( '_mx_simple_ddp_admin_email' );

					if( !$email ) {

						$email = get_user_by( 'ID', 1 )->user_email;

					}

					$websit_name = get_bloginfo( 'name' );

					$websit_domain = get_site_url();

					$websit_domain = str_replace( 'http://', '', $websit_domain );

					$websit_domain = str_replace( 'https://', '', $websit_domain );

					$header  = 'From: ' . $websit_name .' <support@' . $websit_domain . '>' . "\r\n";
					$header .= 'Reply-To: support@' . $websit_domain . "\r\n";

					$header .= "Content-Type: text/html; charset=UTF-8\r\n";

					$subject = __( 'You\'ve received the new Question.', 'mxffi-domain' );

					$message = '<p>' . __( 'User', 'mxffi-domain' ) . ' <b>' . esc_html( $_POST['user_name'] ) . '</b> ' . __( 'has sent a question.', 'mxffi-domain' ) . '</p>';

					$message .= '<p><b>' . esc_html( $_POST['message'] ) . '</b></p>';					

					add_filter( 'wp_mail_content_type', array( 'MX_DDP_Database_Talk', 'mx_send_html' ) );

					wp_mail( $email, $subject, $message, $header );

					remove_filter( 'wp_mail_content_type', array( 'MX_DDP_Database_Talk', 'mx_send_html' ) );

				}

				echo gettype( $post_ID );

			}

			wp_die();

		}

		public static function mx_send_html()
		{
			return "text/html";
		}

		// get ddp item
		public static function mx_get_ddp_items()
		{

			if( empty( $_POST['nonce'] ) ) wp_die();

			if( wp_verify_nonce( $_POST['nonce'], 'mxvjfepcdata_nonce_request_front' ) ) {	

				$post_type = 'post';

				if( isset( $_POST['post_type'] ) ) {

					$post_type = $_POST['post_type'];

				}		

				$query = sanitize_text_field( $_POST['query'] );

				$current_page = sanitize_text_field( $_POST['current_page'] );

				$current_page = intval( $current_page );

				$ddp_per_page = sanitize_text_field( $_POST['ddp_per_page'] );

				$current_page = ( $current_page * $ddp_per_page ) - $ddp_per_page;

				global $wpdb;

				$posts_table = $wpdb->prefix . 'posts';

				$term_relationships_table = $wpdb->prefix . 'term_relationships';

				$sql_str = "SELECT ID, post_title, post_date, post_title, post_content, post_excerpt FROM $posts_table
						WHERE post_type = '$post_type'
							AND post_status = 'publish'
							AND ( post_title LIKE '%$query%'
								OR post_content LIKE '%$query%' )
						ORDER BY post_date DESC
						LIMIT $current_page, $ddp_per_page";

				if( isset( $_POST["tax_query"] ) ) {

					$tax_id = $_POST["tax_query"][0]['terms'];

					$sql_str = "SELECT ID, post_title, post_date, post_title, post_content, post_excerpt				
						FROM $posts_table
						INNER JOIN $term_relationships_table ON $posts_table.ID = $term_relationships_table.object_id
						WHERE $term_relationships_table.term_taxonomy_id = $tax_id
							AND $posts_table.post_status = 'publish'
							AND ( $posts_table.post_title LIKE '%$query%'
								OR $posts_table.post_content LIKE '%$query%' )
						ORDER BY post_date DESC
						LIMIT $current_page, $ddp_per_page
					";

				}

				$posts_id_results = $wpdb->get_results( $sql_str );				

				foreach ( $posts_id_results as $key => $value ) {

					// $user_name = get_post_meta( $value->ID, '_mxffi_user_name', true );

					// $response = get_post_meta( $value->ID, '_mxffi_ddp_response', true );

					// $posts_id_results[$key]->user_name = $user_name;

					// $posts_id_results[$key]->answer = $response;

					$the_thumbnail = get_the_post_thumbnail_url( $value->ID );

					$posts_id_results[$key]->the_thumbnail = $the_thumbnail;

					$the_permalink = get_the_permalink( $value->ID );

					$posts_id_results[$key]->the_permalink = $the_permalink;
			
				}				

				$items_stuff = json_encode( $posts_id_results );

				echo $items_stuff;

			}

			wp_die();

		}

		// get count of ddp items
		public static function mx_get_count_ddp_items()
		{

			if( empty( $_POST['nonce'] ) ) wp_die();

			if( wp_verify_nonce( $_POST['nonce'], 'mxvjfepcdata_nonce_request_front' ) ) {

				$post_type = 'post';

				if( isset( $_POST['post_type'] ) ) {

					$post_type = $_POST['post_type'];

				}	

				$query = sanitize_text_field( $_POST['query'] );

				global $wpdb;

				$posts_table = $wpdb->prefix . 'posts';

				$term_relationships_table = $wpdb->prefix . 'term_relationships';

				$sql_str = "SELECT COUNT(ID)
					FROM $posts_table
					WHERE post_type = '$post_type'
						AND post_status = 'publish'
						AND ( post_title LIKE '%$query%'
							OR post_content LIKE '%$query%' )
				";

				if( isset( $_POST["tax_query"] ) ) {

					$tax_id = $_POST["tax_query"][0]['terms'];

					$sql_str = "SELECT COUNT(ID)				
						FROM $posts_table
						INNER JOIN $term_relationships_table ON $posts_table.ID = $term_relationships_table.object_id
						WHERE $term_relationships_table.term_taxonomy_id = $tax_id
							AND $posts_table.post_status = 'publish'
							AND ( $posts_table.post_title LIKE '%$query%'
								OR $posts_table.post_content LIKE '%$query%' )
					";

				}

				$ddp_coung = $wpdb->get_var( $sql_str );

				echo $ddp_coung;

			}

			wp_die();

		}

		// get ddp item by search
		public static function mx_search_ddp_items()
		{

			if( empty( $_POST['nonce'] ) ) wp_die();

			if( wp_verify_nonce( $_POST['nonce'], 'mxvjfepcdata_nonce_request_front' ) ) {

				$post_type = 'post';

				if( isset( $_POST['post_type'] ) ) {

					$post_type = $_POST['post_type'];

				}	

				$query = sanitize_text_field( $_POST['query'] );

				$current_page = sanitize_text_field( $_POST['current_page'] );

				$current_page = intval( $current_page );

				$ddp_per_page = sanitize_text_field( $_POST['ddp_per_page'] );

				$current_page = ( $current_page * $ddp_per_page ) - $ddp_per_page;

				global $wpdb;

				$posts_table = $wpdb->prefix . 'posts';

				$term_relationships_table = $wpdb->prefix . 'term_relationships';

				$sql_str = "SELECT ID, post_title, post_date, post_title, post_content, post_excerpt FROM $posts_table
					WHERE post_type = '$post_type'
						AND post_status = 'publish'
						AND ( post_title LIKE '%$query%'
							OR post_content LIKE '%$query%' )
					ORDER BY post_date DESC
					LIMIT $current_page, $ddp_per_page";

				if( isset( $_POST["tax_query"] ) ) {

					$tax_id = $_POST["tax_query"][0]['terms'];

					$sql_str = "SELECT ID, post_title, post_date, post_title, post_content, post_excerpt
						FROM $posts_table
						INNER JOIN $term_relationships_table ON $posts_table.ID = $term_relationships_table.object_id
						WHERE $term_relationships_table.term_taxonomy_id = $tax_id
							AND $posts_table.post_status = 'publish'
							AND ( $posts_table.post_title LIKE '%$query%'
								OR $posts_table.post_content LIKE '%$query%' )
						ORDER BY post_date DESC
						LIMIT $current_page, $ddp_per_page
					";

				}

				$posts_id_results = $wpdb->get_results( $sql_str );

				foreach ( $posts_id_results as $key => $value ) {

					$the_thumbnail = get_the_post_thumbnail_url( $value->ID );

					$posts_id_results[$key]->the_thumbnail = $the_thumbnail;

					$the_permalink = get_the_permalink( $value->ID );

					$posts_id_results[$key]->the_permalink = $the_permalink;								

				}					

				$items_stuff = json_encode( $posts_id_results );

				echo $items_stuff;

			}

			wp_die();

		}

}

MX_DDP_Database_Talk::mxffi_db_ajax();

?>