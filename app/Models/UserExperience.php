<?php namespace iJobDesk\Models;


class UserExperience extends Model {

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'user_experiences';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;

    public static function getValidator() {
        $validator = [
            'profile.experience.title'        => 'required|max:100',
            'profile.experience.description'   => 'required|max:500'
        ];

        return $validator;
    }
}