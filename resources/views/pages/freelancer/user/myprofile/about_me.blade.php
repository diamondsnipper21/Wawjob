<?php

use iJobDesk\Models\UserProfile;
use iJobDesk\Models\Category;
use iJobDesk\Models\Language;
use iJobDesk\Models\Skill;
use iJobDesk\Models\User;
use iJobDesk\Models\File;

?>
<script type="text/javascript">
	var AVATAR_WIDTH  	  = {{ User::AVATAR_WIDTH }};
	var AVATAR_HEIGHT 	  = {{ User::AVATAR_HEIGHT }};
</script>

<!-- Title -->
<div class="form-group">
 	<label class="col-sm-3 col-xs-4 control-label">{{ trans('common.title') }}<span class="required">&nbsp;&nbsp;*</span></label>
 	<div class="col-sm-9 col-xs-8">
 		<label class="control-value">{{ $user->profile->title }}</label>
		<input type="text" class="form-control maxlength-handler" id="profile_title" name="profile[title]" value="{{ $user->profile->title }}" maxlength="50" data-rule-required="1" />
	</div>
</div>

{{-- Photo --}}
<div class="form-group">
	<label class="col-sm-3 col-xs-4 control-label">{{ trans('common.portrait') }}</label>
 	<div class="col-sm-9 col-xs-8">
 		<label class="control-value-avatar"><img src="{{ avatar_url($user) }}" width="100" height="100" class="user-avatar img-circle" />
 			@if ($current_user->existAvatar())
 			<a href="{{ route('user.my_profile.remove_avatar') }}" title="{{ trans('common.delete') }}"><i class="hs-admin-trash"></i></a>
 			@endif
 		</label>
		<div class="clearfix"></div>

 		<div class="form-control border-0 pt-3 px-0">
			<div class="file-upload-container">
				<div class="fileinput fileinput-new" data-provides="fileinput">
					<span class="btn btn-success green btn-file">
						<span class="fileinput-new "><i class="icon-cloud-upload"></i>&nbsp;&nbsp;{{ trans('common.select') }}</span> 
						<span class="fileinput-exists">{{ trans('common.change') }}</span>
						
						<input type="file" id="avatar" class="form-control" name="attached_files" {!! render_file_validation_options(File::TYPE_USER_AVATAR) !!} />
						<input type="hidden" name="file_ids">
						<input type="hidden" name="file_type" value="{{ File::TYPE_USER_AVATAR }}" />
					</span>
					<a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a>&nbsp;&nbsp;&nbsp;
				</div>
				<button class="btn btn-danger btn-border btn-upload-cancel hide pull-right" type="button">{{ trans('common.cancel') }}</button>
			</div>
			<div class="temp-avatar avatar-upload-container"></div>
		</div>
	</div>

	<input type="hidden" name="x1" class="x1" />
	<input type="hidden" name="y1" class="y1" />
	<input type="hidden" name="width" class="w" />
	<input type="hidden" name="height" class="h" />
</div>

<!-- Hourly Rate -->
 <div class="form-group">
 	<label class="col-sm-3 col-xs-4 control-label">{{ trans('common.hourly_rate') }}<span class="required">&nbsp;&nbsp;*</span></label>
 	<div class="col-sm-3 col-xs-6">
 		<label class="control-value">${{ $user->profile->rate }} / {{ trans('common.hr') }}</label>
 		<div class="input-group">
 			<div class="input-group-addon">$</div>
 			<input type="text" class="form-control text-right" id="profile_rate" name="profile[rate]" value="{{ $user->profile->rate }}" data-rule-required="1" data-rule-number="1" data-rule-min="0.5" data-rule-max="999.99" />
 			<div class="input-group-addon">/{{ trans('common.hr') }}</div>
 		</div>
	</div>
</div>

<!-- Availability -->
<?php
	$user->profile->available = ($user->profile->desc != null)?$user->profile->available:3; // default value will be "Available more than 30 hrs / week"
