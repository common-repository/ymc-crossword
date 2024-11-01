<?php

namespace YmcCrossword\Backend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class YmcCrossword_Cpt
 * @package YmcCrossword\Backend
 * Create Custom Post Type
 */

class YmcCrossword_Cpt {

	const post_type = 'ymc_crossword';

	public function __construct() {
		add_action( 'init', [ $this, 'register_post_type' ], 0 );
	}

	public function register_post_type() {

		register_post_type( self::post_type,
			array(
				'labels'              => array(
					'name'          => __( 'Crossword', 'ymc-crossword' ),
					'singular_name' => __( 'Crossword', 'ymc-crossword' ),
				),
				'public'              => false,
				'hierarchical'        => false,
				'exclude_from_search' => true,
				'show_ui'             => current_user_can( 'manage_options' ) ? true : false,
				'show_in_admin_bar'   => false,
				'menu_position'       => 7,
				'menu_icon'           => 'dashicons-book-alt',
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => array(
					'title',
				),
			) );
	}

}