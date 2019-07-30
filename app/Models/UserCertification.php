<?php namespace iJobDesk\Models;


class UserCertification extends Model {
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'user_certifications';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;

    public static function getValidator() {
        $validator = [
            'profile.certification.title'       => 'required|max:200',
            'profile.certification.month'       => 'required|integer|between:1,12',
            'profile.certification.year'        => 'required|integer|between:1950,'.date('Y'),
            // 'profile.certification.url'         => 'url',
            'profile.certification.description' => 'required|max:255'
        ];

        return $validator;
    }
}
