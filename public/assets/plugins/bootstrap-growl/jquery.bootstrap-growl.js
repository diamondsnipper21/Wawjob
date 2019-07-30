(function() {
  var $;

  $ = jQuery;

  $.bootstrapGrowl = function(message, options) {
    var $alert, css, offsetAmount;

    options = $.extend({}, $.bootstrapGrowl.default_options, options);
    if (options.type == 'error') {
      options.type = 'danger';
    }

    $alert = $("<div>");
    $alert.attr("class", "bootstrap-growl alert");
    if (options.type) {
      $alert.addClass("alert-" + options.type);
    }

    if (options.allow_dismiss) {
      $alert.append("<a class=\"close\" data-dismiss=\"alert\" href=\"#\">&times;</a>");
    }

    var icons = {
      success: 'fa-check-circle',
      info: 'fa-exclamation-circle',
      danger: 'fa-times-circle',
      warning: 'fa-exclamation-circle'
    };

    var str_message = '';
    if (options.type && icons[options.type]) {
      str_message = '<span class="fa fa-lg ' + icons[options.type] + '"></span>';
    }
    str_message += message;
    $alert.append(str_message);

    if (options.top_offset) {
      options.offset = {
        from: "top",
        amount: options.top_offset
      };
    }

    /*
    * modified - Mar 6, 2016
    * Show alert to the topmost or to the leftmost if the maximum offsetAmount is out of boundary.
    */
    offsetAmount = options.offset.amount;
    lastOffsetAmount = 0;
    $(".bootstrap-growl").each(function() {
      lastOffsetAmount = parseInt($(this).css(options.offset.from)) + $(this).outerHeight() + options.stackup_spacing;

      return offsetAmount = Math.max(offsetAmount, lastOffsetAmount);
    });

    var maxLimit = 0;
    if (options.offset.from == "top") {
      maxLimit = $(window).height() - 30;
    } else if (options.offset.from == "left") {
      maxLimit = $(window).width() - 30;
    }

    if (offsetAmount > maxLimit) {
      if (lastOffsetAmount < maxLimit) {
        offsetAmount = lastOffsetAmount;
      } else {
        offsetAmount = options.offset.amount;
      }
    }
    /* Paul Z. - end */

    css = {
      "position": (options.ele === "body" ? "fixed" : "absolute"),
      "margin": 0,
      "z-index": "9999",
      "display": "none"
    };
    css[options.offset.from] = offsetAmount + "px";
    $alert.css(css);
    if (options.width !== "auto") {
      $alert.css("width", options.width + "px");
    }
    $(options.ele).append($alert);
    switch (options.align) {
      case "center":
        $alert.css({
          "left": "50%",
          "margin-left": "-" + ($alert.outerWidth() / 2) + "px"
        });
        break;
      case "left":
        $alert.css("left", "20px");
        break;
      default:
        $alert.css("right", "20px");
    }
    $alert.fadeIn();
    if (options.delay > 0) {
      $alert.delay(options.delay).fadeOut(function() {
        return $(this).alert("close");
      });
    }
    return $alert;
  };

  $.bootstrapGrowl.clear = function() {
    $(".bootstrap-growl").remove();
  };

  $.bootstrapGrowl.default_options = {
    ele: "body",
    type: "info",
    offset: {
      from: "top",
      amount: 20
    },
    align: "right",
    width: 200,
    delay: 4000,
    allow_dismiss: true,
    stackup_spacing: 10
  };

}).call(this);
