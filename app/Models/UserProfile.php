<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use iJobDesk\Models\Category;

class UserProfile extends Model {

    use SoftDeletes;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'user_profiles';

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['user_id'];

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

    /* User Profile :: Availability */
    const AV_NOT_AVAILABLE = 0; # default value
    const AV_LESS_THAN_10 = 1;
    const AV_10_TO_30 = 2;
    const AV_MORE_THAN_30 = 3;

    public static $strAvailability;
    public static $strVisibility;

    function __construct() {
        parent::__construct();

        self::availabilities();
        self::visibilities();
    }

    public static function availabilities() {
        self::$strAvailability = [
            self::AV_NOT_AVAILABLE => trans('common.av_not_available'),
            self::AV_LESS_THAN_10  => trans('common.av_less_than_10'),
            self::AV_10_TO_30      => trans('common.av_10_to_30'),
            self::AV_MORE_THAN_30  => trans('common.av_more_than_30'),
        ];

        return self::$strAvailability;
    }

    public static function visibilities() {
        self::$strVisibility = [
            0 => trans('profile.sharing.public'),
            1 => trans('profile.sharing.protected'),
            2 => trans('profile.sharing.private')
        ];

        return self::$strVisibility;
    }

    public function toString() {
        return self::$strAvailability[$this->available];
    }

    public function availabilityString() {
        return self::$strAvailability[$this->available?$this->available:0];
    }

    public function visibilityString() {
        return self::$strVisibility[$this->share?$this->share:0];
    }

    public function englishLevelString() {
        $levels = Category::getEnLevels();
        $level_id = $this->en_level?$this->en_level:0;

        if (!array_key_exists($level_id, $levels))
            return '';
        
        $level = $levels[$this->en_level?$this->en_level:0];

        return parse_multilang($level['name']);
    }
}