/**
 * list.js - Monitor / Overview
 */

define([], function () {	

	var fn = {
		$faqList: null,
    $catItems: null,
    $faqItem: null,
    catId: 0,
    loadUrl: null,

    toggleDetail: function () {
      fn.$faqList.find('.dd-item').removeClass("selected");
      var $li = $(this).parents('.dd-item');
      $li.addClass("selected");
      $li.find('.dd-item-content').collapse('toggle');
      fn.$faqList.find('.dd-item').not($li).find('.dd-item-content.in').collapse('hide');
    },
    selectCategory: function () {
      $li = $(this);
      $(".faq-sidebar-menu ul li").removeClass("active");
      $li.addClass("active");
      fn.catId = $li.find("a").attr("cat-id");
      fn.load();
    },

    load: function () {
      // make ajax post call to save the changes.
      $.post(fn.loadUrl, {cat_id: fn.catId}, function (json) {
        if (json.status == 'success') {
          var html = '';
          $.each(json.faqs, function(id, faq){
            html += '<li class="dd-item">';
            html += '  <div class="dd3-content">';
            html += '   <div class="dd-item-header">';
            html += '      <h5 class="title">' + faq.title + '</h5>';
            html += '    </div>';
            html += '    <div class="dd-item-body">';
            html += '      <div class="dd-item-content collapse"><p>' + faq.content + '</p></div>';
            html += '    </div>';
            html += '  </div>';
            html += '</li>';
          });

          fn.$faqList.html(html);

        }
      });
    },
		init: function () {
      fn.$faqList = $('.faq-list .dd-list');
      fn.loadUrl = data.loadUrl;
      fn.$catItems = $('.faq-sidebar-menu .menu-item');
      fn.catId = $(".faq-sidebar-menu ul .active a").attr("cat-id");

      //Load FAQ Event
      fn.$catItems.on('click', fn.selectCategory);
      //FAQ Item Click
      fn.$faqList.on('click', '.dd-item-header', fn.toggleDetail);
		}
	};

	return fn;
});