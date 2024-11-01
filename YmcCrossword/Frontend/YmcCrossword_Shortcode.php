<?php

namespace YmcCrossword\Frontend;

use YmcCrossword\Crossword;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class YmcCrossword_Shortcode
 * @package YmcCrossword\Frontend
 * Shortcode
 */

class YmcCrossword_Shortcode {

	public function __construct() {

		add_shortcode("ymc-crossword", [ $this, "run_shortcode" ]);
	}

	public function run_shortcode( $atts ) {

		$atts = shortcode_atts( [
			'id' => '',
		], $atts );

		$post_id = $atts['id'];

		$post_type = get_post_type($post_id);

		$post_status = get_post_status($post_id);

		$crossword = get_post_meta($post_id, 'ymc_crossword', true);

		$rows = json_decode($crossword, true, 512, JSON_UNESCAPED_UNICODE);

		$bg_stage = Crossword::instance()->variables->get_bg_stage($post_id);
		$bg_color = Crossword::instance()->variables->get_bg_color($post_id);
		$border_color = Crossword::instance()->variables->get_border_color($post_id);
		$text_color = Crossword::instance()->variables->get_text_color($post_id);
		$counter_color = Crossword::instance()->variables->get_counter_color($post_id);
		$question_color = Crossword::instance()->variables->get_question_color($post_id);
		$bg_correct_ansver = Crossword::instance()->variables->get_bg_correct_ansver($post_id);
		$bg_incorrect_ansver = Crossword::instance()->variables->get_bg_incorrect_ansver($post_id);
		$align_question = Crossword::instance()->variables->get_align_question($post_id);
		$popup = Crossword::instance()->variables->get_popup($post_id);
		$mute = Crossword::instance()->variables->get_mute($post_id);
		$header_popup = Crossword::instance()->variables->get_header_popup($post_id);
		$content_popup = Crossword::instance()->variables->get_content_popup($post_id);
		$theme_crossword = Crossword::instance()->variables->get_theme_crossword($post_id);

        ?>

    <style>
		     .ymc-crossword-container .ymc-row {
				background-color : <?php esc_html_e($bg_stage); ?>;
             }
             .ymc-crossword-container .ymc-square .ymc-char {
                background-color : <?php esc_html_e($bg_color); ?>;
                border-color : <?php esc_html_e($border_color); ?>;
                color : <?php esc_html_e($text_color); ?>;
             }
             .ymc-crossword-container .ymc-square .ymc-num {
                color: <?php esc_html_e($counter_color); ?>;
             }
             .ymc-crossword-container .ymc-line .ymc-clue {
                color: <?php esc_html_e($question_color); ?>;
             }
             .ymc-crossword-container .ymc-square.ymc-correct .ymc-char {
                background: <?php esc_html_e($bg_correct_ansver); ?>;
             }
             .ymc-crossword-container .ymc-line .ymc-clue.ymc-completed {
                background: <?php esc_html_e($bg_correct_ansver); ?>;
             }
             .ymc-crossword-container .ymc-square.ymc-error .ymc-char {
                background: <?php esc_html_e($bg_incorrect_ansver); ?>;
             }
             .ymc-crossword-container .ymc-line .ymc-clue.ymc-clue-error {
                background: <?php esc_html_e($bg_incorrect_ansver); ?>;
             }
       </style>

    <?php

		$output = '<div id="ymc-crossword-container" class="ymc-crossword-container '. esc_attr($align_question) .'" data-mute="'. esc_attr($mute) .'" data-popup="'. esc_attr($popup) .'">';

        if ( !empty( $post_id ) && $post_type === 'ymc_crossword' &&
            $post_status === 'publish' && is_array( $rows ) && !empty( $rows ) ) :
            ob_start();
        ?>

            <div class="ymc-crossword dragscroll"></div>
            <div class="ymc-crossword-panel">
            <div class="ymx-theme-crossword">&laquo;<?php echo $theme_crossword; ?>&raquo;</div>
            <div class="ymc-col">
            <div class="ymc-header-clue"><?php esc_html_e('Across:', 'ymc-crossword')?></div>
            <div class="ymc-clueBlock" id="ymc-cluesAcross">
            <div class="ymc-clueDirection" id="ymcDirectionAcross"></div>

            <?php
                foreach ($rows as $row) : ?>
                <div class="ymc-line">
                    <input class="ymc-word" type="hidden" value="<?php esc_attr_e(strtoupper($row['word'])); ?>" />
                    <span class="ymc-lineNum"></span>
                    <a class="ymc-clue" href="#"><?php esc_html_e($row['clue']); ?></a>
                </div>
            <?php endforeach;
            ?>

            </div>
            </div>
            <div class="ymc-col">
            <div class="ymc-header-clue"><?php esc_html_e('Down:', 'ymc-crossword')?></div>
            <div class="ymc-clueBlock" id="ymc-cluesDown">
            <div class="ymc-clueDirection" id="ymcDirectionDown"></div>
            </div>
            </div>
            </div>

            <div class="ymc-control-panel">
            <div class="ymc-score">
                <?php esc_html_e('Score:', 'ymc-crossword')?>
                <span class="ymc-scoreNum">0</span></div>
            <div class="ymc-bl ymc-actions">
                <input class="ymc-button ymc-button-reload" type="button" title="<?php esc_html_e('Reload Crossword','ymc-crossword'); ?>">
                <input class="ymc-button ymc-button-reset" type="button" title="<?php esc_html_e('Reset Timer','ymc-crossword'); ?>">
                <input class="ymc-button ymc-button-start" type="button" title="<?php esc_html_e('Start Timer','ymc-crossword'); ?>">
                <input class="ymc-button ymc-button-pause" type="button" title="<?php esc_html_e('Pause Timer','ymc-crossword'); ?>">
            </div>
            <div class="ymc-bl ymc-timer">
                <span class="ymc-min">00</span> : <span class="ymc-sec">00</span>
            </div>
            <div class="ymc-progress-bar"><?php esc_html_e('Completed:', 'ymc-crossword'); ?>
            <span class="ymc-completed-words">0</span> / <span class="ymc-total-words">0</span></div>
            </div>
            <div class="ymc-popup-crossword">
            <div class="ymc-crossword-content-popup">
                <button class="ymc-crossword-btn-close" title="Close popup">x</button>
                <header class="ymc-crossword-header">
                    <?php esc_html_e($header_popup); ?>
                    <div class="ymc-game-over">Your time: <span class="ymc-min">00</span>:<span class="ymc-sec">00</span></div>
                </header>
                <div class="ymc-entry-content">
                    <?php  echo do_shortcode( $content_popup ); ?>
                </div>
                <div class="ymc-social-panel">
                    <a class="ymc-social ymc-fb" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($this->get_page_url()); ?>&quote=<?php echo rawurlencode(get_the_title()); ?>" target="_blank"></a>
                    <a class="ymc-social ymc-tw" href="https://twitter.com/intent/tweet?text=<?php echo rawurlencode(get_the_title()); ?>&url=<?php echo urlencode($this->get_page_url()); ?>" target="_blank"></a>
                    <a class="ymc-social ymc-ln" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($this->get_page_url()); ?>" target="_blank"></a>
                    <a class="ymc-social ymc-tumblr" href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=<?php echo urlencode($this->get_page_url()); ?>&caption=<?php echo rawurlencode(get_the_title()); ?>" target="_blank"></a>
                </div>
            </div>
        </div>

        <?php else :
	        $output .= "<div class='ymc-crossword-notice'>" . esc_html__('Crossword: ID parameter is missing or invalid.', 'ymc-crossword') ."</div>";
        endif; ?>

		<?php $output .= ob_get_contents();
		ob_end_clean();

		return $output;

	}

    public function get_page_url() {

	    $url =  sanitize_url(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://");

	    $url .= sanitize_url($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

	    return $url;
    }
}