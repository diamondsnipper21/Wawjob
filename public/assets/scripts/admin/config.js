/**
 * config.js
 * This script will support the config for app.js.
 */

var require = {
  baseUrl: siteUrl + '/assets',
  urlArgs: 'v=' + res_version, 
  paths: jQuery.extend({}, require_common_paths, {
    // Components.
    'jquery-migrate': 'plugins/metronic/global/plugins/jquery-migrate.min', //used
    'jquery-ui': 'plugins/jquery-ui/jquery-ui.min',

    'bootstrap': 'plugins/metronic/global/plugins/bootstrap/js/bootstrap.min', //used
    
    'jquery-slimscroll': 'plugins/metronic/global/plugins/jquery-slimscroll/jquery.slimscroll.min', //used
    'jquery-cokie': 'plugins/metronic/global/plugins/jquery.cokie.min', //used
    'jquery-form':  'plugins/metronic/global/plugins/jquery.form/jquery.form',
    'bootstrap-switch': 'plugins/metronic/global/plugins/bootstrap-switch/js/bootstrap-switch.min', //used
    'daterangepicker': 'plugins/metronic/global/plugins/bootstrap-daterangepicker/daterangepicker', //used
    'amcharts': 'plugins/metronic/global/plugins/amcharts/amcharts/amcharts', //used
    'amcharts_serial': 'plugins/metronic/global/plugins/amcharts/amcharts/serial', //used
    'amcharts_pie': 'plugins/metronic/global/plugins/amcharts/amcharts/pie', //used
    'jquery_datatables': 'plugins/metronic/global/plugins/datatables/media/js/jquery.dataTables', //used
    'bootstrap_datatables': 'plugins/metronic/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap', //used
    'datatable': 'plugins/metronic/global/scripts/datatable', //used
    'bootbox': 'plugins/metronic/global/plugins/bootbox/bootbox.min', //used
    'bs-modalmanager': 'plugins/metronic/global/plugins/bootstrap-modal/js/bootstrap-modalmanager', //used
    'bs-modal': 'plugins/metronic/global/plugins/bootstrap-modal/js/bootstrap-modal', //used
    'bs-datepicker': 'plugins/metronic/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min',
    'bs-fileinput': 'plugins/metronic/global/plugins/bootstrap-fileinput/bootstrap-fileinput',
    // 'select2': 'plugins/metronic/global/plugins/select2/select2',
    
    'jstree': 'plugins/metronic/global/plugins/jstree/dist/jstree.min',
    'ckeditor': 'plugins/metronic/global/plugins/ckeditor/ckeditor',

    'bs-maxlength':  'plugins/bootstrap-maxlength/bootstrap-maxlength.min',
    'bs-toastr':     'plugins/bootstrap-toastr/toastr.min',
    'bs-tooltip': 'plugins/bootstrap/js/tooltip',

    'vmap': 'plugins/metronic/global/plugins/jqvmap/jqvmap/jquery.vmap',
    'vmap-world': 'plugins/metronic/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.world',    

    'metronic': 'plugins/metronic/global/scripts/metronic', //used
    'layout': 'plugins/metronic/admin/layout3/scripts/layout', //used
    'quick-sidebar': 'plugins/metronic/admin/layout2/scripts/quick-sidebar', //used
    'demo': 'plugins/metronic/admin/layout3/scripts/demo', //used
    
    'defines': 'scripts/helper/defines',  // global utility functions
    'common': 'scripts/admin/helper/common', //used
    'plugin': 'scripts/admin/helper/plugin', //used
    'notify': 'scripts/admin/helper/notify',
    'jcrop': 'plugins/jcrop/js/jquery.Jcrop.min',

    'redactor': 'plugins/redactor/redactor.min.js',

    // own libraries
    'ajax_datatable': 'scripts/admin/helper/ajax_datatable', //used
    'alert': 'scripts/admin/helper/alert', //used
    'reasonbox': 'scripts/admin/helper/reasonbox', //used

    'page_user_common': 'scripts/admin/pages/super/user/commons',

    // Front End
    'wjbuyer': 'scripts/helper/buyer/buyer',
    'cookie': 'plugins/jquery.cookie/jquery.cookie',
    'datepicker': 'plugins/bootstrap-datepicker/js/bootstrap-datepicker', //used
  }),

  shim: $.extend({}, require_common_shims, {
    'bootstrap': {'deps': ['jquery', 'jquery-ui']},
    'cookie': {'deps': ['jquery']},
    'defines': {'deps': ['jquery']},
    'footable': {'deps': ['jquery']},
    'jquery-form': {'deps': ['jquery']},
    'datepicker': {'deps': ['bootstrap', 'moment']},
    'datetimepicker': {'deps': ['jquery', 'moment']},
    'daterangepicker': {'deps': ['bootstrap', 'moment']},
    'jqueryslimscroll': {'deps': ['jquery']},
    'timepicker': {'deps': ['bootstrap', 'moment']},

    'uniform': {'deps': ['jquery']},

    // Metronic Plugins
    'jquery-migrate': {'deps': ['jquery']},
    'jquery-slimscroll': {'deps': ['jquery']},
    'bootstrap-switch': {'deps': ['bootstrap']},
    'metronic': {'deps': ['jquery', 'jquery-migrate', 'bootstrap', 'bs-hover-dropdown', 'jquery-slimscroll', 
    'jquery-blockui', 'jquery-cokie', 'jquery-uniform', 'bootstrap-switch']},
    'layout': {'deps': ['metronic']},
    'quick-sidebar': {'deps': ['metronic']},
    'demo': {'deps': ['metronic']},
    'amcharts_serial': {'deps': ['amcharts']},
    'amcharts_pie': {'deps': ['amcharts']},
    'jquery-datatables': {'deps': ['jquery']},
    'bootstrap-datatables': {'deps': ['bootstrap', 'jquery-datatables']},
    'datatable': {'deps': ['bootstrap-datatables']},
    'bs-datepicker': {'deps': ['moment']},

    'ajax_datatable': {'deps': ['jquery-form']},
    'ajax_page': {'deps': ['jquery-form']},
    'alert': {'deps': ['bs-modalmanager', 'bs-modal']},
    'reasonbox': {'deps': ['bs-modalmanager', 'bs-modal', 'bs-maxlength', 'jquery-validation']},
    'fileinput': {'deps': ['jquery-form', 'bs-tooltip']},

    'vmap-world': {'deps': ['vmap']},

    'wjbuyer': {'deps': ['bootbox']},
  })
};

var config = {
  noScriptPages: ['admin/dashboard', 'admin/userlist'],
  minDate: '01/01/2012'
};