?>
<div class="form-group">
 	<label class="col-sm-3 control-label">{{ trans('common.availability') }}</label>
 	<div class="col-sm-9">
 		<label class="control-value">{{ $user->profile->availabilityString() }}</label>
 		@foreach (UserProfile::availabilities() as $key => $label)
 		<div class="radio-box mt-2">
	        <label>
	         	<input type="radio" name="profile[available]" value="{{ $key }}" {{ $key == $user->profile->available?'checked':'' }} /> {{ $label }}
	        </label>
	    </div>
 		@endforeach
 	</div>
</div>

<!-- Visiblilty -->
<div class="form-group">
 	<label class="col-sm-3 control-label">{{ trans('common.visibility') }}</label>
 	<div class="col-sm-9">
 		<label class="control-value">{{ $user->profile->visibilityString() }}</label>
 		@foreach (UserProfile::visibilities() as $key => $label)
 		<div class="radio-box mt-2">
	        <label>
	         	<input type="radio" name="profile[share]" value="{{ $key }}" {{ $key == $user->profile->share?'checked':'' }} /> {{ $label }}
	        </label>
	    </div>
 		@endforeach
 	</div>
</div>

<!-- Overview -->
<div class="form-group pt-5">
 	<label class="col-sm-3 control-label">{{ trans('common.about') }}<span class="required">&nbsp;&nbsp;*</span></label>
 	<div class="col-sm-9">
 		<label class="control-value">{!! render_more_less_desc($user->profile->desc, 500) !!}</label>
		<textarea id="profile_desc" name="profile[desc]" class="form-control maxlength-handler" data-rule-required="1" maxlength="5000" minlength="100" rows="10">{{ $user->profile->desc }}</textarea>
	</div>
</div>

<!-- Skills -->
<div class="form-group pb-5">
	<label class="col-sm-3 control-label margin-top-3">{{ trans('common.skills') }}</label>
	<div class="col-sm-9">
		<label class="control-value">
		@foreach ($user->skills as $skill)
			<span class="rounded-item">{{ parse_multilang($skill->name) }}</span>
		@endforeach
		</label>
		<select id="profile_skills" name="profile[skills][]" class="form-select2-control select2-ajax" multiple data-placeholder="{{ trans('profile.select_skills') }}" data-url="{{ route('job.search_skills.ajax') }}" data-maximum-selection-length="10" data-sortable="1">
			@foreach ($user->skills as $skill)
            <option value="{{ $skill->id }}" selected>{{ $skill->name }}</option>
            @endforeach
        </select>
	</div>
</div>

<!-- English Level -->
<div class="form-group">
 	<label class="col-sm-3 control-label">{{ trans('profile.englishlevel') }}</label>
 	<div class="col-sm-9">
 		<label class="control-value">{{ $user->profile->englishLevelString() }}</label>
 		@foreach (Category::getEnLevels() as $id => $level)
 		<div class="radio-box mt-2">
	        <label> 
	         	<input type="radio" name="profile[en_level]" value="{{ $id }}" {{ $id == $user->profile->en_level?'checked':'' }} /> {{ parse_multilang($level['name']) }}
	        </label>
	    </div>
 		@endforeach
 	</div>
</div>

<!-- Languages -->
<div class="form-group">
 	<label class="col-sm-3 control-label margin-top-3">{{ trans('profile.languages') }}</label>
 	<div class="col-sm-9">
		<label class="control-value">
 		@foreach ($user->languages as $language)
		<span class="rounded-item">{{ $language->name }}</span>
		@endforeach
		</label>
		<select id="profile_languages" name="profile[languages][]" class="form-select2-control select2" multiple data-placeholder="{{ trans('profile.select_languages') }}" data-minimumResultsForSearch="10" data-maximum-selection-length="5">
			@foreach ($user->languages as $language)
			<option value="{{ $language->id }}" selected>{{ $language->name }}</option>
            @endforeach

			@foreach (Language::all() as $language)
				@if (!$user->languages->contains('id', $language->id))
            	<option value="{{ $language->id }}">{{ $language->name }}</option>
            	@endif
            @endforeach
		</select>
 	</div>
</div>