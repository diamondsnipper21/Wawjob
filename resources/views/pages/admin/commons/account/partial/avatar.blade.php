<?php

use iJobDesk\Models\File;

?>
<h3 class="form-section">Avatar</h3>
<input type="hidden" name="_token" value="{{ csrf_token() }}" />
<input type="hidden" name="x1" id="x1" />
<input type="hidden" name="y1" id="y1" />
<input type="hidden" name="width" id="w" />
<input type="hidden" name="height" id="h" />

<input type="hidden" name="file_ids" />
<input type="hidden" name="file_type" value="{{ File::TYPE_USER_AVATAR }}" />

<div class="form-group">
    <div>
        <div class="image-container margin-bottom-10 user-avatar" id="user_avatar" >
            <img src="{{ avatar_url($user) }}" class="w-100" />
        </div>
        <div class="image-container margin-bottom-10 crop-preview" id="temp_avatar" style="display:none;"></div>
    </div>
    <div class="fileinput fileinput-new" data-provides="fileinput">
        <span class="btn green btn-file">
            <span class="fileinput-new">Select Image</span>
            <span class="fileinput-exists">Change</span>
            <input type="file" id="avatar" name="attached_files[]" />
        </span>
        <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput">Remove</a>
    </div>
</div>