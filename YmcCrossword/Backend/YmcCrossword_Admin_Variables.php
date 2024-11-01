<?php

namespace YmcCrossword\Backend;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class YmcCrossword_Admin_Variables
 * @package YmcCrossword\Backend
 * Hold all variables app
 */

class YmcCrossword_Admin_Variables {

	public $bg_stage = '#098ab8';

	public $bg_color = '#ffffff';

	public $border_color = '#303030';

	public $text_color = '#303030';

	public $counter_color = '#303030';

	public $question_color = '#303030';

	public $bg_correct_ansver = '#b3ffb4';

	public $bg_incorrect_ansver = '#f7b4b4';

	public $align_question = 'align-left';

	public $popup = '1';

	public $mute = '1';

	public $header_popup = 'Congratulations, you guessed all the words!';

	public $content_popup = 'Add custom text to the popup';

	public $theme_crossword = 'Crossword Theme';

	public function __construct() {}


	public function get_bg_stage( $post_id ) {

		if( !empty(get_post_meta($post_id, 'ymc_bg_stage', true)) ) {
			$this->bg_stage = get_post_meta($post_id, 'ymc_bg_stage', true);
		}
		return $this->bg_stage;
	}

	public function get_bg_color( $post_id ) {

		if( !empty(get_post_meta($post_id, 'ymc_bg_color', true)) ) {
			$this->bg_color = get_post_meta($post_id, 'ymc_bg_color', true);
		}
		return $this->bg_color;
	}

	public function get_border_color( $post_id ) {

		if( !empty(get_post_meta($post_id, 'ymc_border_color', true)) ) {
			$this->border_color = get_post_meta($post_id, 'ymc_border_color', true);
		}
		return $this->border_color;
	}

	public function get_text_color( $post_id ) {

		if( !empty(get_post_meta($post_id, 'ymc_text_color', true)) ) {
			$this->text_color = get_post_meta($post_id, 'ymc_text_color', true);
		}
		return $this->text_color;
	}

	public function get_counter_color( $post_id ) {

		if( !empty(get_post_meta($post_id, 'ymc_counter_color', true)) ) {
			$this->counter_color = get_post_meta($post_id, 'ymc_counter_color', true);
		}
		return $this->counter_color;
	}

	public function get_question_color( $post_id ) {

		if( !empty(get_post_meta($post_id, 'ymc_question_color', true)) ) {
			$this->question_color = get_post_meta($post_id, 'ymc_question_color', true);
		}
		return $this->question_color;
	}

	public function get_bg_correct_ansver( $post_id ) {

		if( !empty(get_post_meta($post_id, 'ymc_bg_correct_ansver', true)) ) {
			$this->bg_correct_ansver = get_post_meta($post_id, 'ymc_bg_correct_ansver', true);
		}
		return $this->bg_correct_ansver;
	}

	public function get_bg_incorrect_ansver( $post_id ) {

		if( !empty(get_post_meta($post_id, 'ymc_bg_incorrect_ansver', true)) ) {
			$this->bg_incorrect_ansver = get_post_meta($post_id, 'ymc_bg_incorrect_ansver', true);
		}
		return $this->bg_incorrect_ansver;
	}

	public function get_align_question( $post_id ) {

		if( !empty(get_post_meta($post_id, 'ymc_align_question', true)) ) {
			$this->align_question = get_post_meta($post_id, 'ymc_align_question', true);
		}
		return $this->align_question;
	}

	public function get_mute( $post_id ) {

		if( get_post_meta($post_id, 'ymc_mute', true) !== '' ) {
			$this->mute = get_post_meta($post_id, 'ymc_mute', true);
		}
		return $this->mute;
	}

	public function get_popup( $post_id ) {

		if( get_post_meta($post_id, 'ymc_popup', true) !== '' ) {
			$this->popup = get_post_meta($post_id, 'ymc_popup', true);
		}
		return $this->popup;
	}

	public function get_header_popup( $post_id ) {

		if( !empty(get_post_meta($post_id, 'ymc_header_popup', true)) ) {
			$this->header_popup = get_post_meta($post_id, 'ymc_header_popup', true);
		}
		return $this->header_popup;
	}

	public function get_content_popup( $post_id ) {

		if( !empty(get_post_meta($post_id, 'ymc_content_popup', true)) ) {
			$this->content_popup = get_post_meta($post_id, 'ymc_content_popup', true);
		}
		return $this->content_popup;
	}

	public function get_theme_crossword( $post_id ) {

		if( !empty(get_post_meta($post_id, 'ymc_theme_crossword', true)) ) {
			$this->theme_crossword = get_post_meta($post_id, 'ymc_theme_crossword', true);
		}
		return $this->theme_crossword;
	}



}