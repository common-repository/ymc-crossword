<?php

/**
 *
 * Plugin Name:       YMC Crossword
 * Description:       The plugin Crossword creates an easy crossword from the words of any combination.
 * Version:           2.4.2
 * Author:            YMC
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ymc-states-map
 * Domain Path:       /languages
 *
 * YMC Crossword is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * YMC Crossword is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Password. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**-------------------------------------------------------------------------------
 *    DEFINES
 * -------------------------------------------------------------------------------*/

if ( ! defined('YMC_CROSSWORD_VERSION') ) {
	   define( 'YMC_CROSSWORD_VERSION', '2.4.2' );
}

if ( ! defined('YMC_CROSSWORD_DIR') ) {
	   define( 'YMC_CROSSWORD_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined('YMC_CROSSWORD_URL') ) {
	   define( 'YMC_CROSSWORD_URL', plugins_url( '/', __FILE__ ) );
}



require_once( YMC_CROSSWORD_DIR . 'YmcCrossword/Crossword.php' );



