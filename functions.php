<?php 

	// E:\OpenServer\domains\my-domain.com\wp-content\themes\some_theme\inc\dynamic-display-posts/
	if ( ! defined( 'MX_DDP_ABS_PATH' ) ) {

		define( 'MX_DDP_ABS_PATH', dirname( __FILE__ ) . '/' );

	}

	// E:\OpenServer\domains\my-domain.com\wp-content\themes\some_theme/inc/dynamic-display-posts/
	if ( ! defined( 'MX_DDP_ABS_URL' ) ) {

		define( 'MX_DDP_ABS_URL', get_template_directory_uri() . '/inc/dynamic-display-posts/' );

	}

	// Global files version
	if ( ! defined( 'MX_DDP_VERSION' ) ) {

		define( 'MX_DDP_VERSION', time() );

	}

	/**
	* Helpers
	*/
	require_once MX_DDP_ABS_PATH . 'core/helpers.php';

	/**
	* Register scrtipts and styles
	*/
	require_once MX_DDP_ABS_PATH . 'classes/enqueue-scripts.php';

	/**
	* Add Shortcodes
	*/
	require_once MX_DDP_ABS_PATH . 'classes/add-shortcodes.php';

	/**
	* AJAX
	*/
	require_once MX_DDP_ABS_PATH . 'classes/database-talk.php';

?>