/**
* stars.js
*
*/

define([], function () {

    var fn = {
        $container: null,

        init: function($container) {
            this.$container = $container;

            this.render();
        },

        render: function() {
            this.$container.each(function() {
                var html = '';
                var width = $(this).data('value');
                var show_marks = $(this).data('show-marks') != undefined && $(this).data('show-marks') == true;                

                if (show_marks) {
                    html += '<div class="star-marks">' + (width / 100 * 5).toFixed(2) + '</div>';
                    html += '<div class="star-marks-overlay">' + (width / 100 * 5).toFixed(2) + '</div>';
                }

                if (width * 1 == width) // width is number format
                    width += '%';

                html += '<div class="gray-stars">' +
                                '<i class="fa fa-star" aria-hidden="true"></i>' +
                                '<i class="fa fa-star" aria-hidden="true"></i>' +
                                '<i class="fa fa-star" aria-hidden="true"></i>' +
                                '<i class="fa fa-star" aria-hidden="true"></i>' +
                                '<i class="fa fa-star" aria-hidden="true"></i>' +
                                
                                '<div class="red-stars" style="width: ' + width + '">' +
                                    '<i class="fa fa-star" aria-hidden="true"></i>' +
                                    '<i class="fa fa-star" aria-hidden="true"></i>' +
                                    '<i class="fa fa-star" aria-hidden="true"></i>' +
                                    '<i class="fa fa-star" aria-hidden="true"></i>' +
                                    '<i class="fa fa-star" aria-hidden="true"></i>' +
                                '</div>' +
                            '</div>';

                $(this).html(html);

                $(this).css('overflow', 'hidden');

                if (show_marks) {
                    $('.gray-stars', $(this)).css('float', 'none').css('display', 'inline-block');
                    var position = $('.star-marks-overlay', $(this)).position();
                    $('.star-marks', $(this)).css('left', position.left).css('top', position.top);
                }
            });
        }
    };

    return fn;
});