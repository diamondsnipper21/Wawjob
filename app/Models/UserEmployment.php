<?php namespace iJobDesk\Models;


class UserEmployment extends Model {

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'user_employments';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;

    public static function getValidator() {
        $validator = [
            'profile.employment.company'       => 'required|max:100',
            'profile.employment.from_month'    => 'required|integer|between:1,12',
            'profile.employment.from_year'     => 'required|integer|between:1950,'.date('Y'),
            // 'profile.employment.to_month'      => 'required|integer|between:1,12',
            // 'profile.employment.to_year'       => 'required|integer|between:1950,'.date('Y'),
            'profile.employment.position'      => 'required|max:50',
            'profile.employment.desc'          => 'required|max:1000'
        ];

        return $validator;
    }
}