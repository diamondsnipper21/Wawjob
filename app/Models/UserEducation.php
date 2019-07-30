<?php namespace iJobDesk\Models;


class UserEducation extends Model {

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'user_educations';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;

    public static function getValidator() {
        $validator = [
            'profile.education.school'        => 'required|max:100',
            'profile.education.from'          => 'required|integer|between:1950,'.date('Y'),
            'profile.education.to'            => 'required|integer|between:1950,'.date('Y'),
            'profile.education.degree'        => 'required|max:100',
            'profile.education.desc'          => 'max:150'
        ];

        return $validator;
    }
}