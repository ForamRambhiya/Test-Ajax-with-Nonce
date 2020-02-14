<?php
/**
 * @package TestAjax
 * Plugin Name: Test Ajax Nonce
 * Plugin URI: https://wisdmlabs.com/
 * Description: This is the example plugin to use Ajax with Nonce
 * Version: 1.0.0
 * Author: WisdmLabs
 * Author URI: https://wisdmlabs.com/
 * License: GPLv2 or later
 * Text Domain: testajax
 */

if ( ! defined( 'WPINC' ) ) { die; }

/* 1. REGISTER SHORTCODE
------------------------------------------ */

/* Init Hook */
add_action( 'init', 'my_wp_ajax_noob_plugin_init', 10 );

/**
 * Init Hook to Register Shortcode.
 * @since 1.0.0
 */
function my_wp_ajax_noob_plugin_init() {

	/* Register Shortcode */
	add_shortcode( 'john-cena', 'my_wp_ajax_noob_john_cena_shortcode_callback' );

}

/**
 * Shortcode Callback
 * Just display empty div. The content will be added via AJAX.
 */
function my_wp_ajax_noob_john_cena_shortcode_callback(){

	/* Enqueue JS only if this shortcode loaded. */
	wp_enqueue_script( 'my-wp-ajax-noob-john-cena-script' );

	/* Create Nonce */
	$nonce = wp_create_nonce( 'john-cena-shortcode' );

	/* Output empty div. */
	return '<div id="john-cena" data-nonce="' . esc_attr( $nonce ) . '"></div>';
}


/* 2. REGISTER SCRIPT
------------------------------------------ */

/* Enqueue Script */
add_action( 'wp_enqueue_scripts', 'my_wp_ajax_noob_scripts' );

/**
 * Scripts
 */
function my_wp_ajax_noob_scripts(){

	/* Plugin DIR URL */
	$url = trailingslashit( plugin_dir_url( __FILE__ ) );

	/* JS + Localize */
	wp_register_script( 'my-wp-ajax-noob-john-cena-script', $url . "assets/script.js", array( 'jquery' ), '1.0.0', true );

	/* Localize Script Data */
	$ajax_data = array(
		'url'   => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'john-cena-script-nonce' ),
	);

	/* Send Data as JS var via Localize Script */
	wp_localize_script( 'my-wp-ajax-noob-john-cena-script', 'john_cena_ajax_data', $ajax_data  );
}


/* 3. AJAX CALLBACK
------------------------------------------ */

/* AJAX action callback */
add_action( 'wp_ajax_john_cena', 'my_wp_ajax_noob_john_cena_ajax_callback' );
add_action( 'wp_ajax_nopriv_john_cena', 'my_wp_ajax_noob_john_cena_ajax_callback' );


/**
 * Ajax Callback
 */
function my_wp_ajax_noob_john_cena_ajax_callback(){

	/* Check DOM nonce */
	check_ajax_referer( 'john-cena-shortcode', 'nonce_data' );

	/* Check Localize Script Nonce */
	check_ajax_referer( 'john-cena-script-nonce', 'nonce_ajax' );

	/* Get request */
	$post_data = wp_unslash( $_POST );
	$first_name = isset( $post_data['first_name'] ) ? $post_data['first_name'] : 'N/A';
	$last_name  = isset( $post_data['last_name'] ) ? $post_data['last_name'] : 'N/A';
	?>
	<p>Hello. Your first name is <?php echo esc_html( $first_name ); ?>.</p>
	<p>And your last name is <?php echo esc_html( $last_name ); ?>.</p>
	<?php
	die(); // required. to end AJAX request.
}