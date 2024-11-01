;
(function( $ ) {
    "use strict"

    $( document ).on('ready', function () {

        // Accordeon
        $('#ymc_crossword_top_meta_box').find('.ymc-crossword-box').each(function (){
            $(this).find('.ymc-header').on('click',function (e){
                e.preventDefault();
                $(this).find('.dashicons-arrow-down-alt2').toggleClass('open');
                $(this).next('.ymc-content').toggle();
                console.log($(this));
            });
        });

        // Add item
        $(document).on('click', '#ymc_crossword_top_meta_box .ymc-content-clue-word .ymc-add-crossword-btn', function (e) {
            e.preventDefault();

            $('#ymc_crossword_top_meta_box .ymc-content-clue-word .ymc-crossword-placeholder').remove();

            let num = $(this).parent().siblings().length;
            if( num > 0 ) {
                num++;
            }
            else {
                num = 1;
            }

            let data = `<div class="ymc-crossword-item">
                        <div class="ymc-counter-block"><span class="ymc-num">${num}</span></div> 
                        <div class="ymc-crossword-block">
                        <div class="ymc-crossword-inner">
                        <label class="ymc-label">Question</label>
                        <input class="ymc-input ymc-input-clue" type="text" placeholder="Add Question" name="ymc-crossword-clue[]" value="" required />
                        </div>
                        <div class="ymc-crossword-inner">
                        <label class="ymc-label">Answer</label>
                        <input class="ymc-input ymc-input-word" type="text" placeholder="Add Answer" name="ymc-crossword-word[]" value="" required />
                        </div>                         
                        </div>
                        <a class="ymc-delete-crossword-item" href="#" title="Delete item"></a>
                        </div>`;

            $(data).insertBefore(".ymc-crossword-action-wrp");

        });

        // Remove item
        $(document).on('click', '#ymc_crossword_top_meta_box .ymc-content-clue-word .ymc-delete-crossword-item', function (e) {
            e.preventDefault();
            $(this).parent().remove();
            let crosswordItems = $('#ymc_crossword_top_meta_box .ymc-content-clue-word .ymc-crossword-item');
            if( crosswordItems.length > 0 ) {
                crosswordItems.each(function (i, el) {
                    $(this).find('.ymc-counter-block .ymc-num').text(i+1);
                });
            }
            else {
                let data = `<div class="ymc-crossword-placeholder">
                            <header class="ymc-text">Add new item<span class="dashicons dashicons-insert"></span></header></div>`;
                $(data).insertBefore(".ymc-crossword-action-wrp");
            }
        });

        // Add Color Picker for all inputs
        $('.custom-color-crossword').wpColorPicker();


    });

    $( window ).on( "load", function() {});

}( jQuery ));