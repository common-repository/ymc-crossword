<?php

namespace YmcCrossword;

use YmcCrossword\Backend\YmcCrossword_Admin_Variables as Variables;
use YmcCrossword\Frontend\YmcCrossword_Shortcode as Shortcode;
use YmcCrossword\Backend\YmcCrossword_Cpt as Cpt;
use YmcCrossword\Backend\YmcCrossword_Meta_Boxes as Boxes;
use YmcCrossword\Frontend\YmcCrossword_Assets_Loader as Assets;



if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Crossword {

	/**
	 * Instance
	 *
	 * @access public
	 * @static
	 */
	public static $instance = null;


	/**
	 * Assets loader.
	 *
	 * Holds the plugin assets loader responsible for conditionally enqueuing
	 * styles and script assets that were pre-enabled.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var Assets
	 */
	public $assets_loader;

	/**
	 * Custom Post Type.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var Cpt
	 */
	public $cpt;


	/**
	 * Meta Boxes.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var Boxes
	 */
	public $meta_boxes;


	/**
	 * Admin Variables.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var Variables
	 */
	public $variables;


	/**
	 * Shortcode.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var Shortcode
	 */
	public $shortcode;


	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @access public
	 *
	 * @return Crossword An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function __construct() {

		// Init Plugin
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Initialize the plugin
	 *
	 * Fired by plugins_loaded action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {

		$this->register_autoloader();
		$this->init_components();
	}

	/**
	 * Register YmcCrossword_Autoloader
	 *
	 * Autoloader loads all the classes needed to run the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function register_autoloader() {
		require_once YMC_CROSSWORD_DIR . 'YmcCrossword/YmcCrossword_Autoloader.php';
		YmcCrossword_Autoloader::run();
	}

	/**
	 * Init components.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function init_components() {

		$this->cpt = new Cpt();
		$this->variables = new Variables();
		$this->assets_loader = new Assets();
		$this->meta_boxes = new Boxes();
		$this->shortcode = new Shortcode();

	}
}

Crossword::instance();