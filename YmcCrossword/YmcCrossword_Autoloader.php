<?php

namespace YmcCrossword;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YmcCrossword_Autoloader {

	/**
	 * Run Autoloader.
	 *
	 * Register a function as `__autoload()` implementation.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function run() {

		spl_autoload_register(function($class) {

			$filename = $class .  '.php';
			$filename =  YMC_CROSSWORD_DIR . str_replace('\\', DIRECTORY_SEPARATOR, $filename);

			if ( is_readable( $filename ) ) {
				require_once $filename;
			}
		});
	}
}