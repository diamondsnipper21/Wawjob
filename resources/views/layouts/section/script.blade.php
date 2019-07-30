@if ( $current_user && $current_user->locale == 'ch' )
<style type="text/css">
body {
    font-family: Microsoft YaHei, tahoma, arial, Hiragino Sans GB, sans-serif;
}
</style>
@endif
<script>
    var siteUrl = '{{ url('/') }}';
    var currentURL = '{{ url()->current() }}';

    @if (isset($res_version))
    var res_version = '{{ $res_version }}';
    @endif
    
    @if (isset($page))
        var pageId = '{{ str_replace('.', '/', $page) }}';
    @endif
    var lang = '{{ App::getLocale() }}';

    var trans = {};
    trans.btn_ok     = '{{ trans('j_message.btn_ok') }}';
    trans.btn_cancel = '{{ trans('j_message.btn_cancel') }}';
    trans.btn_yes    = '{{ trans('j_message.btn_yes') }}';
    trans.btn_no     = '{{ trans('j_message.btn_no') }}';
    trans.loading    = '{{ trans('common.loading') }}';
    trans.find_jobs    = '{{ trans('search.find_jobs') }}';
    trans.find_freelancers    = '{{ trans('search.find_freelancers') }}';

    @if (isset($j_trans) && is_array($j_trans))
        @foreach ($j_trans as $var_name=>$value)
            @if (is_array($value))
                trans.{{ $var_name }} = {};
                @foreach ($value as $sub_var_name => $sub_value)
                    trans.{{ $var_name }}.{{ $sub_var_name }} = "{!! $sub_value  !!}";
                @endforeach
            @else
                trans.{{ $var_name }} = "{!! $value !!}"; 
            @endif
        @endforeach
    @endif

    var config_file_uploads = {
        'url':              '{{ route('file.upload') }}',
        'max_count':        '{{ Config::get('settings.uploads.max_count') }}',
    };

    var block_ui_default_html = '{!! render_block_ui_default_html() !!}';
</script>

<script src="{{ url('assets/plugins/jquery/dist/jquery.min.js') . '?v=' . $res_version }}"></script>
<script src="{{ url('assets/scripts/global.js') . '?v=' . $res_version }}"></script>

<script type="text/javascript">
    var require_common_paths = {
        // Bootstrap Plugins
        'bs-maxlength':             'plugins/bootstrap-maxlength/bootstrap-maxlength.min',
        'bs-toastr':                'plugins/bootstrap-toastr/toastr.min',
        'bs-tooltip':               'plugins/bootstrap/js/tooltip',
        'bs-hover-dropdown':        'plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min', //used

        // jQuery Plugins
        'jquery':                   'plugins/jquery/dist/jquery.min',
        'jquery-blockui':           'plugins/jquery.blockUI',
        'jquery-validation':        'plugins/jquery.validation/dist/jquery.validate',
        'jquery-uniform':           'plugins/jquery.uniform/jquery.uniform.min',

        // Others
        'tmpl':                     'plugins/tmpl/js/tmpl.min',
        'select2':                  'plugins/select2/select2',
        'moment':                   'plugins/moment.min',
        'fancybox':                 'plugins/fancybox/dist/jquery.fancybox.min',
        'chartjs':                  'plugins/chart.js/Chart.min',
        'cubeportfolio':            'plugins/unify/vendor/cubeportfolio-full/cubeportfolio/js/jquery.cubeportfolio',
        'inputmask':                'plugins/Inputmask/dist/min/jquery.inputmask.bundle.min',

        // Custom extensions
        'ajax_page':                'scripts/helper/ajax_page',
        'fileinput':                'scripts/helper/fileinput',
        'stars':                    'scripts/helper/stars',
    };

    var require_common_shims = {
        'jquery-uniform': {'deps': ['jquery']},
        'select2': {'deps': ['jquery']},
        'bs-hover-dropdown': {'deps': ['jquery', 'bootstrap']},
        'cubeportfolio': {'deps': ['jquery']},
        'inputmask': {'deps': ['jquery']},
    }
</script>