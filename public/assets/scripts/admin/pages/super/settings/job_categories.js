/**
 * @author KCG
 * @since July 30, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'alert', 'jstree', 'bs-modalmanager', 'bs-modal', 'ajax_datatable', 'jquery-form'], function (common) {

    var fn = {
        current_category: null,

        init: function() {
            this.initElements();

            this.modal.init();
            
            this.bindEvents();
            this.render();
        },

        initElements: function() {
            this.$container = $('#job_categories');
            this.$form      = $('form', this.$container);
        },

        bindEvents: function() {
            var self = this;

            $('.edit-link, .add-link').on('click', function() {
                var url = $(this).data('url');
                var id = $(this).data('id');
                var action = ($(this).hasClass('edit-link')?'edit':'add');
                
                if (id)
                    url += '/' + id;
                url += '?action=' + action;

                // $('body').modalmanager('loading');

                self.modal.open(url);

                return false;
            });

            // delete category
            $('.delete-link').alert({
                message: 'Are you sure to delete this category?',
                title: 'Confirmation',
                cancelButton: {
                    label: "Cancel",
                    className: 'btn-default',
                    callback: function() {
                    }
                },
                actionButton: {
                    label: "Delete",
                    className: 'blue',
                    callback: function(e, $this) {
                        $('input[name="_action"]').val('DELETE');
                        $('input[name="_id"]').val($this.data('id'));

                        self.$form.submit();
                    }
                }
            });

            var $self = this.$container;
            // re-ordering
            $('.order-link').alert({
                message: 'Are you sure to re-order?',
                title: 'Job Category',
                cancelButton: {
                    label: "No",
                    className: 'btn-default',
                    callback: function() {
                    }
                },
                actionButton: {
                    label: "Yes",
                    className: 'blue',
                    callback: function(e, $this) {
                        var url = $this.data('url');
                        data = {
                            'data': $('.tree').data('jstree').get_json()
                        };

                        $.ajaxDatatable.submitForm(url, data, function(html) {
                            var $html = $(html);
                            var $contents = $($.ajaxDatatable.selector($self), $html);

                            if ($contents.length == 0)
                                $self.html(html);
                            else
                                $self.html($contents.html());

                            self.init();
                        });
                    }
                }
            });            

            $('.tree').on('select_node.jstree', function(e, category) {
                console.log($(this).data());
                var id = category.node.id;
                var params = id.split('_');

                id = params[0];
                parent_id = params[1];

                $('.edit-link').attr('disabled', ROOT_ID == id);
                $('.add-link').attr('disabled', parent_id != 0);
                $('.delete-link').attr('disabled', ROOT_ID == id);

                $('.action-link').data('id', id);
            });
        },

        render: function() {
            var self = this;

            common.initModal();

            $('.tree').jstree({
                'plugins': ["wholerow", "types", "state", 'search', 'dnd'],
                'core': {
                    "themes" : {
                        "responsive": false
                    },
                    "check_callback" : true,
                    'data': jtree_categories
                },
                "types" : {
                    "default" : {
                        "icon" : "fa fa-folder icon-state-info icon-lg"
                    },
                    "file" : {
                        "icon" : "fa fa-file icon-state-info icon-lg"
                    }
                },
                "state" : { "key" : "job_categories" },
            });

            this.$container.ajaxDatatable({
                success: function(html) {
                    self.init();
                }
            });
        },

        modal: {
            init: function() {
                this.initElements();
                this.bindEvents();
                this.render();
            },

            initElements: function() {
                this.$container = $('#modal_job_category');
                this.$form = $('form', this.$container);
            },

            bindEvents: function() {
                var self = this;

                this.$container.on('show', function() {
                    self.init();    
                });
            },

            render: function() {
                var self = this;
                
                this.$form.validate();

                this.$container.ajaxDatatable({
                    success: function(html) {
                        self.initElements();
                        self.render();

                        $('.save-button', self.$container).attr('disabled', true);

                        window.setTimeout(function() {
                            self.close();
                            fn.$form.submit();
                        }, 2000);
                    }
                });

                Global.renderMaxlength();
            },

            open: function(url) {
                var $modal = this.$container;

                setTimeout(function(){
                    $modal.load(url, '', function() {
                        // Metronic.unblockUI();
                        $modal.modal();
                    });
                }, 1000);
            },

            close: function() {
                var $modal = this.$container;
                $modal.modal('hide');
            }
        }
    };

    return fn;
});
define.amd = amd;