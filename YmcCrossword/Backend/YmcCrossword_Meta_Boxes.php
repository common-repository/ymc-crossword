<?php

namespace YmcCrossword\Backend;

use YmcCrossword\Crossword;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class YmcCrossword_Meta_Boxes
 * @package YmcCrossword\Backend
 * Create Meta Boxes in admin panel
 */

class YmcCrossword_Meta_Boxes {

	public function __construct() {

		add_action( 'add_meta_boxes', [ $this, 'add_post_metabox' ]);

		add_action( 'save_post', [ $this, 'save_meta_box' ], 10, 3);
	}

	public function save_meta_box( $post_id, $post, $update ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}

		if( 'ymc_crossword' === get_post_type($post_id) ) {

			if( ! empty( $_POST['ymc-crossword-clue'] ) && ! empty( $_POST['ymc-crossword-word']) ) {

				$array = [];
				$questions = sanitize_post($_POST['ymc-crossword-clue'], 'edit');
				$words     = sanitize_post($_POST['ymc-crossword-word'],'edit');

				unset( $questions[ 'filter' ] );
				unset( $questions[ 'ID' ] );
				unset( $words[ 'filter' ] );
				unset( $words[ 'ID' ] );

				$symbol = [ "\\\"", "&", "!", "@", "#", "$", "^", "*", "\\", "(", ")", "-", "+", "%", "_", "|", "/", "(", ")", "=", ":", "[", "]" ];

				for ( $i = 0; $i < count($questions); $i++ ) {

					$clue = str_replace($symbol, "", strip_tags( $questions[$i]) );
					$word = str_replace($symbol, "", strip_tags( $words[$i]) );

					$arr['clue'] = htmlspecialchars(str_replace(array("\\'"), "'", $clue), ENT_QUOTES);
					$arr['word'] = htmlspecialchars(str_replace(array("\\'"), "'", $word), ENT_QUOTES);
					$array[] = $arr;
				}

				$json = json_encode($array, JSON_UNESCAPED_UNICODE);

				update_post_meta( $post_id, 'ymc_crossword', $json );

			}
            else {
	            delete_post_meta( $post_id, 'ymc_crossword' );
            }
		}

