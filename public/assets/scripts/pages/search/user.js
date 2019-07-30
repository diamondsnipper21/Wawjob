/**
* user.js
* so gwang
*/

define(['stars', 'select2'], function (stars) {

    var fn = {
        url: '',

        $form: null,
        $boxFilters: null,

        $keyword: null,
        $category: null,
        $title: null,
        $hourlyRates: null,
        $jobSuccesses: null,
        $feedbacks: null,
        $englishLevels: null,
        $languages: null,
        $hours: null,
        $activities: null,        

        init: function() {
            this.$form = $('#search_form');
            this.$boxFilters = $('.box-filters');

			this.$keyword = $('#keyword', fn.$form);
			this.$category = $('#category', fn.$form);
			this.$title = $('#title', fn.$form);
			
			this.$hourlyRates = $('[name="hr"]', fn.$form);
			this.$jobSuccesses = $('[name="js"]', fn.$form);
			this.$feedbacks = $('[name="f"]', fn.$form);			
			this.$englishLevels = $('[name="el"]', fn.$form);
			this.$languages = $('#languages', fn.$form);	
			this.$hours = $('[name="hb"]', fn.$form);			
			this.$activities = $('[name="a"]', fn.$form);

            this.bindEvents();

            this.initLanguages();

            this.render();
        },

		getUrlParams: function() {
			var q = fn.$keyword.val().trim();
			var c = fn.$category.val();
			var t = fn.$title.val().trim();
			var hr = $('[name="hr"]:checked', fn.$form).val();
			var js = $('[name="js"]:checked', fn.$form).val();
			var f = $('[name="f"]:checked', fn.$form).val();
			var el = $('[name="el"]:checked', fn.$form).val();			
			var hb = $('[name="hb"]:checked', fn.$form).val();
			var a = $('[name="a"]:checked', fn.$form).val();
			var l = fn.$languages.val();

			if ( q != '' ) {
				fn.addUrlParam({name:'q', value:q});
			}

			if ( c != '' ) {
				fn.addUrlParam({name:'c', value:c});
			}

			if ( t != '' ) {
				fn.addUrlParam({name:'t', value:t});
			}

			if ( hr != '' ) {
				fn.addUrlParam({name:'hr', value:hr});
			}

			if ( js != '' ) {
				fn.addUrlParam({name:'js', value:js});
			}

			if ( f != '' ) {
				fn.addUrlParam({name:'f', value:f});
			}

			if ( el != '' ) {
				fn.addUrlParam({name:'el', value:el});
			}

			if ( l != '' && l) {
				fn.addUrlParam({name:'ln', value:fn.$languages.val()});
			}

			if ( hb != '' ) {
				fn.addUrlParam({name:'hb', value:hb});
			}

			if ( a != '' ) {
				fn.addUrlParam({name:'a', value:a});
			}
		},

		makePrettyUrl: function() {
			fn.getUrlParams();

			if (fn.url == '')
				location.href = currentURL;
			else
			location.href = currentURL + '?' + fn.url;
			
			return false;
		},

		addUrlParam: function(param) {
			if ( fn.url != '' ) {
				fn.url += '&';
			}

			fn.url += param.name + '=' + param.value;

			return fn.url;
		},

        bindEvents: function() {
            var self = this;

            fn.$form.on('submit', fn.makePrettyUrl);

            fn.$category.on('change', fn.makePrettyUrl);
            fn.$title.on('change', fn.makePrettyUrl);
            fn.$hourlyRates.on('change', fn.makePrettyUrl);
            fn.$jobSuccesses.on('change', fn.makePrettyUrl);
            fn.$feedbacks.on('change', fn.makePrettyUrl);
            fn.$englishLevels.on('change', fn.makePrettyUrl);
            fn.$languages.on('change', fn.makePrettyUrl);
            fn.$hours.on('change', fn.makePrettyUrl);
            fn.$activities.on('change', fn.makePrettyUrl);

            window.onpopstate = function(e){
                if(e.state) {
                    var html        = e.state.html;
                    var $container  = $(e.state.content_id);
                    var page_title  = e.state.page_title;

                    $.ajaxPage.replaceHTML($container, html);
                    document.title = page_title;

                    self.init();
                }
            };
        },

        initLanguages: function() {
			if ( fn.$languages.val() ) {
				$.ajax({
				    type: 'GET',
				    blockUI: false,
				    url: fn.$languages.data('url') + '?id=' + fn.$languages.val()
				}).then(function (data) {
				    var option = new Option(data.name, data.id, true, true);
				    fn.$languages.append(option);
				});
			}
        },

        render: function() {
            var self = this;

            stars.init($('.score .stars'));

            Global.renderUniform();
            Global.renderSelect2();
            Global.renderTooltip();
            Global.renderGoToTop();
        },
    };

    return fn;
});
