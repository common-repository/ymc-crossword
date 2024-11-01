<?php

namespace YmcCrossword\Frontend;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YmcCrossword_Assets_Loader {

	/**
	 * Init.
	 *
	 * Initialize Scripts CSS & JS.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', [ $this, 'backend_embed_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_embed_scripts' ] );

	}

	// Backend enqueue scripts & style.
	public function backend_embed_scripts() {

		wp_enqueue_style( 'ymc-crossword-' . $this->generate_handle(), YMC_CROSSWORD_URL . 'YmcCrossword/assets/css/crossword-admin.css', array(), YMC_CROSSWORD_VERSION);
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_script( 'ymc-crossword-'.$this->generate_handle(), YMC_CROSSWORD_URL . 'YmcCrossword/assets/js/crossword-admin.js', array( 'jquery' ), YMC_CROSSWORD_VERSION, true );
	}


	// Frontend enqueue scripts & style.
	public function frontend_embed_scripts() {

		wp_enqueue_style( 'ymc-crossword-'.$this->generate_handle(), YMC_CROSSWORD_URL . 'YmcCrossword/assets/css/crossword.css', array(), YMC_CROSSWORD_VERSION);
		wp_enqueue_script( 'ymc-crossword-dragscroll'.$this->generate_handle(), YMC_CROSSWORD_URL . 'YmcCrossword/assets/js/dragscroll.js', array( 'jquery' ), YMC_CROSSWORD_VERSION, true );
		wp_enqueue_script( 'ymc-crossword-'.$this->generate_handle(), YMC_CROSSWORD_URL . 'YmcCrossword/assets/js/crossword.js', array( 'jquery' ), YMC_CROSSWORD_VERSION, true );
		wp_localize_script( 'ymc-crossword-'.$this->generate_handle(), '_ymc_crossword_object',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce'    => wp_create_nonce('get_data_crossword'),
				'path'     => YMC_CROSSWORD_URL.'/YmcCrossword'
			));


	}

	// Generate handle
	public function generate_handle() {
		return wp_create_nonce('ymc-crossword');
	}

}