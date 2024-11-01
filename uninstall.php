<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uninstall plugin
 * Trigger Uninstall process only if WP_UNINSTALL_PLUGIN is defined
 */

if( ! defined('WP_UNINSTALL_PLUGIN') ) exit;

global $wpdb;

// Delete data from table wp_postmeta
$wpdb->get_results('DELETE FROM wp_postmeta WHERE meta_key IN (
                                  "ymc_bg_stage", 
                                  "ymc_bg_color", 
                                  "ymc_border_color", 
                                  "ymc_text_color", 
                                  "ymc_counter_color", 
                                  "ymc_question_color", 
                                  "ymc_bg_correct_ansver",
                                  "ymc_bg_incorrect_ansver",
                                  "ymc_align_question",
                                  "ymc_mute",
                                  "ymc_popup",
                                  "ymc_header_popup",
                                  "ymc_content_popup"
                                )');


// Delete data from table wp_posts
$wpdb->get_results('DELETE FROM wp_posts WHERE post_type IN ("ymc_crossword")');