		// Header Popup
		if( isset($_POST['ymc_theme_crossword']) ) {
			$theme_crossword = !empty( $_POST['ymc_theme_crossword'] ) ? sanitize_text_field( $_POST['ymc_theme_crossword'] ) : Crossword::instance()->variables->theme_crossword;
			update_post_meta( $post_id, 'ymc_theme_crossword', $theme_crossword );
		}
		// Bg scene
		if( isset($_POST['ymc_bg_stage']) ) {
			$bg_stage = !empty( $_POST['ymc_bg_stage'] ) ? sanitize_hex_color( $_POST['ymc_bg_stage'] ) : Crossword::instance()->variables->bg_stage;
			update_post_meta( $post_id, 'ymc_bg_stage', $bg_stage );
		}
		// Bg color
		if( isset($_POST['ymc_bg_color']) ) {
			$bg_color = !empty( $_POST['ymc_bg_color'] ) ? sanitize_hex_color( $_POST['ymc_bg_color'] ) : Crossword::instance()->variables->bg_color;
			update_post_meta( $post_id, 'ymc_bg_color', $bg_color );
		}
        // Border color
		if( isset($_POST['ymc_border_color']) ) {
			$border_color = !empty( $_POST['ymc_border_color'] ) ? sanitize_hex_color( $_POST['ymc_border_color'] ) : Crossword::instance()->variables->border_color;
			update_post_meta( $post_id, 'ymc_border_color', $border_color );
		}
		// Text color
		if( isset($_POST['ymc_text_color']) ) {
			$text_color = !empty( $_POST['ymc_text_color'] ) ? sanitize_hex_color( $_POST['ymc_text_color'] ) : Crossword::instance()->variables->text_color;
			update_post_meta( $post_id, 'ymc_text_color', $text_color );
		}
		// Counter color
		if( isset($_POST['ymc_counter_color']) ) {
			$counter_color = !empty( $_POST['ymc_counter_color'] ) ? sanitize_hex_color( $_POST['ymc_counter_color'] ) : Crossword::instance()->variables->counter_color;
			update_post_meta( $post_id, 'ymc_counter_color', $counter_color );
		}
		// Question color
		if( isset($_POST['ymc_question_color']) ) {
			$question_color = !empty( $_POST['ymc_question_color'] ) ? sanitize_hex_color( $_POST['ymc_question_color'] ) : Crossword::instance()->variables->question_color;
			update_post_meta( $post_id, 'ymc_question_color', $question_color );
		}
		// Bg correct ansver
		if( isset($_POST['ymc_bg_correct_ansver']) ) {
			$bg_correct_ansver = !empty( $_POST['ymc_bg_correct_ansver'] ) ? sanitize_hex_color( $_POST['ymc_bg_correct_ansver'] ) : Crossword::instance()->variables->bg_correct_ansver;
			update_post_meta( $post_id, 'ymc_bg_correct_ansver', $bg_correct_ansver );
		}
		// Bg incorrect ansver
		if( isset($_POST['ymc_bg_incorrect_ansver']) ) {
			$bg_incorrect_ansver = !empty( $_POST['ymc_bg_incorrect_ansver'] ) ? sanitize_hex_color( $_POST['ymc_bg_incorrect_ansver'] ) : Crossword::instance()->variables->bg_incorrect_ansver;
			update_post_meta( $post_id, 'ymc_bg_incorrect_ansver', $bg_incorrect_ansver );
		}
		// Bg align question
		if( isset($_POST['ymc_align_question']) ) {
			$align_question = !empty( $_POST['ymc_align_question'] ) ? sanitize_text_field( $_POST['ymc_align_question'] ) : Crossword::instance()->variables->align_question;
			update_post_meta( $post_id, 'ymc_align_question', $align_question );
		}
		// Mute
		if( isset($_POST['ymc_mute']) ) {
			update_post_meta( $post_id, 'ymc_mute', sanitize_text_field( $_POST['ymc_mute'] ) );
		}
        //  Show/Hide Popup
		if( isset($_POST['ymc_popup']) ) {
            update_post_meta( $post_id, 'ymc_popup', sanitize_text_field( $_POST['ymc_popup'] ) );
		}
		// Header Popup
		if( isset($_POST['ymc_header_popup']) ) {
			$header_popup = !empty( $_POST['ymc_header_popup'] ) ? sanitize_text_field( $_POST['ymc_header_popup'] ) : Crossword::instance()->variables->header_popup;
			update_post_meta( $post_id, 'ymc_header_popup', $header_popup );
		}
		// Content popup
		if( isset($_POST['ymc_content_popup']) ) {
			$content_popup = !empty( $_POST['ymc_content_popup'] ) ? wp_kses_post( $_POST['ymc_content_popup'] ) : Crossword::instance()->variables->content_popup;
			update_post_meta( $post_id, 'ymc_content_popup', $content_popup );
		}
	}

	public function add_post_metabox() {

		add_meta_box( 'ymc_crossword_top_meta_box' , __('Settings Crossword','ymc-crossword'), [ $this,'top_meta_box' ], 'ymc_crossword', 'normal', 'core');

		add_meta_box( 'ymc_crossword_side_meta_box' , __('YMC Crossword','ymc-crossword'), [ $this,'side_meta_box' ], 'ymc_crossword', 'side', 'core');

	}

	public function top_meta_box() {

		$post_id = get_the_ID();

		$var_instance = Crossword::instance()->variables;
    ?>

        <div class="ymc-crossword-container">

            <div class="ymc-crossword-box ymc-crossword-box-shortcode">

                <div class="ymc-header">
                    <span class="icon-theme dashicons dashicons-shortcode"></span>
	                <?php esc_html_e( 'Shortcode', 'ymc-crossword' ); ?>
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </div>

                <div class="ymc-content ymc-content-shortcode">
                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Shortcode for Page / Post','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
                            <?php esc_html_e('Directly paste this shortcode in your page','ymc-crossword'); ?>
                        </div>
                        <input type="text" readonly value="[ymc-crossword id='<?php esc_attr_e($post_id); ?>']" onfocus="this.select()" class="ymc-input ymc-input-shortcode">
                    </div>
                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Shortcode for Page Template','ymc-crossword'); ?>
                            <span class="ymc-info">
                            <?php esc_html_e('Directly paste this shortcode in your page template','ymc-crossword'); ?>
                        </span>
                        </label>
		                <?php $sh_code = "&lt;?php echo do_shortcode('[ymc-crossword id=&quot;". esc_attr($post_id) ."&quot;]'); ?&gt;"; ?>
                        <input type="text" readonly value="<?php esc_attr_e($sh_code); ?>" onfocus="this.select()" class="ymc-input ymc-input-shortcode">
                    </div>
                </div>

            </div>

            <div class="ymc-crossword-box ymc-crossword-box-settings">

                <div class="ymc-header">
                    <span class="icon-theme dashicons dashicons-admin-settings"></span>
	                <?php esc_html_e( 'Settings', 'ymc-crossword' ); ?>
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </div>

                <div class="ymc-content ymc-content-settings">

                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Crossword Theme','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
			                <?php esc_html_e('Set theme crossword','ymc-crossword'); ?>
                        </div>
                        <input class="input-field ymc-input" type="text" name="ymc_theme_crossword" placeholder="Add crossword theme" value="<?php esc_attr_e($var_instance->get_theme_crossword($post_id)); ?>">
                    </div>

                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Background for crossword scene:','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
			                <?php esc_html_e('Set background for crossword scene','ymc-crossword'); ?>
                        </div>
                        <input class="input-field custom-color-crossword" type="text" name="ymc_bg_stage" value="<?php esc_attr_e($var_instance->get_bg_stage($post_id)); ?>">
                    </div>

                    <div class="ymc-form-group">
                        <label class="ymc-label">
				            <?php esc_html_e('Cell background color:','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
				            <?php esc_html_e('Set background for crossword cells','ymc-crossword'); ?>
                        </div>
                        <input class="input-field custom-color-crossword" type="text" name="ymc_bg_color" value="<?php esc_attr_e($var_instance->get_bg_color($post_id)); ?>">
                    </div>

                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Cell border color:','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
			                <?php esc_html_e('Set border color for crossword cells','ymc-crossword'); ?>
                        </div>
                        <input class="input-field custom-color-crossword" type="text" name="ymc_border_color" value="<?php esc_attr_e($var_instance->get_border_color($post_id)); ?>">
                    </div>

                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Text color:','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
			                <?php esc_html_e('Set text color for crossword cells','ymc-crossword'); ?>
                        </div>
                        <input class="input-field custom-color-crossword" type="text" name="ymc_text_color" value="<?php esc_attr_e($var_instance->get_text_color($post_id)); ?>">
                    </div>

                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Counter color:','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
			                <?php esc_html_e('Set counter color for crossword cells','ymc-crossword'); ?>
                        </div>
                        <input class="input-field custom-color-crossword" type="text" name="ymc_counter_color" value="<?php esc_attr_e($var_instance->get_counter_color($post_id)); ?>">
                    </div>

                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Question panel color:','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
			                <?php esc_html_e('Set question panel color for crossword puzzle','ymc-crossword'); ?>
                        </div>
                        <input class="input-field custom-color-crossword" type="text" name="ymc_question_color" value="<?php esc_attr_e($var_instance->get_question_color($post_id)); ?>">
                    </div>

                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Background CORRECT answers','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
			                <?php esc_html_e('Set background CORRECT answers for crossword puzzle','ymc-crossword'); ?>
                        </div>
                        <input class="input-field custom-color-crossword" type="text" name="ymc_bg_correct_ansver" value="<?php esc_attr_e($var_instance->get_bg_correct_ansver($post_id)); ?>">
                    </div>

                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Background INCORRECT answers','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
			                <?php esc_html_e('Set background INCORRECT answers for crossword puzzle','ymc-crossword'); ?>
                        </div>
                        <input class="input-field custom-color-crossword" type="text" name="ymc_bg_incorrect_ansver" value="<?php esc_attr_e($var_instance->get_bg_incorrect_ansver($post_id)); ?>">
                    </div>

                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Align crossword block','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
			                <?php esc_html_e('Set the location of the Crossword block','ymc-crossword'); ?>
                        </div>
                        <?php
                            $checkedLeft = '';
                            $checkedRight = '';
                            $checkedCenter = '';
                        ?>
                        <?php if( $var_instance->get_align_question($post_id) === 'align-left' ) : ?>
                            <?php $checkedLeft = 'checked'; ?>
                        <?php elseif( $var_instance->get_align_question($post_id) === 'align-right' ) : ?>
	                        <?php $checkedRight = 'checked'; ?>
                        <?php  else : ?>
	                        <?php $checkedCenter = 'checked'; ?>
                        <?php  endif; ?>
                        <label class="ymc-radio-btn"><input type="radio" name="ymc_align_question" <?php echo $checkedCenter; ?> value="align-center"><?php esc_html_e('Center','ymc-crossword'); ?></label>
                        <label class="ymc-radio-btn"><input type="radio" name="ymc_align_question" <?php echo $checkedLeft; ?> value="align-left"><?php esc_html_e('Left','ymc-crossword'); ?></label>
                        <label class="ymc-radio-btn"><input type="radio" name="ymc_align_question" <?php echo $checkedRight; ?> value="align-right"><?php esc_html_e('Right','ymc-crossword'); ?></label>
                    </div>

                    <div class="ymc-form-group">
                        <label class="ymc-label">
		                    <?php esc_html_e('Enable / Disable Mute','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
		                    <?php esc_html_e('Play sound on correct or incorrect answers','ymc-crossword'); ?>
                        </div>
	                    <?php $checkedMute = ''; ?>
	                    <?php  if( $var_instance->get_mute($post_id) === '1' ) : ?>
		                    <?php $checkedMute = 'checked'; ?>
	                    <?php  endif;  ?>
                        <input type="hidden" name="ymc_mute" value='0'>
                        <input type="checkbox" name="ymc_mute" <?php echo $checkedMute; ?> value="1">
                    </div>

                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Show / Hidden Popup','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
			                <?php esc_html_e('Show/hide the popup when the game is over','ymc-crossword'); ?>
                        </div>
	                    <?php $checkedPopup = ''; ?>
	                    <?php if( $var_instance->get_popup($post_id) === '1' ) : ?>
		                    <?php $checkedPopup = 'checked'; ?>
	                    <?php  endif;  ?>
                        <input type="hidden" name="ymc_popup" value='0'>
                        <input type="checkbox" name="ymc_popup" <?php echo $checkedPopup; ?> value="1">
                        <div class="clearfix"></div>
                    </div>

                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Header popup','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
			                <?php esc_html_e('Set header for popup','ymc-crossword'); ?>
                        </div>
                        <input class="input-field ymc-input" type="text" name="ymc_header_popup" placeholder="Add header" value="<?php esc_attr_e($var_instance->get_header_popup($post_id)); ?>">
                    </div>

                    <div class="ymc-form-group">
                        <label class="ymc-label">
			                <?php esc_html_e('Popup content','ymc-crossword'); ?>
                        </label>
                        <div class="ymc-info">
			                <?php esc_html_e('Add content inside the popup','ymc-crossword'); ?>
                        </div>
                        <?php
                            wp_editor( $var_instance->get_content_popup($post_id), 'ymc_content_popup', $settings = array(
                                'wpautop' => true,
                                'tinymce' => true,
                                'textarea_rows' => 10,
                                'textarea_name' => 'ymc_content_popup',
                            ));
                        ?>
                    </div>

                </div>

            </div>

            <div class="ymc-crossword-box ymc-crossword-box-clue-word">

                <div class="ymc-header">
                    <span class="icon-theme dashicons dashicons-welcome-write-blog"></span>
			        <?php esc_html_e( 'Questions & Answers', 'ymc-crossword' ); ?>
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </div>

                <div class="ymc-content ymc-content-clue-word">

                    <div class="ymc-form-group">

                        <label class="ymc-label">
					        <?php esc_html_e('Clues and Words','ymc-crossword'); ?>
                        </label>

                        <div class="ymc-info">
					        <?php esc_html_e('Add clues (questions) and words (answers) to crossword puzzle','ymc-crossword'); ?>
                        </div>

                        <div class="ymc-crossword-row">

					        <?php

					        $crossword = get_post_meta( $post_id, 'ymc_crossword', true );
					        $items = json_decode($crossword, true, 512, JSON_UNESCAPED_UNICODE);
					        $number = 1;

					        if ( $items ) :

						        foreach ($items as $item) : ?>

                                    <div class="ymc-crossword-item">

                                        <div class="ymc-counter-block"><span class="ymc-num"><?php esc_html_e($number); ?></span></div>

                                        <div class="ymc-crossword-block">

                                            <div class="ymc-crossword-inner">
                                                <label  class="ymc-label"><?php esc_html_e('Question','ymc-crossword'); ?></label>
                                                <input class="ymc-input ymc-input-clue" type="text" placeholder="Add Question"  name="ymc-crossword-clue[]" value="<?php esc_attr_e($item['clue']); ?>" required />
                                            </div>

                                            <div class="ymc-crossword-inner">
                                                <label  class="ymc-label"><?php esc_html_e('Answer','ymc-crossword'); ?></label>
                                                <input class="ymc-input ymc-input-word" type="text" placeholder="Add Answer"  name="ymc-crossword-word[]" value="<?php esc_attr_e($item['word']); ?>" required />
                                            </div>

                                        </div>

                                        <a class="ymc-delete-crossword-item" href="#" title="Delete item"></a>

                                    </div>

							        <?php $number++; ?>

						        <?php endforeach; ?>

					        <?php else : ?>

                                <div class="ymc-crossword-placeholder">
                                    <header class="ymc-text">
								        <?php esc_html_e('Add new item','ymc-crossword'); ?>
                                        <span class="dashicons dashicons-insert"></span>
                                    </header>
                                </div>

					        <?php endif; ?>

                            <div class="ymc-crossword-action-wrp">
                                <a href="#" class="ymc-add-crossword-btn">
							        <?php esc_html_e('Add new item','ymc-crossword'); ?>
                                </a>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    <?php

	}

	public function side_meta_box() { ?>
		<article>
            <img class="ymc-logo" src="<?php esc_attr_e(YMC_CROSSWORD_URL . 'YmcCrossword/assets/images/logo.png'); ?>">
			<?php
			 esc_html_e('YMC Crossword is a game that consists in guessing words according to definitions.
			 A crossword puzzle is a form of a square or a rectangular grid of squares. The goal of the game is to fill 
			 in the white squares with letters to form words or phrases by solving clues that lead to answers.','ymc-crossword');
			?>
			<hr/>
			<strong style="color: #000; font-weight: 700; line-height: 1.2; font-size: 16px; background: #098ab821; display: block; padding: 7px 5px;">
				Did you like or find our plugin helpful? To support the plugin, you can make a <a target="_blank" href="https://www.paypal.com/donate/?hosted_button_id=B2MHM5LM29UGW">Donation</a></strong>.
		</article>
	<?php }

}