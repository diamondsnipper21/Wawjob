/**
 * contract/feedback.js
 *
 * @author Ro Un Nam
 * @since Jun 07, 2017
 */
$.fn.review = function () {

    var options = {};
    options = $.extend({}, $.fn.review.defaults);
    
    var makeCapFirstLetter = function (string) {
        return string.charAt(0).toUpperCase() + string.substr(1);
    };
    
    return this.each(function () {
        var item = $(this);
        
        var itemReviewData = function (key, value) {
            arguments[0] = 'review' + makeCapFirstLetter(key);
            return item.data.apply(item, arguments); 
        };

        if (!itemReviewData('init')) {
            //get our values, either from the data-* html5 attribute or from the options.
            itemReviewData('iconcount', isNaN(itemReviewData('iconcount')) ? options.iconcount : itemReviewData('iconcount'));
            itemReviewData('iconstep', itemReviewData('iconstep') || options.iconstep);
            itemReviewData('iconwidth', itemReviewData('iconwidth') || options.iconwidth);
            itemReviewData('iconheight', itemReviewData('iconheight') || options.iconheight);
            itemReviewData('iconvalue', itemReviewData('iconvalue') || options.iconvalue);
            itemReviewData('init',item.data());
        }

        var range = item.find('.review-default, .review-selected, .review-hover');
        item.find('.review-default').height(parseInt(itemReviewData('iconheight')) + parseInt(itemReviewData('iconheight'))*0.5);
        item.find('.review-default').width((parseInt(itemReviewData('iconwidth'))/2)*(itemReviewData('iconcount')));
        item.find('.review-hover').width(0);
        item.find('.review-selected').width(0);

        var getReviewWidthSize = function (element, event) {
            
            var elementPosX = (event.changedTouches) ? event.changedTouches[0].pageX : event.pageX;
            var offsetx = elementPosX - $(element).offset().left;
            if (offsetx > range.width()) offsetx = range.width();
            if (offsetx < 0) offsetx = 0;

            var defaultIconWidth = (parseFloat(itemReviewData('iconwidth')))*parseFloat(itemReviewData('iconstep')); 
            var tempCount = offsetx / defaultIconWidth;
            var selIconCount =  Math.ceil(tempCount);
            var hWidth = selIconCount*(parseInt(itemReviewData('iconwidth')))*parseFloat(itemReviewData('iconstep'));

            return hWidth;
        };

        var setReviewHover = function (width) {
            var h = item.find('.review-hover');
            if (h.data('width') != width) {
                item.find('.review-selected').hide();

                if ( width < parseInt(itemReviewData('iconwidth')) ) {
                    width = parseInt(itemReviewData('iconwidth'));
                }

                h.width(width).show().data('width', width);
                item.find('.review-selected').data('width',width);
            }
            var iconvalue = h.width() / (parseInt(itemReviewData('iconwidth'))*parseFloat(itemReviewData('iconstep')));
            item.parent().next().find('span').text((iconvalue/2).toFixed(1)); 
            item.parent().next().show();
        };

        var setReviewClick = function (width) {
            if ( width < parseInt(itemReviewData('iconwidth')) ) {
                width = parseInt(itemReviewData('iconwidth'));
            }
            var h = item.find('.review-selected');
            item.find('.review-hover').hide();
            h.width(width).show().data('width', width);            
        };

        item.mousemove(function (e) {
            var w = getReviewWidthSize(this, e);
            setReviewHover(w);
        });

        item.mousedown(function (e) {
            var w = getReviewWidthSize(this, e);
            setReviewClick(w);
        });

        item.mouseleave(function (e) {
            item.find('.review-hover').hide().width(0).data('width', '');
            item.find('.review-selected').show();
            item.parent().next().find('span').text(item.find('#review').val());
        });

        item.mouseup(function (e) {
            item.find('.review-hover').hide();
            item.find('.review-selected').width(item.find('.review-selected').data('width'));
            var iconvalue = item.find('.review-selected').width() / (parseInt(itemReviewData('iconwidth'))*parseFloat(itemReviewData('iconstep')));
            item.find('#review').val((iconvalue/2).toFixed(1));
            item.parent().next().find('span').text((iconvalue/2).toFixed(1));
            item.parent().next().show();
            itemReviewData('iconvalue',iconvalue);
            //range.blur();
        });

    });
},

define([], function () {

	var fn = {

		$form: null,
        FEEDBACK_LENGTH: 5000,

		init: function () {

			this.$form = $('#form_feedback');
			
			this.validate();

			fn.insert();

			$.fn.review.defaults = {iconvalue:0, iconcount: 10, iconstep: 0.5, iconwidth: 21, iconheight: 20};
            $('.stars').review();

            Global.renderUniform();
            Global.renderSelect2();
            Global.renderMaxlength();
		},

		insert: function() {
			var count = 5;
			for (var i = 0; i < count; i++) {
				$('.review-default').append('<i class="fa fa-star" aria-hidden="true"></i>').css('color','#CCCCCC');
				$('.review-selected').append('<i class="fa fa-star" aria-hidden="true"></i>').css('color','#ffc501');
				$('.review-hover').append('<i class="fa fa-star" aria-hidden="true"></i>').css('color','#ffc501');
			};
		},

		validate: function() {
			this.$form.validate();
		}
  	}

	return fn;
});