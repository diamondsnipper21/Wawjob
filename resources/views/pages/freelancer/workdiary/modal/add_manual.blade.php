<!-- Add manual time -->
<div class="modal fade" id="addManualModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ trans('common.add_manual_time') }}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3 col-xs-4"><label>{{ trans('common.date') }}</label></div>
                    <div class="col-sm-9 col-xs-8">
                        <span class="date">{{ trans('common.weekdays_abbr.' . date_format(date_create($meta['wdate']), 'N')) }} {{ date_format(date_create($meta['wdate']), $format_date2) }}</span>
                    </div>  
                </div> 
                <div class="row">
                    <div class="col-sm-3 col-xs-4"><label>{{ trans('common.timezone') }}</label></div>
                    <div class="col-sm-9 col-xs-8">
                        <span class="timezone"></span>
                    </div>  
                </div>  
                <div class="row">
                    <div class="col-sm-3 col-xs-4">
                        <label>{{ trans('common.from_cap') }}</label> <span class="form-required"> *</span>
                    </div>
                    <div class="col-sm-9 col-xs-8">
                        <div class="pull-left w-15">
	                        <select id="startHour" name="starthour" class="form-control">
	                            @for ( $i = 0; $i < 24; $i++)
	                            <option value="{{$i}}">{{$i}}</option>
	                            @endfor
	                        </select>
	                    </div>
                        <span class="pull-left mt-2 ml-2">h</span>
                        <div class="pull-left w-20 ml-4">
	                        <select id="startMinute" name="startminute" class="form-control select2">
	                            @for ( $i = 0; $i < 6; $i++)
	                            @if ( $i == 0 )
	                            <option value="{{$i}}">00</option>
	                            @else
	                            <option value="{{$i*10}}">{{$i*10}}</option>
	                            @endif
	                            @endfor
	                        </select>
	                    </div>
                        <span class="pull-left mt-2 ml-2">m</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 col-xs-4">
                        <label>{{ trans('common.to_cap') }}</label> <span class="form-required"> *</span>
                    </div>
                    <div class="col-sm-9 col-xs-8">
                        <div class="pull-left w-15">
                            <select id="endHour" name="endhour" class="form-control">
                                @for ( $i = 0; $i < 24; $i++)
                                <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                        <span class="pull-left mt-2 ml-2">h</span>
                        <div class="pull-left w-20 ml-4">
	                        <select id="endMinute" name="endminute" class="form-control select2">
	                            @for ( $i = 0; $i < 6; $i++)
	                            @if ( $i == 0 )
	                            <option value="{{$i}}">00</option>
	                            @else
	                            <option value="{{$i*10}}">{{$i*10}}</option>
	                            @endif
	                            @endfor
	                        </select>
	                    </div>
                        <span class="pull-left mt-2 ml-2">m</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 col-xs-4">
                        <label>
                            {{ trans('common.memo') }}
                            <span class="form-required"> *</span>
                        </label>
                    </div>
                    <div class="col-sm-9 col-xs-8">
                        <textarea id="manualMemo" class="manualmemo form-control maxlength-handler resize-none" maxlength="{{ $config['freelancer']['workdiary']['memo'] }}"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertManual" class="btn btn-primary">{{ trans('common.apply') }}</button>
                <button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel') }}</button>
            </div>
        </div>
    </div>
</div>
